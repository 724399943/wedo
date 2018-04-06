<?php
namespace Shop\Model;
use Think\Model;

class GoodsShoppingCartModel extends Model {
	private $dbPrefix;
	public function __construct() {
		parent::__construct();
		$this->dbPrefix = C('DB_PREFIX');
	}

	/**
	 * [checkGoods 检测商品库存]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)          2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $goodsId     [description]
	 * @param     [type]        $goodsNumber [description]
	 * @return    [type]                     [description]
	 */
	public function checkGoods($goodsId, $goodsNumber, $userId) {
		$where = array(
			'user_id' => $userId,
			'goods_id' => $goodsId
		);
		$shoppingCartNumber = $this->where($where)->getField('goods_number');
		$shoppingCartNumber = !empty($shoppingCartNumber) ? $shoppingCartNumber : 0;
		$goodsDetail = M('goods')->field('`goods_number`, `goods_price`, `is_on_sale`, `is_delete`')->find($goodsId);
		$stockNumber = !empty($goodsDetail['goods_number']) ? $goodsDetail['goods_number'] : 0;
		$goodsNumber = !empty($goodsNumber) ? $goodsNumber + $shoppingCartNumber : 1 + $shoppingCartNumber;
		$return = array(
			'status' => '200000',
			'goodsDetail' => $goodsDetail
		);
		if (empty($goodsDetail)) {
			$return = array(
				'status' => '400000',
				'message' => L('_PC_USER_GOODS_EXISTS_')
			);
        }
        if (empty($goodsDetail['is_on_sale']) || $goodsDetail['is_delete'] == '1') {
        	$return = array(
				'status' => '400000',
				'message' => L('module_point_order_off_the_shelves')
			);
        }
		if ($stockNumber < $goodsNumber) {
			$return = array(
				'status' => '400000',
				'message' => L('module_point_order_in_short_stock')
			);
		}
		return $return;
	}

	/**
	 * [goodsInc 存在商品递增]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)          2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $goodsId     [description]
	 * @param     [type]        $userId      [description]
	 * @param     [type]        $goodsNumber [description]
	 * @return    [type]                     [description]
	 */
	public function goodsInc($goodsId, $userId, $goodsNumber) {
		$sql = "UPDATE `{$this->dbPrefix}goods_shopping_cart` 
				SET `goods_number` = `goods_number` + '{$goodsNumber}' 
				WHERE `user_id` = '{$userId}' 
				AND `goods_id` = '{$goodsId}'";
		$result = M()->execute($sql);
		if ($result !== false) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * [getShoppingCart 购物车]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)     2016          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $userId [description]
	 * @return    [type]                [description]
	 */
	public function getShoppingCart($userId) {
		$sql = "SELECT `sc`.`id`, `sc`.`goods_number`, ROUND(`sc`.`goods_number` * `g`.`goods_price`, 2) AS `total`, ROUND(`sc`.`goods_number` * `g`.`favorable_price`, 2) AS `favorable_total`, `g`.`id` AS `goods_id`, `g`.`goods_image`, `g`.`goods_name`, `g`.`goods_price`, `g`.`favorable_price`, `g`.`goods_number` AS `stock_number`, `g`.`relevance_id`, `g`.`category_id`, `g`.`attr_list`, `g`.`agent_id`, `g`.`express_type`, `g`.`agent_category_id`, `g`.`favorable_start_time`, `g`.`favorable_end_time` 
				FROM `{$this->dbPrefix}goods_shopping_cart` AS `sc` 
				LEFT JOIN `{$this->dbPrefix}goods` AS `g` ON `sc`.`goods_id` = `g`.`id` 
				WHERE `sc`.`user_id` = '{$userId}' 
				AND `g`.`is_delete` = '0'
                AND `g`.`is_on_sale` = '1' 
                AND `sc`.`is_buy` = '0'";
		$shoppingCart = M()->query($sql);
		$total = 0;
		$time = time();
		$exists = array();
		$list = array();
		$agentModel = M('agent');
		foreach ($shoppingCart as $key => $value) {
			$is_favorable = ( $time >= $value['favorable_start_time'] && $time <= $value['favorable_end_time'] ) ? '1' : '0';
			$value['is_favorable'] = $is_favorable;
			// 处理商品属性
			$value['attrList'] = $this->attrlistToSku($value['attr_list']);

			// 对同店铺进行归类
			if (!in_array($value['agent_id'], $exists)) {
				$info = $agentModel->field('`agent_name`, `logo`')->find($value['agent_id']);
				$list[$value['agent_id']] = $info;
				$exists[] = $value['agent_id'];
			}
			$list[$value['agent_id']]['goodsList'][] = $value;
			$total += $is_favorable == '1' ? $value['favorable_total'] : $value['total'];
		}
		sort($list);
		$return['list'] = $list;
		$return['total'] = sprintf("%.2f", substr(sprintf("%.3f", $total), 0, -1));
		return $return;
	}

	/**
	 * [toBeOrder 待下单操作]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)     2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $ids     	[description]
	 * @param     [type]        $userId 	[description]
	 * @return    [type]                	[description]
	 */
	public function toBeOrder($ids, $userId) {
		$userModel 	= M('user');
		$agentModel = M('agent');
		$userConsigneeModel = D('UserConsignee');
		$exists = array();
		$list = array();
		$return['status'] = '200000';
		if (empty($ids)) {
			return array(
				'status' 	=> '400000',
				'message'	=> L('module_point_order_select_to_settled')
			);
        }
		$ids = trim($ids, ',');
		$sql = "SELECT `sc`.`id`, `sc`.`goods_number`, ROUND(`sc`.`goods_number` * `g`.`goods_price`, 2) AS `total`, ROUND(`sc`.`goods_number` * `g`.`favorable_price`, 2) AS `favorable_total`, `g`.`id` AS `goods_id`, `g`.`goods_image`, `g`.`goods_name`, `g`.`goods_price`, `g`.`favorable_price`, `g`.`goods_number` AS `stock_number`, `g`.`relevance_id`, `g`.`category_id`, `g`.`attr_list`, `g`.`agent_id`, `g`.`is_delete`, `g`.`is_on_sale`, `g`.`express_type`, `g`.`favorable_start_time`, `g`.`favorable_end_time` 
				FROM `{$this->dbPrefix}goods_shopping_cart` AS `sc` 
				LEFT JOIN `{$this->dbPrefix}goods` AS `g` ON `sc`.`goods_id` = `g`.`id` 
				WHERE `sc`.`user_id` = '{$userId}' 
				AND `g`.`is_delete` = '0' 
                AND `g`.`is_on_sale` = '1' 
                AND `sc`.`id` IN ({$ids})";
        $shoppingCart = M()->query($sql);
        if (empty($shoppingCart)) {
        	return array(
				'status' 	=> '400000',
				'message'	=> L('module_point_order_select_to_settled')
			);
        }
        $total = 0;
        $time = time();
		foreach ($shoppingCart as $key => $value) {
			if ($value['is_delete'] == '1' || $value['is_on_sale'] == '0') {
				return array(
					'status' 	=> '400000',
					'message'	=> "【{$value['goods_name']}】" . L('module_point_order_off_the_shelves')
				);
            }
            if ($value['goods_number'] > $value['stock_number']) {
            	return array(
					'status' 	=> '400000',
					'message'	=> "【{$value['goods_name']}】" . L('module_shopping_cart_out_of_stock')
				);
            }
            $value['is_reach'] = 0;
            $value['is_visit'] = 0;
            if ( strpos($value['express_type'], '0') !== false ) {
            	$value['is_visit'] = 1;
            }
            if ( strpos($value['express_type'], '1') !== false ) {
            	$value['is_reach'] = 1;
            }

            //获取商品属性sku
            $value['attrList'] = $this->attrlistToSku($value['attr_list']);
			
			// 对同店铺进行归类
			if (!in_array($value['agent_id'], $exists)) {
				$info = $agentModel->field('`agent_name`, `logo`')->find($value['agent_id']);
				$list[$value['agent_id']] = $info;
				$exists[] = $value['agent_id'];
			}

			$is_favorable = ( $time >= $value['favorable_start_time'] && $time <= $value['favorable_end_time'] ) ? '1' : '0';
			$value['is_favorable'] = $is_favorable;

			$list[$value['agent_id']]['goodsList'][] = $value;
			$list[$value['agent_id']]['agent_total'] += ( $time >= $value['favorable_start_time'] && $time <= $value['favorable_end_time'] ) ? $value['favorable_total'] : $value['total'];
			$list[$value['agent_id']]['agent_count'] += 1;
			$total += $is_favorable == '1' ? $value['favorable_total'] : $value['total'];
		}
		sort($list);
		// 用户是否有默认地址
		$defaultId = $userModel->where(array('id'=> $userId))->getField('default_id');
		if (!empty($defaultId)) {
			$consignee = $userConsigneeModel->find($defaultId);
		} else {
			$consignee = $userConsigneeModel->order('id DESC')->limit(1)->select();
			$consignee = !empty($consignee[0]) ? $consignee[0] : '';
		}

		if ($consignee) {
			$consignee['province_name'] = getRegionName($consignee['province']);
			$consignee['city_name'] = getRegionName($consignee['city']);
			$consignee['county_name'] = getRegionName($consignee['county']);
		}
		$return['consignee'] = $consignee;
		$return['list'] = $list;
		$return['total'] = sprintf("%.2f", substr(sprintf("%.3f", $total), 0, -1));
		return $return;
	}

	/**
	 * [attrlistToSku description]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)       2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $attrList [description]
	 * @return    [type]                  [description]
	 */
	private function attrlistToSku($attrList) {
		$attrList = trim($attrList, ',');
        if (str_replace(',', '', $attrList) != '') {
            $sql = "SELECT `gan`.`attr_name`, `gav`.`attr_value`
                    FROM `{$this->dbPrefix}goods_attr_value` AS `gav`
                    LEFT JOIN `{$this->dbPrefix}goods_attr_name` AS `gan` ON `gav`.`attr_name_id` = `gan`.`id`
                    WHERE `gav`.`id` IN ({$attrList}) AND `gan`.`is_relation` = '1'";
            $query = M()->query($sql);
            $result = empty($query) ? array() : $query;
        } else {
            $result = array();
        }
        return $result;
	}

	/**
	 * [toBeCommit 下单]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
	public function toBeCommit($parameter) 
	{
		$ids = $parameter['ids'];
		$userId = $parameter['userId'];
		$expressContent = !empty($parameter['expressContent']) ? json_decode($parameter['expressContent'], true) : '';
		$consigneeId = $parameter['consigneeId'];
		$userModel 	= M('user');
		$userConsigneeModel = D('UserConsignee');
		$list = array();
		$return['status'] = '200000';
		if ( empty($ids) ) {
			return array(
				'status' 	=> '400000',
				'message'	=> L('module_point_order_select_to_settled')
			);
        }
        if ( empty($expressContent) ) {
			return array(
				'status' 	=> '400000',
				'message'	=> '请选择配送方式！'
			);
        }

		$ids = trim($ids, ',');
		$sql = "SELECT `sc`.`id`, `sc`.`goods_number`, ROUND(`sc`.`goods_number` * `g`.`goods_price`, 2) AS `total`, ROUND(`sc`.`goods_number` * `g`.`favorable_price`, 2) AS `favorable_total`, `sc`.`goods_id`, `g`.`goods_image`, `g`.`goods_name`, `g`.`goods_price`, `g`.`favorable_price`, `g`.`goods_number` AS `stock_number`, `g`.`relevance_id`, `g`.`category_id`, `g`.`attr_list`, `g`.`agent_id`, `g`.`is_delete`, `g`.`is_on_sale`, `g`.`express_type`, `g`.`favorable_start_time`, `g`.`favorable_end_time` 
				FROM `{$this->dbPrefix}goods_shopping_cart` AS `sc` 
				LEFT JOIN `{$this->dbPrefix}goods` AS `g` ON `sc`.`goods_id` = `g`.`id` 
				WHERE `sc`.`user_id` = '{$userId}' 
				AND `g`.`is_delete` = '0' 
                AND `g`.`is_on_sale` = '1' 
                AND `sc`.`id` IN ({$ids})";
        $shoppingCart = M()->query($sql);
        if (empty($shoppingCart)) {
        	return array(
				'status' 	=> '400000',
				'message'	=> L('module_point_order_select_to_settled')
			);
        }

        $total = 0;
        $expressData = array();
        foreach ($expressContent as $key => $value) {
        	if ( $value['express_type'] == '0' && empty($consigneeId) ) {
        		return array(
					'status' 	=> '400000',
					'message'	=> L('module_shopping_cart_select_address')
				);
        		break;
        	}
        	$expressData[$value['agent_id']] = $value['express_type'];
        }

        $time = time();
		foreach ($shoppingCart as $key => $value) {
			if ($value['is_delete'] == '1' || $value['is_on_sale'] == '0') {
				return array(
					'status' 	=> '400000',
					'message'	=> "【{$value['goods_name']}】" . L('module_point_order_off_the_shelves')
				);
            }
            if ($value['goods_number'] > $value['stock_number']) {
            	return array(
					'status' 	=> '400000',
					'message'	=> "【{$value['goods_name']}】" . L('module_shopping_cart_out_of_stock')
				);
            }

            if ( strpos($value['express_type'], $expressData[$value['agent_id']]) !== false ) {
            	$attrList = $this->attrlistToSku($value['attr_list']);
            	$saveAttr = '';
            	if ( !empty($attrList) ) {
            		foreach ($attrList as $attr_key => $attr_value) {
            			$saveAttr .= $attr_value['attr_name'] . ':' . $attr_value['attr_value'] . ';';
            		}
            	}
            	$is_favorable = ( $time >= $value['favorable_start_time'] && $time <= $value['favorable_end_time'] ) ? '1' : '0';
				$list[] = array(
					'goods_id'		=> $value['goods_id'],
					'goods_name'	=> $value['goods_name'],
					'goods_image'	=> $value['goods_image'],
					'unit_price'	=> $is_favorable == '1' ? $value['favorable_price'] : $value['goods_price'],
					'price'			=> $is_favorable == '1' ? $value['favorable_total'] : $value['total'],
					'goods_number'	=> $value['goods_number'],
					'attr_list'		=> trim($saveAttr, ';'),
					'add_time'		=> time()
				);
				$total += $is_favorable == '1' ? $value['favorable_total'] : $value['total'];
				unset($attrList);
            }
		}
		sort($list);

		$consignee = $userConsigneeModel->find($consigneeId);
		if ($consignee) {
			$consignee['province'] = getRegionName($consignee['province']);
			$consignee['city'] = getRegionName($consignee['city']);
			$consignee['county'] = getRegionName($consignee['county']);
		}

		$return['consignee'] = $consignee;
		$return['list'] = $list;
		$return['total'] = sprintf("%.2f", substr(sprintf("%.3f", $total), 0, -1));
		return $return;
	}
}
