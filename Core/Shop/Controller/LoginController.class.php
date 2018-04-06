<?php
namespace Shop\Controller;
use Think\Controller;
use Plugins\Huanxin\Easemob;
use Plugins\Point\Point;
use Plugins\Money\Money;

// 登录控制器
class LoginController extends BaseController {
    /**
     * [login 用户登录]
     * @author StanleyYuen <[350204080@qq.com]>
     * @modify kofu <[418382595@qq.com]>
     */
    public function login() {
        if ( IS_POST ) {
            // 记住密码
            $rememberPassword = I('post.rememberPassword');
            if ( $rememberPassword == '1' ) {
                $nextWeekTime = 3600 * 24 * 7;
                session_cache_expire($nextWeekTime / 60);
                session_set_cookie_params($nextWeekTime);
            }
            session_start();

            $userModel = M('user');
            $account   = I('post.account', '');
            $password  = I('post.password', '');

            // 采用系统加密
            $password = encrypt($password);
            if ( isPhone($account) ) {
                $user = $userModel->where(array('phone'=>$account, 'password'=>$password, 'method'=> '1'))->find();
            } elseif ( isEmail($account) ) {
                $user = $userModel->where(array('email'=>$account, 'password'=>$password, 'method'=> '0'))->find();
            }
            if ( empty($user) ) {
                $user = $userModel->where(array('username'=>$account, 'password'=>$password))->find();
            }
            
            if ( !empty($user) ) {
                $file = "/tmp/sess_{$user['session_id']}";
                // 删除编辑用户session文件
                if ( file_exists($file) && $user['session_id'] != session_id() ) {
                    unlink($file);
                }
                $saveData = array(
                    'id'=> $user['id'],
                    'session_id' => session_id(),
                    'last_login_time'=> time()
                );
                $userModel->save($saveData);

                session('userId', $user['id']);
                session('userInfo', $user);
                session('language', $user['language']);
                session('is_temp', 0);
                cookie('think_language', $user['language']);

                if ( $rememberPasswors == '1' ) {
                    session('rememberPassword', 1);
                }

                // 判断店家登录
                if ( $user['status'] == '1' ) {
                    $agent = M('agent');
                    $where['user_id'] = $user['id'];
                    $agentInfo = $agent->where($where)->field('`id`, `agent_name`, `logo`, `status`')->find();
                    if ( empty($agentInfo) ) {
                        exit(statusCode(array('userId'=> $user['id']), 400025));
                    }
                    if ( $agentInfo['status'] == '1' ) {
                        $userInfo = session('userInfo');
                        $userInfo['nickname'] = $agentInfo['agent_name'];
                        $userInfo['headimgurl'] = empty($agentInfo['logo']) ? '/Static/Public/Wechat/images/customer_position.png' : $agentInfo['logo'];
                        session('userInfo', $userInfo);
                        session('agentId', $agentInfo['id']);
                    } else {
                        exit(statusCode(array(), 400021));
                    }
                }

                exit(statusCode(array('userId'=> $user['id']), 200000, 'OK.'));
            } else {
                if ( isPhone($account) === true ) {
                    $where['phone'] = $account;
                } elseif ( isEmail($account) === true ) {
                    $where['email'] = $account;
                } else {
                    $where['username'] = $account;
                }
                if ( $userModel->where($where)->count() <= 0 ) {
                    exit(statusCode(array(), 400022));
                } else {
                    exit(statusCode(array(), 400023));
                }
            }
        } else {
            $this->display();
        }
    }

    /**
     * [logout 用户退出]
     * @author StanleyYuen <[350204080@qq.com]>
     */
    public function logout() {
        session('userInfo', NULL);
        session('userId', NULL);
        session('is_temp', NULL);
        session('language', NULL);
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie( session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        if (IS_POST) {
            exit(statusCode(array()));
        } else {
            header('Location:' . U('Login/login'));
            // $this->error('非法访问！');
        }
    }

    /**
     * [reg 注册会员]
     * @author kofu <[418382595@qq.com]>
     */
    public function register() {
        if (IS_POST) {
            $user = D('User');
            $post = I('post.');
            $phone = $post['phone'];
            $email = $post['email'];
            $referrer = $post['referrer'];
            $verify = $post['verify'];
            if ( !empty($phone) ) {
                // $data = $user->create($post, 6);
                $data = $user->create($post, 9);
            }
            if ( !empty($email) ) {
                $data = $user->create($post, 7);
            }
            if ( empty($data) ) {
                exit(statusCode(array(), 400000, $user->getError()));
            }
            
            $result = true;
            $user->startTrans();
            // 推荐人获得积分校验
            if ( !empty($referrer) ) {
                if ( isPhone($referrer) ) {
                    $referrerResult = $user->where(array('phone'=> $referrer, 'method'=> '1'))->find();
                    if ( empty($referrerResult) ) {
                        $referrerResult = $user->where(array('phone'=> $referrer, 'method'=> '0'))->find();
                    }
                } elseif ( isEmail($referrer) ) {
                    $referrerResult = $user->where(array('email'=> $referrer, 'method'=> '0'))->find();
                    if ( empty($referrerResult) ) {
                        $referrerResult = $user->where(array('email'=> $referrer, 'method'=> '1'))->find();
                    }
                }
                if ( empty($referrerResult) ) {
                    $referrerResult = $user->where(array('username'=> $referrer))->find();
                }
                if ( empty($referrerResult) ) {
                    exit(statusCode(array(), 400024));
                }
            }

            // $data['nickname'] = ( !empty($phone) ) ? $data['phone'] : $data['email'];
            // $data['username'] = ( !empty($phone) ) ? $data['phone'] : $data['email'];
            $data['nickname'] = ( !empty($email) ) ? $data['email'] : $data['phone'];
            $data['username'] = ( !empty($email) ) ? $data['email'] : $data['phone'];
            $data['password'] = encrypt($data['password']);
            $data['headimgurl'] = "/Static/Public/Wechat/images/customer_position.png";
            // 校验验证码
            $checkCode = $this->checkCode($data['nickname'], 0, $verify);
            if ( $checkCode['status'] == '400000' ) {
                $result = false;
                exit(statusCode(array(), 400000, $checkCode['message']));
            }

            if ( $userId = $user->add($data) ) {
                // 注册环信
                $this->imreg($userId, $data['nickname']);

                $userInfo = $user->find($userId);
                session('userInfo', $userInfo);
                session('userId', $userId);
                session('is_temp', 0);
            } else {
                $result = false;
                exit(statusCode(array(), 100002));
            }

            // 推荐人获得积分
            if ( !empty($referrer) ) {
                $point = new Point();
                $referrerPoint = C('referrerPoint');
                $referrerPoint = !empty($referrerPoint) ? $referrerPoint : 1;
                $parameter = array(
                    'from_id' => $userId,
                    'to_id' => $referrerResult['id'],
                    'num' => $referrerPoint,
                    'type' => 1,
                    'event_type' => 0,
                    'description' => "Referer+{$referrerPoint}point(s)",
                );
                if ( $point->point($parameter) === false ) {
                    $result = false;
                    exit(statusCode(array(), 100002));
                }
            }

            if ( $data['status'] == '1' ) {
                $money = new Money();
                $registerGetMoney = C('registerGetMoney');
                $parameter = array(
                    'from_id'       => '0',
                    'to_id'         => $userId,
                    'eventType'     => '9',
                    'type'          => '0',
                    'money'         => $registerGetMoney,
                    'description'   => "Register +RM{$registerGetMoney}",
                );
                if ( $money->money($parameter) === false ) {
                    $result = false;
                }
            }

            if ( $result === true ) {
                $user->commit();
                $returnData['userId'] = $userId;
                exit(statusCode($returnData));
            } else {
                $user->rollback();
            }
        } else {
            $article = M('article')->where(array('sign'=> 'agreement'))->getField('`content`');
            $this->assign('article', $article);
            $this->display();
        }
    }

    /**
     * [checkCode 检验验证码]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    private function checkCode($account, $type, $verify) {
        $verifyCode = M('user_verify_code');
        $where['type'] = $type;
        if ( isPhone($account) === true ) {
            $where['phone'] = $account;
        } elseif ( isEmail($account) === true ) {
            $where['email'] = $account;
        }

        $result = array(
            'status'    => '200000',
            'message'   => ''
        );
        // 最新一条有效
        $userVerify = $verifyCode->where($where)->order('add_time DESC')->limit(1)->field('id,verify,is_abate,add_time')->select();    
        //判断最新一条验证码是否一致
        if (!empty($userVerify) && $userVerify[0]['verify'] == $verify) {
            //验证码有效时间 
            $endTime    = time() - C('VERIFY_CODE_EFFECTIVE_TIME');  
            //防止二次使用该验证码
            if ($userVerify[0]['is_abate'] == 0 && $userVerify[0]['add_time'] > $endTime) {
                $saveData = array(
                    'id' => $userVerify[0]['id'],
                    'is_abate' => '1'
                );
                // 设置为失效
                $verifyCode->save($saveData);  
            } else {
                $result = array(
                    'status'    => '400000',
                    'message'   => L('_PC_LOGIN_REACQUIRE_CODE_')
                );
            }
        } else {
            $result = array(
                'status'    => '400000',
                'message'   => L('_PC_LOGIN_CODE_ERROR_')
            );
        }
        return $result;
    }

    /**
     * [forgetPassword 忘记密码]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function forgetPassword() {
        if (IS_POST) {
            $user = D('User');
            $post = I('post.');
            $data = $user->create($post, 4);
            if ( empty($data) ) {
                exit(statusCode(array(), 400000, $user->getError()));
            }

            $verify = $post['verify'];
            $account = $post['account'];
            // 校验验证码
            $checkCode = $this->checkCode($account, 1, $verify);
            if ( $checkCode['status'] == '400000' ) {
                exit(statusCode(array(), 400000, $checkCode['message']));
            }

            if ( isPhone($account) === true ) {
                $where['phone'] = $account;
            } elseif ( isEmail($account) === true ) {
                $where['email'] = $account;
            }

            $data['password'] = encrypt($data['password']);
            if ( $user->where($where)->save($data) !== false ) {
                exit(statusCode());
            } else {
                exit(statusCode(array(), 100002));
            }
        } else {
            $this->display();
        }
    }

    /**
     * [resetPassword 修改密码]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c) 2016 Xcrozz (http://www.xcrozz.com)
     */
    public function resetPassword() {
        if (IS_POST) {
            $user = D('User');
            $post = I('post.');
            if ( !empty($post['method']) && $post['method'] == 'app' ) {
                $userId = session('userId');
                if ( !empty($userId) ) {
                    $currentPassword = $user->where(array('id'=> $userId))->getField('password');
                    if ( $currentPassword != encrypt($post['old_password']) ) {
                        exit(statusCode(array(), 100002));
                    }
                    $data['password'] = encrypt($post['password']);
                    if ( $user->where(array('id'=> $userId))->save($data) !== false ) {
                        exit(statusCode());
                    } else {
                        exit(statusCode(array(), 100002));
                    }
                } else {
                    exit(statusCode(array(), 100002));
                }
            } else {
                $data = $user->create($post, 5);
                if ( empty($data) ) {
                    exit(statusCode(array(), 400000, $user->getError()));
                }

                $verify = $post['verify'];
                $account = $post['account'];
                // 校验验证码
                $checkCode = $this->checkCode($account, 2, $verify);
                if ( $checkCode['status'] == '400000' ) {
                    exit(statusCode(array(), 400000, $checkCode['message']));
                }

                if ( isPhone($account) === true ) {
                    $where['phone'] = $account;
                } elseif ( isEmail($account) === true ) {
                    $where['email'] = $account;
                }

                $data['password'] = encrypt($data['password']);
                if ( $user->where($where)->save($data) !== false ) {
                    exit(statusCode());
                } else {
                    exit(statusCode(array(), 100002));
                }
            }

        } else {
            $this->display();
        }
    }

    /**
     * [ajaxLogin ajax获取登录信息]
     * @author StanleyYuen <[350204080@qq.com]>
     */
    public function ajaxLogin() {
        $userInfo   = session('userInfo');
        $checkIn    = session('checkIn');
        $loginResult = array(
            'userId' =>  $userInfo['id'],
            'userNickname' =>  $userInfo['nickname'],
            'userImage' =>  $userInfo['headimgurl']
        );
        $is_temp = session('is_temp');
        if ( empty($loginResult['userId']) || $is_temp == '1' ) {
            exit(statusCode(array('isLogin'=> 0)));
        } else {
            exit(statusCode(array('isLogin'=>1,'checkIn'=>$checkIn,'result'=>$loginResult)));
        }
    }

    /**
     * [getVerifyCode 获取验证码]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function getVerifyCode() {
        if( IS_POST ) {
            $account = I('post.account', '');
            if ( isPhone($account) === true ) {
                $result = $this->phoneCode();
            } elseif ( isEmail($account) === true ) {
                $result = $this->emailCode();
            } else {
                $result = array(
                    'status'    => '400000',
                    'message'   => L('_PC_LOGIN_PHONE_EMAIL_INCORRECT_')
                );
            }
            exit(statusCode(array('code'=> $result['code']), $result['status'], $result['message']));
        } else {
            $this->error('非法访问！');
        }
    }

    /**
     * [phoneCode 手机验证码]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    private function phoneCode() {
        $post = I('post.');
        $user = M('user');
        $userVerifyCode = D('UserVerifyCode');
        $data = $userVerifyCode->create($post, 4);
        $userId = session('userId');
        $userId = empty($userId) ? 0 : $userId;
        $result = array(
            'status'    => '200000',
            'message'   => ''
        );

        $type   = $post['type']; //验证码类型
        $phone  = $post['account'];
        switch ( $type ) {
            case '0':
                if ( $user->where(array('phone'=> $phone))->count() > 0 ){
                    $result['status']   = '400000';
                    $result['message']  = L('_PC_LOGIN_PHONE_ALREADY_REGISTERED_');
                    return $result;
                }
                break;
            case '1':
                if ( $user->where(array('phone'=> $phone))->count() <= 0 ){
                    $result['status']   = '400000';
                    $result['message']  = L('_PC_LOGIN_NO_PHONE_NUMBER_');
                    return $result;
                }
                break;
        }
        $endTime    = time() - C('VERIFY_CODE_INTERVAL_TIME');
        $where      = array(
            'phone'     => $phone,
            'add_time'  => array('gt', $endTime),
            'type'      => $type,
        );
        $verifyCount = $userVerifyCode->where($where)->count();

        if( $verifyCount == 0 ) {
            $sort_date = date('Ymd');
            $verifyDateCount = $userVerifyCode->where(array('user_id'=> $userId, 'phone'=> $phone, 'sort_date'=> $sort_date))->count();

            //验证码次数每天上限
            if ($verifyDateCount <= C('VERIFY_DATE_COUNT') ) {
                $minute = C('VERIFY_CODE_EFFECTIVE_TIME') / 60;
                $verify_code = random_string('numeric', 6);
                $result['result'] = '0';
                if( !empty($result['result']) ) {
                    $result['status']   = '400000';
                    $result['message']  = $result['errmsg'];
                    return $result;
                } else {
                    $data['phone'] = $phone;
                    $addData = array(
                        'user_id'   => $userId,
                        'verify'    => $verify_code,
                        'type'      => $type,
                        'sort_date' => $sort_date,
                    );
                    $data = array_merge($data, $addData);
                    $userVerifyCode->add($data);
                    $result['code'] = $verify_code;
                }
            } else {
                $result['status']   = '400000';
                $result['message']  = L('_PC_LOGIN_VERIFICATION_LIMITED_');
            }
        } else {
            $result['status']   = '400000';
            $result['message']  = L('_PC_LOGIN_VERIFICATION_INTERVAL_TIME_', array('intervalTime'=> C('VERIFY_CODE_INTERVAL_TIME')));
        }
        return $result;
    }

    /**
     * [emailCode 邮箱验证码]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    private function emailCode() {
        $post = I('post.');
        $user = M('user');
        $userVerifyCode = D('UserVerifyCode');
        $data = $userVerifyCode->create($post, 5);
        $userId = session('userId');
        $userId = empty($userId) ? 0 : $userId;
        $result = array(
            'status'    => '200000',
            'message'   => ''
        );

        $type   = $post['type']; //验证码类型
        $email  = $post['account'];
        switch ( $type ) {
            case '0':
                if ( $user->where(array('email'=> $email))->count() > 0 ){
                    $result['status']   = '400000';
                    $result['message']  = L('_PC_LOGIN_MAILBOX_EXISTS_');
                    return $result;
                }
                break;
            case '1':
                if ( $user->where(array('email'=> $email))->count() <= 0 ){
                    $result['status']   = '400000';
                    $result['message']  = L('_PC_LOGIN_NO_MAILBOX_');
                    return $result;
                }
                break;
        }
        $endTime    = time() - C('VERIFY_CODE_INTERVAL_TIME');
        $where      = array(
            'email'     => $email,
            'add_time'  => array('gt', $endTime),
            'type'      => $type,
        );
        $verifyCount = $userVerifyCode->where($where)->count();

        if( $verifyCount == 0 ) {
            $sort_date = date('Ymd');
            $verifyDateCount = $userVerifyCode->where(array('user_id'=> $userId, 'email'=> $email, 'sort_date'=> $sort_date))->count();

            //验证码次数每天上限
            if ( $verifyDateCount <= C('VERIFY_DATE_COUNT') ) {
                $minute = C('VERIFY_CODE_EFFECTIVE_TIME') / 60;
                $verify_code = random_string('numeric', 6);

                $content = <<<EOT
                Dear {$email}，


                We received a request to verify your registration on Wedo App on the account of {$email} through your email address.

                The Wedo Verification Code is <b>{$verify_code}</b>

                IF you did not request this code, it is possible that someone else is trying to access Wedo App using your Email address. Do no forward or give this code to anyone.


                We’ll keep DIRECTING you to our latest offers, promotions and great deals!

                More Merchants are marching their way with their products to Wedo App!

                Let us ease your shopping method by directing you to your nearby store!


                From Wedo Team
                Wedo Business Connection Sdn Bhd
                Contact: 04-828 6601
                Facebook: wedo.my
                Website: wedo.my
EOT;
                $returnData = sendMail($email, 'Your verification code.', $content);
                // $returnData['result'] = '0';
                if( !empty($returnData['result']) ) {
                    $result['status']   = '400000';
                    $result['message']  = $returnData['errmsg'];
                    return $result;
                } else {
                    $data['email'] = $email;
                    $addData = array(
                        'user_id'   => $userId,
                        'verify'    => $verify_code,
                        'type'      => $type,
                        'sort_date' => $sort_date,
                    );
                    $data = array_merge($data, $addData);
                    $userVerifyCode->add($data);
                    $result['code'] = $verify_code;
                }
            } else {
                $result['status']   = '400000';
                $result['message']  = L('_PC_LOGIN_VERIFICATION_LIMITED_');
            }
        } else {
            $result['status']   = '400000';
            $result['message']  = L('_PC_LOGIN_VERIFICATION_INTERVAL_TIME_', array('intervalTime'=> C('VERIFY_CODE_INTERVAL_TIME')));
        }
        return $result;
    }

    /** 
     * 验证码生成
     */
    public function verify_c() {
        $Verify = new \Think\Verify();
        $Verify->fontSize = 18;
        $Verify->length   = 4;
        $Verify->useNoise = false;
        $Verify->codeSet = '0123456789';
        $Verify->imageW = 130;
        $Verify->imageH = 42;
        $Verify->expire = 600;
        $Verify->entry();
    }

    /**
     * [imreg 注册环信]
     * @Author kofu<[418382595@qq.com]>
     * @param  [type]                   $id       [description]
     * @param  [type]                   $nickname [description]
     * @return [type]                             [description]
     */
    public function imreg($id, $nickname){
        $options = imConf();
        $Easemob = new Easemob($options);
        $username = "wedo{$id}";
        $save = $Easemob->createUser($username, $username, $nickname);
        return $save;
    }
}
