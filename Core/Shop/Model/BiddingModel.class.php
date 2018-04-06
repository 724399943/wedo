<?php
namespace Shop\Model;
use Think\Model;
use Plugins\Money\Money;

class BiddingModel extends Model {
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
		// 首页商品竞价
		array('goods_id', 'require', '{%module_bidding_select_goods}', 1, 'regex', 4),
		array('start_time', 'require', '{%module_bidding_select_date}', 1, 'regex', 4),
		array('start_time', checkStartTime, '{%module_bidding_select_greater_date}', 1, 'callback', 4),
		// array('total', 'require', '{%module_bidding_enter_amount}', 1, 'regex', 4),
		// array('total', 'is_numeric', '{%module_bidding_amount_must_be_number}', 1, 'function', 4),

		// 优惠商品竞价
		array('goods_id', 'require', '{%module_bidding_select_goods}', 1, 'regex', 5),
		array('favorable_price', 'require', '{%module_bidding_discount_amount}', 1, 'regex', 5),
		array('start_time', 'require', '{%module_bidding_select_date}', 1, 'regex', 5),
		array('start_time', checkStartTime, '{%module_bidding_select_greater_date}', 1, 'callback', 5),
		// array('total', 'require', '{%module_bidding_enter_amount}', 1, 'regex', 5),
		// array('total', 'is_numeric', '{%module_bidding_amount_must_be_number}', 1, 'function', 5),

		// 商家竞价
		array('start_time', 'require', '{%module_bidding_select_date}', 1, 'regex', 6),
		array('start_time', checkStartTime, '{%module_bidding_select_greater_date}', 1, 'callback', 6),
		// array('total', 'require', '{%module_bidding_enter_amount}', 1, 'regex', 6),
		// array('total', 'is_numeric', '{%module_bidding_amount_must_be_number}', 1, 'function', 6),

		// 广告位竞价
		array('start_time', 'require', '{%module_bidding_select_date}', 1, 'regex', 7),
		array('start_time', checkStartTime, '{%module_bidding_select_greater_date}', 1, 'callback', 7),
		array('image', 'require', '{%module_bidding_advertisement_photo}', 1, 'regex', 7),
		// array('total', 'require', '{%module_bidding_enter_amount}', 1, 'regex', 7),
		// array('total', 'is_numeric', '{%module_bidding_amount_must_be_number}', 1, 'function', 7),
	);

	/**
	 * [_auto 自动完成]
     * @author kofu <[418382595@qq.com]>
	 */
	protected $_auto = array(
		// 首页商品竞价
		array('add_time', 'time', 4, 'function'),
		array('bidding_type', 0, 4),
		array('start_time', 'strtotime', 4, 'function'),
		array('end_time', getEndtime, 4, 'callback'),

		// 优惠商品竞价
		array('add_time', 'time', 5, 'function'),
		array('bidding_type', 1, 5),
		array('start_time', 'strtotime', 5, 'function'),
		array('end_time', getEndtime, 5, 'callback'),

		// 商家竞价
		array('add_time', 'time', 6, 'function'),
		array('bidding_type', 2, 6),
		array('start_time', 'strtotime', 6, 'function'),
		array('end_time', getEndtime, 6, 'callback'),

		// 商家竞价
		array('add_time', 'time', 7, 'function'),
		array('bidding_type', 3, 7),
		array('start_time', 'strtotime', 7, 'function'),
		array('end_time', getEndtime, 7, 'callback'),
	);

	public function checkStartTime($value) {
		$time = intval(date('Ymd', time()));
		$startTime = intval(date('Ymd', strtotime($value)));
		if ( $startTime > $time ) {
			return true;
		} else {
			return false;
		}
	}

	public function getEndtime() {
		$startTime = I('post.start_time');
		return strtotime($startTime) + 86400;
	}

	/**
	 * [getNotBiddingGoods 获得未竞价商品]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
	public function getNotBiddingGoods($parameter) {
		$goodsModel = M('goods');
		$agentId = $parameter['agentId'];
		$bidding_type = $parameter['bidding_type'];
		$keyword = !empty($parameter['keyword']) ? $parameter['keyword'] : '';
		$category_id = !empty($parameter['category_id']) ? $parameter['category_id'] : '';
		$agent_category_id = !empty($parameter['agent_category_id']) ? $parameter['agent_category_id'] : '';
		$type = !empty($parameter['type']) ? $parameter['type'] : 0;
		$page = !empty($parameter['page']) ? $parameter['page'] : 0;
		$limitStart = !empty($parameter['limitStart']) ? $parameter['limitStart'] : 0;
		$limit = !empty($parameter['limit']) ? $parameter['limit'] : 10;
		$where = array(
			'bidding_type' => $bidding_type,
			'agent_id' => $agentId,
			'status' => array('NEQ', '2')
		);
		$goods = $this->where($where)->select();
		unset($where);
		$where = array(
			'agent_id' => $agentId,
			'goods_main_id' => '0'
		);
		if ( !empty($goods) ) {
			$goodsIds = array_column($goods, 'goods_id');
			$where['id'] = array('NOTIN', implode(',', $goodsIds));
		}
		// 商品名称
		if ( !empty($keyword) ) {
			$where['goods_name'] = array('LIKE', "%{$keyword}%");
		}
		// 平台分类
		if ( !empty($category_id) ) {
			$where['category_id'] = $category_id;
		}
		// 店内分类
		if ( !empty($agent_category_id) ) {
			$where['agent_category_id'] = $agent_category_id;
		}

		$field = '`id`, `category_id`, `agent_category_id`, `goods_name`, `goods_price`, `goods_image`, `introduction`, `add_time`';
		$list = $goodsModel->where($where)->field($field)->limit($limitStart.','.$limit)->select();
		$this->processData($list);
		$count = 0;
		if ( !empty($type) ) {
			$count = $goodsModel->where($where)->count();
		}
		$returnData['list'] 	= $list;
		$returnData['page'] 	= $page + 1;
		$returnData['count'] 	= ceil($count / $limit);
		return $returnData;
	}

	private function processData(&$list) {
		foreach ($list as $key => &$value) {
			$value['category_name'] = getCategoryName($value['category_id']);
			$value['agent_category_name'] = getAgentCategoryName($value['agent_category_id']);
		}
	}

	/**
	 * [getBiddingGoodsRecord 首页商品/优惠商品竞价记录]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $parameter [description]
	 * @return    [type]                   [description]
	 */
	public function getBiddingGoodsRecord($parameter) {
		$agentId = $parameter['agentId'];
		$goods_name = !empty($parameter['goods_name']) ? $parameter['goods_name'] : '';
		$category_id = !empty($parameter['category_id']) ? $parameter['category_id'] : '';
		$agent_category_id = !empty($parameter['agent_category_id']) ? $parameter['agent_category_id'] : '';
		$bidding_type = !empty($parameter['bidding_type']) ? $parameter['bidding_type'] : 0;
		$banner_type = $parameter['banner_type'] != '' ? $parameter['banner_type'] : '-1';
		$status = $parameter['status'] != '' ? $parameter['status'] : '-1';
		$recordType = $parameter['recordType']; // 0：店铺竞价记录，1：平台竞价记录
		$type = !empty($parameter['type']) ? $parameter['type'] : 0;
		$page = !empty($parameter['page']) ? $parameter['page'] : 0;
		$limitStart = !empty($parameter['limitStart']) ? $parameter['limitStart'] : 0;
		$limit = !empty($parameter['limit']) ? $parameter['limit'] : 10;
		$whereStr = '';
		if ( !empty($goods_name) ) {
			$whereStr .= " AND `g`.`goods_name` LIKE '%{$goods_name}%'";
		}
		if ( !empty($category_id) ) {
			$whereStr .= " AND `g`.`category_id` = '{$category_id}'";
		}
		if ( !empty($agent_category_id) ) {
			$whereStr .= " AND `g`.`agent_category_id` = '{$agent_category_id}'";
		}
		if ( $recordType == '0' ) {
			$whereStr .= " AND `big`.`agent_id` = '". $agentId ."'";
		}
		if ( $banner_type != '-1' ) {
			$whereStr .= " AND `big`.`banner_type` = '". $banner_type ."'";
		}
		if ( $status != '-1' ) {
			$whereStr .= " AND `big`.`status` = '". $status ."'";
		}
		$sql = "SELECT `big`.`id`, `big`.`agent_id`, `big`.`total`, `big`.`start_time`, `big`.`end_time`, `big`.`status`, `g`.`id` AS `goods_id`, `g`.`goods_name`, `g`.`goods_price`, `g`.`goods_image`, `g`.`introduction`, `g`.`category_id`, `g`.`agent_category_id` 
				FROM `".$this->dbPrefix."bidding` AS `big` 
				LEFT JOIN `".$this->dbPrefix."goods` AS `g` ON `big`.`goods_id` = `g`.`id` 
				WHERE `big`.`is_pay` = '1' 
				AND `big`.`bidding_type` = '".$bidding_type."'".$whereStr." 
				ORDER BY `big`.`id` DESC
				LIMIT ".$limitStart." , ".$limit;
		$list = M()->query($sql);
		$count = 0;
		if ( !empty($type) ) {
			$sql = "SELECT COUNT(*) AS `count` 
				FROM `".$this->dbPrefix."bidding` AS `big` 
				LEFT JOIN `".$this->dbPrefix."goods` AS `g` ON `big`.`goods_id` = `g`.`id` 
				WHERE `big`.`is_pay` = '1' 
				AND `big`.`bidding_type` = '".$bidding_type."'".$whereStr;
			$count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
		}
		$this->processRecordData($bidding_type, $list);
		$returnData['list'] 	= $list;
		$returnData['page'] 	= $page + 1;
		$returnData['count'] 	= ceil($count / $limit);
		return $returnData;
	}

	/**
	 * [getBiddingRecord 推荐店铺/广告位竞价记录]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $parameter [description]
	 * @return    [type]                   [description]
	 */
	public function getBiddingRecord($parameter) {
		$agentId = !empty($parameter['agentId']) ? $parameter['agentId'] : '';
		$agent_name = !empty($parameter['agent_name']) ? $parameter['agent_name'] : '';
		$bidding_type = !empty($parameter['bidding_type']) ? $parameter['bidding_type'] : 0;
		$banner_type = $parameter['banner_type'] != '' ? $parameter['banner_type'] : '-1';
		$status = $parameter['status'] != '' ? $parameter['status'] : '-1';
		$recordType = $parameter['recordType']; // 0：店铺竞价记录，1：平台竞价记录
		$type = !empty($parameter['type']) ? $parameter['type'] : 0;
		$page = !empty($parameter['page']) ? $parameter['page'] : 0;
		$limitStart = !empty($parameter['limitStart']) ? $parameter['limitStart'] : 0;
		$limit = !empty($parameter['limit']) ? $parameter['limit'] : 10;
		$whereStr = '';
		if ( !empty($agent_name) ) {
			$whereStr .= " AND `a`.`agent_name` LIKE '%{$agent_name}%'";
		}
		if ( $recordType == '0' ) {
			$whereStr .= " AND `big`.`agent_id` = '". $agentId ."'";
		}
		if ( $banner_type != '-1' ) {
			$whereStr .= " AND `big`.`banner_type` = '". $banner_type ."'";
		}
		if ( $status != '-1' ) {
			$whereStr .= " AND `big`.`status` = '". $status ."'";
		}
		$sql = "SELECT `big`.`id`, `big`.`agent_id`, `big`.`goods_id`, `big`.`total`, `big`.`start_time`, `big`.`end_time`, `big`.`status`, `a`.`agent_name`, `a`.`introduction` 
				FROM `".$this->dbPrefix."bidding` AS `big` 
				LEFT JOIN `".$this->dbPrefix."agent` AS `a` ON `big`.`agent_id` = `a`.`id` 
				WHERE `big`.`is_pay` = '1' 
				AND `big`.`bidding_type` = '".$bidding_type."'".$whereStr." 
				ORDER BY `big`.`id` DESC
				LIMIT ".$limitStart." , ".$limit;
		$list = M()->query($sql);
		$count = 0;
		if ( !empty($type) ) {
			$sql = "SELECT COUNT(*) AS `count` 
				FROM `".$this->dbPrefix."bidding` AS `big` 
				LEFT JOIN `".$this->dbPrefix."agent` AS `a` ON `big`.`agent_id` = `a`.`id` 
				WHERE `big`.`is_pay` = '1' 
				AND `big`.`bidding_type` = '".$bidding_type."'".$whereStr;
			$count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
		}
		$this->processRecordData($bidding_type, $list);
		$returnData['list'] 	= $list;
		$returnData['page'] 	= $page + 1;
		$returnData['count'] 	= ceil($count / $limit);
		return $returnData;
	}

	private function processRecordData($bidding_type, &$list) {
		$goodsModel = M('goods');
		$agentModel = M('agent');
		$agentCategoryModel = M('agent_category');
		$agentCategoryRelevanceModel = M('agent_category_relevance');
		foreach ($list as $key => &$value) {
			switch ($bidding_type) {
				case '0':
				case '1':
					$agentInfo = $agentModel->field('`agent_name`, `introduction`')->find($value['agent_id']);
					$value['agent_name'] = $agentInfo['agent_name'];
					$value['category_name'] = getCategoryName($value['category_id']);
					$value['agent_category_name'] = getAgentCategoryName($value['agent_category_id']);
					break;
				case '2':
					$idData = $agentCategoryRelevanceModel->where(array('agent_id'=> $value['agent_id']))->select();
					if ( !empty($idData) ) {
						$idData = implode(',', array_column($idData, 'category_id'));
						$findData = $agentCategoryModel->where(array('id'=> array('IN', $idData)))->field('GROUP_CONCAT(`category_name`) AS `category_name`')->find();
					}
					$value['agent_category_name'] = $findData['category_name'];
					break;
				case '3':
					$value['goods_name'] = $goodsModel->where(array('id'=> $value['goods_id']))->getField('`goods_name`');
					break;
			}
		}
	}

	/**
	 * [payForBidding 支付竞价]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $parameter [description]
	 * @return    [type]                   [description]
	 */
	public function payForBidding($parameter) {
		$userId = $parameter['userId'];
		$order = $parameter['order'];
        $userMoney = M('user')->where(array('id'=> $userId))->getField('money');
        if ( $userMoney < $order['total'] ) {
        	return array(
        		'status' => '400000',
        		'message' => L('module_bidding_balance_issufficient'),
        	);
        }

        $result = true;
        $this->startTrans();
        $money = new Money();
        $parameter = array(
        	'from_id' => 0,
			'to_id' => $userId,
			'money' => $order['total'],
			'type' => 1,
			'eventType' => 5
        );
        if ( $money->money($parameter) === false ) {
        	$result = false;
        	return array(
        		'status' => '400000',
        		'message' => '扣除余额失败',
        	);
        }

        $order['is_pay'] = 1;
        if ( $this->save($order) === false ) {
        	$result = false;
        	return array(
        		'status' => '400000',
        		'message' => '更新订单失败',
        	);	
        }

        if ( $result === true ) {
        	$this->commit();
        	return array(
        		'status' => '200000'
        	);
        } else {
        	$this->rollback();
        }
	}

	/**
     * [makeBiddingSn 生成订单号]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function makeBiddingSn() {
        // //订单生成规则
        $orderId = $this->field('max(id) as `maxId`')->find(); //得到订单的最大自增ID
        $tempStr = rand(1000, 9999) . $orderId['maxId'];        //生成随机数
        $masterOrder = C('CHANNEL') . date('ymd') . str_pad($tempStr, 10, "0", STR_PAD_LEFT);          //合并成订单号

        //如果有重复的订单号的话，则回滚
        if($this->where(array('order_sn'=> $masterOrder))->count() > 0) {
            return $this->makeOrderSn();
        }
        return $masterOrder;
    }
}
