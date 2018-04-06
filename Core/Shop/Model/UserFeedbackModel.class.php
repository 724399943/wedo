<?php
namespace Shop\Model;
use Think\Model;

class UserFeedbackModel extends Model {
	/**
	 * [_validate 自动验证]
     * @author kofu <[418382595@qq.com]>
	 */
	protected $_validate = array(
		// 新增
		array('text', 'require', '{%module_user_feedback_content}', 1, 'regex', 1),

		// 修改
		// array('title', 'require', '请输入求购标题！', 1, 'regex', 2),
	);

	/**
	 * [_auto 自动完成]
     * @author kofu <[418382595@qq.com]>
	 */
	protected $_auto = array(
		array('add_time', 'time', 1, 'function'),
	);
}
