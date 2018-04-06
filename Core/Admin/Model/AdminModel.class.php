<?php
namespace Admin\Model;
use Think\Model;
class AdminModel extends Model {
	/**
	 * [_validate 自动验证]
     * @author kofu <[418382595@qq.com]>
	 */
	protected $_validate = array(
		// 新增
		array('group_id', 'require', '请选择角色', 1, 'regex', 1),
		array('admin_account', 'require', '账号不能为空', 1, 'regex', 1),
		array('admin_account', '', '该账号已添加', 1, 'unique', 1),
		array('admin_password', 'require', '密码不能为空', 1, 'regex', 1),
		// array('admin_password', 'admin_repassword', '两次输入密码不同！', 1, 'confirm', 1),

		// 修改
		array('group_id', 'require', '请选择角色', 1, 'regex', 1),
		array('admin_password', 'require', '密码不能为空', 1, 'regex', 2),
		// array('admin_password', 'admin_repassword', '两次输入密码不同！', 1, 'confirm', 2),
	);

	/**
	 * [_auto 自动完成]
     * @author kofu <[418382595@qq.com]>
	 */
	protected $_auto = array(
		array('add_time', 'time', 1, 'function'),
	);
}
