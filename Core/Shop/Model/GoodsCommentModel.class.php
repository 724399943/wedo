<?php
namespace Shop\Model;
use Think\Model;

class GoodsCommentModel extends Model {
	private $dbPrefix;
	public function __construct() {
		parent::__construct();
		$this->dbPrefix = C('DB_PREFIX');
	}
	/**
	 * [_validate 自动验证]
     * @author kofu <[418382595@qq.com]>
	 */
	protected $_validate = array(
		// 新增
		array('contain', 'require', '{%module_goods_comment_review_content}', 1, 'regex', 1),
		array('star', 'require', '{%module_goods_comment_select_stars}', 1, 'regex', 1),
		// array('photo', 'require', '{%module_goods_comment_review_photo}', 1, 'regex', 1),
		// array('photo', checkPhoto, '{%module_goods_comment_max_upload_photo}', 1, 'callback', 1),

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
	 * [checkPhoto 检测图片上传数量]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
	public function checkPhoto() {
		$photo = I('photo', '', 'htmlspecialchars_decode');
		$photo = json_decode($photo, true);
		if ( count($photo) > 3 ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * [getComment 获取评论]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $parameter [description]
	 * @return    [type]                   [description]
	 */
	public function getComment($parameter) {
		$goodsModel = D('Goods');
		$goods_name = !empty($parameter['goods_name']) ? $parameter['goods_name'] : '';
		$express_type = $parameter['express_type'] != '' ? $parameter['express_type'] : '-1';
		$type = !empty($parameter['type']) ? $parameter['type'] : '0';
		$star = $parameter['star'] != '' ? $parameter['star'] : '-1';
		$is_reply = $parameter['is_reply'] != '' ? $parameter['is_reply'] : '-1';
		$agent_id = !empty($parameter['agent_id']) ? $parameter['agent_id'] : '';
		$goods_id = !empty($parameter['goods_id']) ? $parameter['goods_id'] : '';
		$page = !empty($parameter['page']) ? $parameter['page'] : 0;
		$limitStart = !empty($parameter['limitStart']) ? $parameter['limitStart'] : 0;
		$limit = !empty($parameter['limit']) ? $parameter['limit'] : 10;
		$whereStr = '';
		if ( !empty($goods_name) ) {
			$whereStr .= " AND `od`.`goods_name` LIKE '%{$goods_name}%'";
		}
		if ( $express_type != '-1' ) {
			$whereStr .= " AND `o`.`express_type` = '{$express_type}'";
		}
		if ( $star != '-1' ) {
			$whereStr .= " AND `gc`.`star` = '{$star}'";
		}
		if ( $is_reply != '-1' ) {
			$whereStr .= ( $is_reply == '0' ) ? " AND `gc`.`reply_contain` = ''" : " AND `gc`.`reply_contain` != ''";
		}
		if ( !empty($agent_id) ) {
			$whereStr .= " AND `gc`.`agent_id` = '{$agent_id}'";
		}
		if ( !empty($goods_id) ) {
			$data = $goodsModel->where(array('goods_main_id'=> $goods_id))->field('id')->select();
			$data = array_column($data, 'id');
			$goods_id = !empty($data) ? 
				implode(',', $data) . ",{$goods_id}" : 
				$goods_id;
			$whereStr .= " AND `gc`.`goods_id` IN({$goods_id})";
		}
		$count = 0;
		if ( $type == '1' ) {
			$sql = "SELECT COUNT(*) AS `count` 
				FROM `{$this->dbPrefix}goods_comment` AS `gc` 
				LEFT JOIN `{$this->dbPrefix}order_detail` AS `od` ON `gc`.`order_sn` = `od`.`order_sn` AND `gc`.`goods_id` = `od`.`goods_id` 
				LEFT JOIN `{$this->dbPrefix}order` AS `o` ON `od`.`order_sn` = `o`.`order_sn` 
				WHERE 1{$whereStr}";
			$count = M()->query($sql);
			$count = !empty($count[0]['count']) ? $count[0]['count'] : '0';
		}
		$goodsCommentImage = M('goods_comment_image');
		$sql = "SELECT `gc`.`id`, `gc`.`nickname`, `gc`.`user_id`, `gc`.`order_sn`, `gc`.`goods_id`, `gc`.`headimgurl`, `gc`.`star`, `gc`.`contain`, `gc`.`reply_contain`, `gc`.`image_ids`, `gc`.`attr_list`, `gc`.`add_time`, `od`.`unit_price`, `od`.`price`, `od`.`goods_number`, `od`.`goods_name`, `o`.`express_type` 
				FROM `{$this->dbPrefix}goods_comment` AS `gc` 
				LEFT JOIN `{$this->dbPrefix}order_detail` AS `od` ON `gc`.`order_sn` = `od`.`order_sn` AND `gc`.`goods_id` = `od`.`goods_id` 
				LEFT JOIN `{$this->dbPrefix}order` AS `o` ON `od`.`order_sn` = `o`.`order_sn` 
				WHERE 1{$whereStr} 
				ORDER BY `gc`.`id` DESC 
				LIMIT {$limitStart} , {$limit}";
		$list = M()->query($sql);
		foreach ($list as $key => &$value) {
			if ( !empty($value['image_ids']) ) {
				$map['id'] = array('IN', $value['image_ids']);
				$value['images'] = $goodsCommentImage->where($map)->field('`comment_image`')->select();
				$value['images'] = array_column($value['images'], 'comment_image');
			} else {
				$value['images'] = array();
			}
			// if ( $type == '1' ) $this->processData($value);
		}
		$returnData['list']		= $list;
        $returnData['page']     = $page + 1;  
		$returnData['count']    = ceil($count / $limit);
		return $returnData;
	}

	// private function processData(&$data) {
	// 	$dbPrefix = C('DB_PREFIX');
	// 	$sql = "SELECT `od`.`unit_price`, `od`.`price`, `od`.`goods_number`, `od`.`goods_name`, `o`.`express_type` 
	// 			FROM `{$dbPrefix}order_detail` AS `od` 
	// 			LEFT JOIN `{$dbPrefix}order` AS `o` ON `od`.`order_sn` = `o`.`order_sn` 
	// 			WHERE `od`.`order_sn` = '{$data['order_sn']}' 
	// 			AND `od`.`goods_id` = '{$data['goods_id']}'";
	// 	$result = M()->query($sql);
	// 	$data = !empty($result[0]) ? array_merge($result[0], $data) : $data;
	// }
}
