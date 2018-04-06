<?php
namespace Admin\Model;
use Think\Model;
class AdModel extends Model {
    /**
     * [_validate 自动验证]
     * @author kofu <[418382595@qq.com]>
     */
	protected $_validate = array(
		// 新增
		array('url', 'require', '请输入链接地址', 1, 'regex', 1),
		array('image', 'require', '请上传广告图片', 1, 'regex', 1),

		// 编辑
		array('url', 'require', '请输入链接地址', 1, 'regex', 2),
		array('image', 'require', '请上传广告图片', 1, 'regex', 2),

		array('sort', 'require', '请输入排序', 1, 'regex', 4),
	);

    /**
     * [_auto 自动完成]
     * @author kofu <[418382595@qq.com]>
     */
	protected $_auto = array(
		// array('start_time', 'strtotime', 3, 'function'),
		// array('end_time', 'strtotime', 3, 'function'),
		array('add_time', 'time', 1, 'function'),
	);
}