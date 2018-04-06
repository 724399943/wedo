<?php
namespace Admin\Controller;
use Think\Controller;
// 基础控制器
class BaseController extends Controller {
    /**
     * [_initialize 初始化]
     * @author StanleyYuen <[350204080@qq.com]>
     */
	public function _initialize() {
        session_start();
        $adminId 	  = session('adminId');         // 管理员用户id
        $adminAccount = session('adminAccount');    // 管理员用户名
        // 其他条件
        $otherCondition = !in_array(CONTROLLER_NAME , array('Login'));
        if ( empty($adminId) && empty($adminAccount) && $otherCondition ) {
            header('LOCATION:' . U('Login/login'));
            exit();
        } else {
            // 登录时选择【保存一周】。若用户访问，则时间在用户登录那一刻顺延
            $rememberPassword = session('rememberPassword');
            if ($rememberPassword == '1') {
                session_write_close();
                $nextWeekTime = 3600 * 24 * 7;
                session_cache_expire($nextWeekTime / 60);
                session_set_cookie_params($nextWeekTime);
                session_start();
            }

	        // 读取数据库配置
	        $config = M('config')->getField('config_sign, config_value');
	        C($config);

            R('Public/auth', array($adminId));
            $authList = session('authList');
        }
	}
}