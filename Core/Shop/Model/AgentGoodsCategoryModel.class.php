<?php
namespace Shop\Model;
use Think\Model;

class AgentGoodsCategoryModel extends Model {
	/**
     * [_validate 自动验证]
     * @author kofu <[418382595@qq.com]>
     */
	protected $_validate = array(
		// 新增
		array('category_name', 'require','{%module_agent_goods_category_name}', 1, 'regex', 1),
		array('category_name', '','{%module_agent_goods_category_name_repeat}', 1, 'unique', 1),

		// 编辑
		array('category_name', 'require','{%module_agent_goods_category_name}', 1, 'regex', 2),
	);

    /**
     * [_auto 自动完成]
     * @author kofu <[418382595@qq.com]>
     */
	protected $_auto = array(
		array('add_time', 'time', 1, 'function'),
	);
}
