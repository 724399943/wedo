<?php
namespace Shop\Model;
use Think\Model;
class GoodsToPointModel extends Model {
	protected $_validate = array(
		// 新增
		array('goods_id', 'require', '请选择商品！', 1, 'regex', 1),

	);

	protected $_auto = array(
		array('add_time', 'time', 1, 'function'),
	);
}