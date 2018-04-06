<?php
namespace Shop\Model;
use Think\Model;

class UserVerifyCodeModel extends Model {
	/**
	 * [_validate 自动验证]
     * @author kofu <[418382595@qq.com]>
	 */
	protected $_validate = array(
		// 验证手机
		// array('phone', 'require', '手机号码不能为空！', 1, 'regex', 4),
		// array('phone', checkPhone, '手机号码格式不对！', 1, 'callback', 4),

		// 验证邮箱
		// array('email', 'require', '邮箱不能为空！', 1, 'regex', 5),
		// array('email', checkEmail, '邮箱格式不对！', 1, 'callback', 5),
	);

	protected $_auto = array(
		array('add_time', 'time', 4, 'function'),
		array('add_time', 'time', 5, 'function'),
	);

	/**
	 * [checkPhone 校验手机]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)    2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $account [description]
	 * @return    [type]               [description]
	 */
	protected function checkPhone($account) {
		$result = ( !isPhone($account) ) ? false : true;
		return $result;
	}

	/**
	 * [checkEmail 校验邮箱]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)      2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $account [description]
	 * @return    [type]                 [description]
	 */
	protected function checkEmail($account) {
		$result = ( !isEmail($account) ) ? false : true;
		return $result;
	}
}
