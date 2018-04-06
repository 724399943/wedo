<?php
namespace Shop\Model;
use Think\Model;

class UserModel extends Model {
	private $letter = "AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz";
	private $number = "0123456789";

	/**
	 * [_validate 自动验证]
     * @author kofu <[418382595@qq.com]>
	 */
	protected $_validate = array(
		// 新增
		array('phone', 'require', '{%_PC_LOGIN_ENTER_YOUR_MOBILE_NUMBER_}', 1, 'regex', 1),
		array('phone', '', '{%_PC_LOGIN_CONTACT_REGISTERED_}', 1, 'unique', 1),
		array('password', 'require', '{%_PC_LOGIN_ENTER_YOUR_PASSWORD_}', 1, 'regex', 1),
		array('password', checkPassword, '{%_PC_LOGIN_ENTER_NEW_PASSWORD_}', 1, 'callback', 1),
		array('password', 'repassword', '{%_PC_LOGIN_PASSWORD_DID_NOT_MATCH_}', 1, 'confirm', 1),

		// 修改
		array('nickname', 'require', '昵称不能为空！', 1, 'regex', 2),
		array('sex', 'require', '请选择性别！', 1, 'regex', 2),
		// array('headimgurl', 'require', '请上传头像！', 1, 'regex', 2),
		
		// 忘记密码
		array('account', checkAccount, '手机/邮箱不存在！', 1, 'callback', 4),
		array('password', 'require', '{%_PC_LOGIN_ENTER_YOUR_PASSWORD_}', 1, 'regex', 4),
		array('password', checkPassword, '{%_PC_LOGIN_ENTER_NEW_PASSWORD_}', 1, 'callback', 4),
		array('verify', 'require', '{%_PC_LOGIN_ENTER_PHONE_CODE_}', 1, 'regex', 4),

		// 修改密码
		array('account', checkAccount, '手机/邮箱不存在！', 1, 'callback', 5),
		array('password', 'require', '{%_PC_LOGIN_ENTER_YOUR_PASSWORD_}', 1, 'regex', 5),
		array('password', checkPassword, '{%_PC_LOGIN_ENTER_NEW_PASSWORD_}', 1, 'callback', 5),
		array('verify', 'require', '{%_PC_LOGIN_ENTER_PHONE_CODE_}', 1, 'regex', 5),

		// 手机注册
		array('phone', 'require', '{%_PC_LOGIN_ENTER_YOUR_MOBILE_NUMBER_}', 1, 'regex', 6),
		array('phone', '', '{%_PC_LOGIN_CONTACT_REGISTERED_}', 1, 'unique', 6),
		array('password', 'require', '{%_PC_LOGIN_ENTER_YOUR_PASSWORD_}', 1, 'regex', 6),
		array('password', checkPassword, '{%_PC_LOGIN_ENTER_NEW_PASSWORD_}', 1, 'callback', 6),
		array('verify', 'require', '{%_PC_LOGIN_ENTER_PHONE_CODE_}', 1, 'regex', 6),

		// 邮箱注册
		array('email', 'require', '{%_PC_LOGIN_ENTER_YOUR_EMAIL_ADDRESS_}', 1, 'regex', 7),
		array('email', '', '{%_PC_LOGIN_EMAIL_ALREADY_REGISTERED_}', 1, 'unique', 7),
		array('password', 'require', '{%_PC_LOGIN_ENTER_YOUR_PASSWORD_}', 1, 'regex', 7),
		array('password', checkPassword, '{%_PC_LOGIN_ENTER_NEW_PASSWORD_}', 1, 'callback', 7),
		array('verify', 'require', '{%_PC_LOGIN_ENTER_PHONE_CODE_}', 1, 'regex', 7),

		// 修改密码
		array('password', 'require', '{%_PC_LOGIN_ENTER_YOUR_PASSWORD_}', 1, 'regex', 8),
		array('new_password', 'require', '{%_PC_LOGIN_ENTER_YOUR_PASSWORD_}', 1, 'regex', 8),
		array('new_repassword', 'require', '{%_PC_LOGIN_ENTER_YOUR_PASSWORD_}', 1, 'regex', 8),
		array('new_password', checkPassword, '{%_PC_LOGIN_ENTER_NEW_PASSWORD_}', 1, 'callback', 8),
		array('new_repassword', checkPassword, '{%_PC_LOGIN_ENTER_NEW_PASSWORD_}', 1, 'callback', 8),
		array('new_password', 'new_repassword', '{%_PC_LOGIN_PASSWORD_DID_NOT_MATCH_}', 1, 'confirm', 8),
	);

	protected $_auto = array(
		array('add_time', 'time', 6, 'function'),
		array('add_time', 'time', 7, 'function'),
	);

	/**
	 * [checkAccount 检测手机/邮箱]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)      2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $account [description]
	 * @return    [type]                 [description]
	 */
	protected function checkAccount( $account ) {
		if ( isEmail($account) ) {
			$where['email'] = $account;
			$count = $this->where($where)->count();
		} elseif ( isPhone($account) ) {
			$where['phone'] = $account;
			$count = $this->where($where)->count();
		}
		$result = ( $count <= 0 ) ? false : true;
		return $result;
	}

	/**
	 * [checkPassword 检验密码]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)     2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $string [description]
	 * @return    [type]                [description]
	 */
	protected function checkPassword($string) {
		$letter = $this->letter;
		$number = $this->number;
		$length = strlen($string);
		$checkData = array();
		if ( $length > 12 || $length < 6 ) {
			return false;
		}
		for ($i = 0; $i < $length; $i ++ ) {
			$thatData = $string[$i];
			if ( strpos($letter, $thatData) === false && strpos($number, $thatData) === false ) {
				return false;
			}
			if ( strpos($letter, $thatData) !== false ) {
				$checkData['letter'] = 1;
			}
			if ( strpos($number, $thatData) !== false ) {
				$checkData['number'] = 1;
			} 
		}
		if ( empty($checkData['number']) || empty($checkData['letter']) ) {
			return false;
		}
		return true;
	}

	/**
     * [getUserInfo 获取用户信息]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function getUserInfo($userId = '', $field = '') 
    {
        $userId = empty($userId) ? session('userId') : $userId;
        $userInfo = $this->field($field)->find($userId);
        return $userInfo;
    }
}
