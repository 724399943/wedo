<?php
namespace Admin\Model;
use Think\Model;

class MessageModel extends Model {
    /**
     * [_validate 自动验证]
     * @author kofu <[418382595@qq.com]>
     */
	protected $_validate = array(
		// 新增
		array('title', 'require', '请输入消息标题！', 1, 'regex', 1),
		array('content', 'require', '请输入消息详情！', 1, 'regex', 1),
		array('image', 'require', '请上传封面！', 1, 'regex', 1),

		// 编辑
	);

    /**
     * [_auto 自动完成]
     * @author kofu <[418382595@qq.com]>
     */
	protected $_auto = array(
		array('add_time', 'time', 1, 'function'),
		array('type', 0),
		array('message_type', 0),
	);
}