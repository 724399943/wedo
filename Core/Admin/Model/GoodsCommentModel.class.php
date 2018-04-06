<?php
namespace Admin\Model;
use Think\Model;

class GoodsCommentModel extends Model {
	// private $dbPrefix;
	// public function __construct() {
	// 	parent::__construct();
	// 	$this->dbPrefix = C('DB_PREFIX');
	// }
	/**
	 * [_validate 自动验证]
     * @author kofu <[418382595@qq.com]>
	 */
	protected $_validate = array(
		// 新增
		array('contain', 'require', '请输入评价内容！', 1, 'regex', 1),
		array('star', 'require', '请选择星数！', 1, 'regex', 1),
		// array('photo', 'require', '请上传评论图片！', 1, 'regex', 1),
		// array('photo', checkPhoto, '最多上传3张评论图片！', 1, 'callback', 1),

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

	/**
	 * [getComment 获取评论]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $parameter [description]
	 * @return    [type]                   [description]
	 */
	public function getComment($parameter) {
		$type = !empty($parameter['type']) ? $parameter['type'] : '0';
		$agent_id = !empty($parameter['agent_id']) ? $parameter['agent_id'] : '';
		$goods_id = !empty($parameter['goods_id']) ? $parameter['goods_id'] : '';
		$page = !empty($parameter['page']) ? $parameter['page'] : 0;
		$limitStart = !empty($parameter['limitStart']) ? $parameter['limitStart'] : 0;
		$limit = !empty($parameter['limit']) ? $parameter['limit'] : 10;
		if ( !empty($agent_id) ) {
			$where['agent_id'] = $agent_id;
		}
		if ( !empty($goods_id) ) {
			$where['goods_id'] = $goods_id;
		}
		$where['status'] = '0';
		$count = 0;
		if ( $type == '1' ) {
			$count = $this->where($where)->count();
		}
		$goodsCommentImage = M('goods_comment_image');
		$field = '`id`, `nickname`, `user_id`, `order_sn`, `goods_id`, `headimgurl`, `star`, `contain`, `reply_contain`, `image_ids`, `attr_list`, `add_time`';
		$list = $this->where($where)->field($field)->order('`id` DESC')->limit($limitStart. ',' . $limit)->select();
		foreach ($list as $key => &$value) {
			$map['id'] = array('IN', $value['image_ids']);
			$value['images'] = $goodsCommentImage->where($map)->field('`comment_image`')->select();
			$value['images'] = array_column($value['images'], 'comment_image');
			if ( $type == '1' ) $this->processData($value);
		}
		$returnData['list']			= $list;
        $returnData['page']         = $page + 1;  
		$returnData['count']        = ceil($count / $limit);
		return $returnData;
	}

	private function processData(&$data) {
		$dbPrefix = C('DB_PREFIX');
		$sql = "SELECT `od`.`unit_price`, `od`.`price`, `od`.`goods_number`, `od`.`goods_name`, `o`.`express_type` 
				FROM `{$dbPrefix}order_detail` AS `od` 
				LEFT JOIN `{$dbPrefix}order` AS `o` ON `od`.`order_sn` = `o`.`order_sn` 
				WHERE `od`.`order_sn` = '{$data['order_sn']}' 
				AND `od`.`goods_id` = '{$data['goods_id']}'";
		$result = M()->query($sql);
		$data = !empty($result[0]) ? array_merge($result[0], $data) : $data;
	}
}
