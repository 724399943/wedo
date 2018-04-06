<?php
namespace Shop\Controller;
use Think\Controller;

class UserController extends BaseController {
    private $userModel;
    public function __construct() 
    {
        parent::__construct();
        $this->userModel = D('User');
    }

    /**
     * [index 个人中心首页]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function index() {
        $userId = session('userId');
        $field = '`id`, `nickname`, `headimgurl`, `money`, `all_earning`, `point`';
        $userInfo = $this->userModel->getUserInfo($userId, $field);
        
        $order = M('order');
        // 待付款
        $where = array(
            'user_id'=> $userId, 
            'is_pay'=> '0',
            'is_out_date'=> '0',
            'is_split'=> array('IN', '0,1')
        );
        $userInfo['order']['pay'] = $order->where($where)->count();

        // 待发货
        $where = array(
            'user_id'=> $userId, 
            'is_pay'=> '1',
            'delivery_status'=> '0',
            'received'=> '0',
            'is_out_date'=> '0',
            'is_split'=> array('IN', '0,1')
        );
        $userInfo['order']['delivery'] = $order->where($where)->count();

        // 待收货
        $where = array(
            'user_id'=> $userId, 
            'is_pay'=> '1',
            'delivery_status'=> '1',
            'received'=> '0',
            'is_out_date'=> '0',
            'is_split'=> array('IN', '0,1')
        );
        $userInfo['order']['receive'] = $order->where($where)->count();

        // 待评价
        $where = array(
            'user_id'=> $userId, 
            'is_pay'=> '1',
            'delivery_status'=> '1',
            'received'=> '1',
            'is_comment'=> '0',
            'is_out_date'=> '0',
            'is_split'=> array('IN', '0,1')
        );
        $userInfo['order']['comment'] = $order->where($where)->count();

        $dbPrefix = C('DB_PREFIX');
        $sql = "SELECT count(*) AS total 
                FROM `{$dbPrefix}message` AS `m` 
                LEFT JOIN `{$dbPrefix}message_relevance` AS `mr` ON `m`.`id` = `mr`.`message_id` 
                WHERE `mr`.`receiver_id` = '{$userId}' 
                AND `mr`.`is_read`= '0' 
                AND `m`.`is_delete` = '0'";
        $message = M()->query($sql);

        if ( IS_POST ) {
            exit(statusCode(array(
                'list'  => $userInfo,
                'message' => $message[0]['total']
            )));
        } else {
            $this->assign('message', $message[0]['total']);
            $this->assign('list', $userInfo);
            $this->display();
        }
    }

    /**
     * [userInfo 个人信息]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function userInfo() 
    {
        if ( IS_POST ) {
            $userId = session('userId');
            $field = '`id`, `nickname`, `headimgurl`, `pay_password`, `sex`, `username`, `phone`, `email`, `money`, `point`, `longitude`, `latitude`, `last_earning`, `all_earning`, `status`';
            $userInfo = $this->userModel->getUserInfo($userId, $field);
            exit(statusCode(array(
                'list' => $userInfo
            )));
        } else {
            $this->display();
        }
    }

    /**
     * [verifyPayPassword 验证支付密码]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function verifyPayPassword() 
    {
        if ( IS_POST ) {
            $user = M('user');
            $userId = session('userId');
            $password = I('password');
            $password = encrypt($password);
            $payPassword = $user->where(array('id'=> $userId))->getField('`pay_password`');
            ( $payPassword == $password ) ? 
                exit(statusCode()) : 
                exit(statusCode(array(), 100002));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [editPayPassword 修改支付密码]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function editPayPassword() 
    {
        if ( IS_POST ) {
            $user = M('user');
            $userId = session('userId');
            $password = I('password');
            $password = encrypt($password);
            $saveData = array(
                'id' => $userId,
                'pay_password' => $password
            );
            ( $user->save($saveData) !== false ) ? 
                exit(statusCode()) : 
                exit(statusCode(array(), 100002));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [verifyWithdrawPassword 验证提现密码]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function verifyWithdrawPassword() 
    {
        if ( IS_POST ) {
            $user = M('user');
            $userId = session('userId');
            $password = I('password');
            $password = encrypt($password);
            $payPassword = $user->where(array('id'=> $userId))->getField('`withdraw_password`');
            ( $payPassword == $password ) ? 
                exit(statusCode()) : 
                exit(statusCode(array(), 100002));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [editWithdrawPassword 修改提现密码]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function editWithdrawPassword() 
    {
        if ( IS_POST ) {
            $user = M('user');
            $userId = session('userId');
            $password = I('password');
            $password = encrypt($password);
            $saveData = array(
                'id' => $userId,
                'withdraw_password' => $password
            );
            ( $user->save($saveData) !== false ) ? 
                exit(statusCode()) : 
                exit(statusCode(array(), 100002));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [updateUser 更新用户信息]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function updateUser() {
        if (IS_POST) {
            $userId = session('userId');
            $user = D('User');
            $data = $user->create(I('post.'), 2);
            if ( empty($data) ) {
                exit(statusCode(array(), 400000, $user->getError()));
            }
            $data['id'] = $userId;
            if ($user->save($data) !== false) {
                $userInfo = $user->find($userId);
                session('userInfo', $userInfo);
                exit(statusCode());
            } else {
                exit(statusCode(array(), 100002));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [userFeedback 意见反馈]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function userFeedback() 
    {
        if ( IS_POST ) {
            $userId = session('userId');
            $userFeedback = D('UserFeedback');
            $data = $userFeedback->create(I('post.'), 1);
            if ( !empty($data) ) {
                $data['user_id'] = $userId;
                ( $userFeedback->add($data) ) ?
                    exit(statusCode()) : 
                    exit(statusCode(array(), 100002));
            } else {
                exit(statusCode(array(), 400000, $userFeedback->getError()));
            }
        } else {
            $this->display();
        }
    }

    /**
     * [feedbackSuccess 反馈成功]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function feedbackSuccess() {
        $this->display();
    }

    /**
     * [withdraw 提现]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function withdraw() 
    {
        if ( IS_POST ) {
            $userId = session('userId');
            $userWithdraw = D('UserWithdraw');
            $data = $userWithdraw->create(I('post.'), 1);
            if ( !empty($data) ) {
                $result = $userWithdraw->checkMoney($userId, $data['money']);
                if ( $result['status'] == '400000' ) {
                    exit(statusCode(array(), $result['status'], $result['message']));
                }
                $data['user_id'] = $userId;
                if ( $userWithdraw->add($data) ) {
                    // 扣除金额
                    $userWithdraw->setDecMoney($userId, $data['money']);
                    exit(statusCode());
                } else {
                    exit(statusCode(array(), 100002));
                }
            } else {
                exit(statusCode(array(), 400000, $userWithdraw->getError()));
            }
        } else {
            $userInfo = $this->userModel->getUserInfo();
            $maxWithdrawMoney = $userInfo['money'] - C('registerGetMoney');
            $maxWithdrawMoney = $maxWithdrawMoney < 0 ? 0 : $maxWithdrawMoney;
            $this->assign('maxWithdrawMoney', $maxWithdrawMoney);
            $this->assign('userInfo', $userInfo);
            $this->display();
        }
    }

    /**
     * [withdrawSuccess 提现成功]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function withdrawSuccess() {
        $this->display();
    }

    /**
     * [withdrawLog 提现记录]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function withdrawLog() 
    {
        if ( IS_POST ) {
            $userId = session('userId');
            $userWithdraw = D('UserWithdraw');
            $parameter = array(
                'userId' => $userId,
                'type' => I('type', '0'),
                'page' => $page,
                'limitStart' => $limitStart,
                'limit' => $limit
            );
            $returnData = $userWithdraw->getWithdrawLog($parameter);
            exit(statusCode($returnData));
        } else {
            $this->display();
        }
    }

    /**
     * [uploadUserImage 上传用户图片]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function uploadUserImage() {
        if (IS_POST) {
            $image = I('image');
            if (empty($image)) {
                exit(statusCode(array(), 400000, '请上传图片！'));
            }
            $imageData = base64_upload($image, 'User'); //保存图片
            if ( !$imageData['boolean'] ) {
                exit(statusCode(array(), $imageData['status']));
            } else {
                exit(statusCode(array('image'=> $imageData['data'])));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

   /**
     * [feedback 用户反馈信息]
     * @author shichun <672517056@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function feedback() {
        $userId = session('userId'); //获取用户ID
        if (IS_POST) {
            $content = I('post.text',''); 

            if (empty($content)) {
                // $this->error('您还没输入内容');
                exit(statusCode(array(),400000,'您还没输入内容!'));
            }
            $data = array(
                'user_id' => $userId,
                'text'  => $content,
                'add_time' =>time(),
            );
            $feed = M('user_feedback')->add($data);
            if ($feedback !== false) {
                // $this->success('我们已经收到您的反馈!','{:U(User/feedback)}');
                exit(statusCode(array(),400000,'我们已经收到您的反馈!'));
            }else{
                // $this->error('提交失败,请再次提交!');
                exit(statusCode(array(),400000,'提交失败,请再次提交!'));
            }
        }else{
            $this->display();
        }
    }

    /**
     * [chooseLanguage 选择语言]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function chooseLanguage() {
        if ( IS_POST ) {
            $userId = session('userId');
            $userModel = D('User');
            $language = I('language', 'en-us', 'string');
            $jump = I('jump', '0', 'string');
            cookie('think_language', $language, 3600);
            session('language', $language);
            $userModel->where(array('id'=> $userId))->save(array('language'=> $language));
            ($jump == '0') ? header("Location:" . U('User/chooseLanguage')) : exit(statusCode());
        } else {
            $this->display();
        }
    }

    /**
     * [toAttention 关注商家]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function toAttention() {
        if ( IS_POST ) {
            $agent_id = I('agent_id');
            $userId = session('userId');
            $userAttention = M('user_attention');
            $addData = array(
                'user_id' => $userId,
                'agent_id' => $agent_id
            );
            if ( $userAttention->where($addData)->count() > 0 ) {
                exit(statusCode(array(), 400000, '您已关注该商家'));
            }
            ( $userAttention->add($addData) !== false ) ?
                exit(statusCode()) :
                exit(statusCode(array(), 400000, '关注失败'));
        } else {
            exit(statusCode(array(), 100001));
        }
    }
}