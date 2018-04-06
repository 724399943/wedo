<?php
namespace Shop\Model;
use Think\Model;

class OrderModel extends Model {
	private $dbPrefix;
	public function __construct() {
		parent::__construct();
		$this->dbPrefix = C('DB_PREFIX');
	}

	/**
	 * [getOrder 订单]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)         2016          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $userId     [description]
	 * @param     [type]        $orderType  [all：全部，toBePaid：待支付，toBeShipped：待发货，toBeReceived：待收货，toBeComment：待评价]
	 * @param     [type]        $userType   [buyer：买家订单，saler：卖家订单]
	 * @param     integer       $limitStart [description]
	 * @param     integer       $limit      [description]
	 * @return    [type]                    [description]
	 */
	public function getOrder($parameter) {
		$userId = !empty($parameter['userId']) ? $parameter['userId'] : '';
		$agentId = !empty($parameter['agentId']) ? $parameter['agentId'] : '';
		$orderSn = !empty($parameter['orderSn']) ? $parameter['orderSn'] : '';
		$orderType = !empty($parameter['orderType']) ? $parameter['orderType'] : 'all';
		$userType = !empty($parameter['userType']) ? $parameter['userType'] : 'buyer';
		$expressType = !empty($parameter['expressType']) ? $parameter['expressType'] : '-1';
		$date = !empty($parameter['date']) ? $parameter['date'] : '';
		$type = !empty($parameter['type']) ? $parameter['type'] : 0;
		$page = !empty($parameter['page']) ? $parameter['page'] : 0;
		$startTime = !empty($parameter['startTime']) ? $parameter['startTime'] : 0;
		$endTime = !empty($parameter['endTime']) ? $parameter['endTime'] : 0;
		$limitStart = !empty($parameter['limitStart']) ? $parameter['limitStart'] : 0;
		$limit = !empty($parameter['limit']) ? $parameter['limit'] : 10;

		$orderModel = M('order');
		$userModel = M('user');
		$orderDetailModel = M('order_detail');
		switch ($orderType) {
			case 'all':
				// $where = array(
				// 	'is_out_date' => '0',
				// );
				break;
			case 'toBePaid':
				$where = array(
					'is_pay' => '0',
					'delivery_status' => '0',
					'received' => '0',
					'is_out_date' => '0',
				);
				break;
			case 'toBeShipped':
				$where = array(
					'is_pay' => '1',
					'delivery_status' => '0',
					'received' => '0',
					'is_out_date' => '0',
				);
				break;
			case 'toBeReceived':
				$where = array(
					'is_pay' => '1',
					'delivery_status' => '1',
					'received' => '0',
					'is_out_date' => '0',
				);
				break;
			case 'toBeComment':
				$where = array(
					'is_pay' => '1',
					'delivery_status' => '1',
					'received' => '1',
					'is_comment' => '0',
					'is_out_date' => '0',
				);
				break;
			case 'isFinish':
				$where = array(
					'is_pay' => '1',
					'delivery_status' => '1',
					'received' => '1',
					'is_comment' => '1',
					'is_out_date' => '0',
				);
				break;
			case 'isOutDate':
				$where = array(
					'is_out_date' => '1',
				);
				break;
		}
		if ( !empty($orderSn) ) {
			$where['order_sn'] = array('LIKE', "%{$orderSn}%");
		}
		// 日期筛选
		if ( !empty($date) ) {
			$startTime = strtotime($date);
			$endTime = $startTime + 86400;
			$where['add_time'] = array('BETWEEN', array($startTime, $endTime));
		}
		if ( !empty($startTime) && !empty($endTime) ) {
			$where['add_time'] = array('BETWEEN', array($startTime, $endTime));
		}
		// 配送方式
		if ( $expressType != '-1' ) {
			$where['express_type'] = $expressType;
		}
		$where['is_split'] = array('IN', '0,1');
		if ($userType == 'buyer') {
			$where['user_id'] = $userId;
		} else {
			$where['supplier_id'] = $agentId;
		}
		$count = 0;
		if ( !empty($type) ) {
			$count = $this->where($where)->count();
		}
		$field = '`id`, `user_id`, `order_sn`, `total`, `carriage`, `status`, `express_type`, `buyer_message`, `remark`, `add_time`';
		$list = $this->where($where)->order('id DESC')->field($field)->limit($limitStart.','.$limit)->select();
		$mapField = '`goods_id`, `goods_image`, `goods_name`, `unit_price`, `price`, `goods_number`, `attr_list`, `is_comment`';
		foreach ($list as $key => &$value) {
			$map['order_sn'] = $value['order_sn'];
			$value['goodsList'] = $orderDetailModel->where($map)->field($mapField)->select();
			if ($userType == 'buyer') {
				
			} else {
				$value['nickname'] = getNickname($value['user_id']);
			}
			$value['goods_number'] = 0;
			foreach ($value['goodsList'] as $k => &$v) {
                //获取商品属性sku
                // $v['attr_list'] = trim($v['attr_list'], ',');
                // if (str_replace(',', '', $v['attr_list']) != '') {
                //     $sql = "SELECT `gan`.`attr_name`, `gav`.`attr_value`
                //             FROM `{$this->dbPrefix}goods_attr_value` AS `gav`
                //             LEFT JOIN `{$this->dbPrefix}goods_attr_name` AS `gan` ON `gav`.`attr_name_id` = `gan`.`id`
                //             WHERE `gav`.`id` IN ({$v['attr_list']}) AND `gan`.`is_relation` = '1'";
                //     $attrList = M()->query($sql);
                //     $v['attr_list'] = empty($attrList) ? '' : $attrList;
                // } else {
                // 	$v['attr_list'] = '';
                // }
                $value['goods_number'] += $v['goods_number'];
            }
		}
		$returnData['list']			= $list;
        $returnData['page']         = $page + 1;  
		$returnData['count']        = ceil($count / $limit);
		return $returnData;
	}

	/**
	 * [checkGoods 返回商品信息]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)      2016          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $goodsId [description]
	 * @return    [type]                 [description]
	 */
	public function checkGoods($goodsId) {
		$goodsModel = M('goods');
		$list = $goodsModel->where(array('id'=> array('IN', $goodsId)))->field('`id`, `goods_number`, `goods_name`, `goods_image`, `goods_price`, `shop_id`')->select();
		if (!empty($list)) {
			return $list;
		} else {
			return false;
		}
	}

	/**
	 * [getOrderDetail 获取订单详情]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $parameter [description]
	 * @return    [type]                   [description]
	 */
	public function getOrderDetail($parameter) 
	{
		$userId = !empty($parameter['userId']) ? $parameter['userId'] : '';
		$order_sn = !empty($parameter['order_sn']) ? $parameter['order_sn'] : '';
		$longitude = !empty($parameter['longitude']) ? $parameter['longitude'] : '113.37763';
		$latitude = !empty($parameter['latitude']) ? $parameter['latitude'] : '23.13275';
		$userModel = M('user');
		$orderDetailModel = M('order_detail');
		$where = array(
			// 'user_id' => $userId,
			'order_sn' => $order_sn
		);
		$field = '`user_id`, `order_sn`, `consignee`, `telephone`, `province`, `city`, `county`, `address`, `giving_point`, `total`, `express_type`, `express_content`, `message`, `buyer_message`, `status`, `remark`, `add_time`, `pay_time`';
		$orderData = $this->where($where)->field($field)->find();
		if ( empty($orderData) ) {
			return array(
				'status' => '400000'
			);
		}
		// unset($where['user_id']);
		$field = '`goods_id`, `goods_name`, `goods_image`, `unit_price`, `price`, `attr_list`, `goods_number`, `is_comment`';
		$goodsData = $orderDetailModel->where($where)->field($field)->select();
		$goodsData = $this->processData($orderData, $goodsData, $longitude, $latitude);
		$userData = $userModel->where(array('id'=> $orderData['user_id']))->field('`credit`, `nickname`')->find();
		$returnData['orderData'] = $orderData;
		$returnData['goodsData'] = $goodsData;
		$returnData['userData'] = $userData;
		return $returnData;
	}

	private function processData(&$orderData, $goodsData, $longitude, $latitude) {
		$goodsModel = M('goods');
		$agentModel = D('Agent');
		$goodsCommentModel = D('GoodsComment');
		$userModel = M('user');
		$exists = array();
		$expressData = array();
		$messageData = array();
		$expressContent = json_decode($orderData['express_content'], true);
		$message = json_decode($orderData['message'], true);
		foreach ($expressContent as $key => $value) {
			$expressData[$value['agent_id']] = $value['express_type'];
		}
		foreach ($message as $key => $value) {
			$messageData[$value['agent_id']] = $value['message'];
		}

		foreach ($goodsData as $key => &$value) {
			$where['id'] = $value['goods_id'];
			$supplier_id = $goodsModel->where($where)->getField('`supplier_id`');

			// 对同店铺进行归类
			if ( !in_array($supplier_id, $exists) ) {
	        	$distance = $agentModel->calc($longitude, $latitude, '`longitude`', '`latitude`');
				$data = $agentModel->field('`user_id`, `agent_name`, `logo`, `longitude`, `latitude`, `star`, '. $distance .'AS `distance`')->find($supplier_id);
				$data['credit'] = $userModel->where(array('id'=> $data['user_id']))->getField('`credit`');
				$data['distance'] = round($data['distance'] / 1000, 1);
				$list[$supplier_id] = $data;
				$exists[] = $supplier_id;
			}

			// 商品评价
			$value['commentData'] = $goodsCommentModel->where(['order_sn'=>$orderData['order_sn'], 'goods_id'=>$value['goods_id']])->field('`contain`, `reply_contain`, `add_time`')->find();

			$list[$supplier_id]['goodsList'][] = $value;
			$list[$supplier_id]['express_type'] = $expressData[$supplier_id];
			$list[$supplier_id]['message'] = !empty($messageData[$supplier_id]) ? $messageData[$supplier_id] : '';
		}
		unset($orderData['express_content'], $orderData['express_type'], $orderData['message']);
		sort($list);
		return $list;
	}

	/**
	 * [getPurchaseHistory 获得购买记录]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $parameter [description]
	 * @return    [type]                   [description]
	 */
	public function getPurchaseHistory($parameter) {
		$userId = $parameter['userId'];
		$limitStart = !empty($parameter['limitStart']) ? $parameter['limitStart'] : 0;
		$limit = !empty($parameter['limit']) ? $parameter['limit'] : 10;
		$sql = "SELECT `o`.`order_sn`, `od`.`goods_id`, `od`.`goods_name`, `od`.`goods_image`, `od`.`unit_price`, `od`.`price`, `od`.`goods_number`, `od`.`is_comment`, `od`.`attr_list`, `od`.`add_time` 
				FROM `{$this->dbPrefix}order_detail` AS `od` 
				LEFT JOIN `{$this->dbPrefix}order` AS `o` ON `od`.`order_sn` = `o`.`order_sn` 
				WHERE `o`.`user_id` = '{$userId}' 
				AND `o`.`is_pay` = '1' 
				AND `o`.`delivery_status` = '1' 
				AND `o`.`received` = '1' 
				ORDER BY `od`.`add_time` DESC 
				LIMIT {$limitStart} , {$limit}";
		$list = M()->query($sql);
		$returnData = array();
		foreach ($list as $key => $value) {
			$time = intval(date('Ymd', $value['add_time']));
			$returnData[$time]['goods'][] = $value;
			$returnData[$time]['time'] = $value['add_time'];
		}
		rsort($returnData);
		return $returnData;
	}
}
