<?php
namespace Admin\Controller;
use Think\Controller;
// 登录控制器
class LoginController extends Controller {
    /**
     * [login 管理员登录]
     * @author StanleyYuen <[350204080@qq.com]>
     */
	public function login() {
        if ( IS_POST ) {
	        if(!$_SESSION['admin_login']){
		        die('非法请求');
	        }
            // 记住密码
            $rememberPassword = I('post.rememberPassword');
            if ( $rememberPassword == '1' ) {
                $nextWeekTime = 3600 * 24 * 7;
                session_cache_expire($nextWeekTime / 60);
                session_set_cookie_params($nextWeekTime);
            }
            session_start();
			$_post = I('post.');
            $account  = I('post.account');
        	$password = I('post.password');
			if (array_key_exists('verify_c', $_post))
	        {
		        $verify_c = I("post.verify_c");
		        $verify    = new \Think\Verify();
		        $isVerify  = $verify->check($verify_c);
		        if (!$isVerify)
		        {
			        $this->error('请输入正确的验证码！');
		        }
	        }
            // 采用系统加密
            // $password = _encrypt($password);
            $password = encrypt($password);

    		$admin    = M('admin')->field('id, is_lock, admin_account')->where(array('admin_account'=>$account, 'admin_password'=>$password))->find();
            if ($admin['is_lock'] == '1') {
            	$this->error('账户被锁定！');
            }

    		if ( !empty($admin) ) {
    			session('adminId',      $admin['id']);
                session('adminAccount', $admin['admin_account']);

			    $_SESSION['login_fail'] = 0;
                if ( $rememberPassword == '1' ) {
                    session('rememberPassword', 1);
                }

                R('Public/auth', array($admin['id']));
            } else {
			    $login_fail = $_SESSION['login_fail'] +1;
			    $_SESSION['login_fail'] = $login_fail;
			    if ($login_fail > 2)
			    {

					$_SESSION['is_show'] = 1;
			    }
    			$this->error('密码或用户出错！', U('Login/login'));
            }
    	} else {
	        $_SESSION['admin_login'] =1;
	        $is_show = $_SESSION['is_show'];
	        $this->assign('is_show',$is_show);
			$this->display('login');
    	}
	}

    /**
     * [logout 管理员退出]
     * @author StanleyYuen <[350204080@qq.com]>
     */
    public function logout() {
        session_start();
        session('adminId', null);
        session('adminAccount', null);
        session('rememberPassword', null);

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie( session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"] );
        }

        $this->success('退出成功！', U('Login/login'));
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
}