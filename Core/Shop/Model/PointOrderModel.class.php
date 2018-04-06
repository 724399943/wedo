<?php
namespace Shop\Model;
use Think\Model;

class PointOrderModel extends Model {
	private $dbPrefix;
	public function __construct() {
		parent::__construct();
		$this->dbPrefix = C('DB_PREFIX');
	}

	/**
	 * [pointOrder 积分订单]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $parameter [description]
	 * @return    [type]                   [description]
	 */
	public function pointOrder($parameter) {
        $userId = !empty($parameter['userId']) ? $parameter['userId'] : '';
		$agentId = !empty($parameter['agentId']) ? $parameter['agentId'] : '';
		$type = !empty($parameter['type']) ? $parameter['type'] : '0';
		$limitStart = !empty($parameter['limitStart']) ? $parameter['limitStart'] : 0;
		$limit = !empty($parameter['limit']) ? $parameter['limit'] : 10;
        $where['is_pay'] = '1';
        if ( !empty($userId) ) {
            $where['user_id'] = $userId;
        }
        if ( !empty($agentId) ) {
            $where['agent_id'] = $agentId;
        }
        
        switch ($type) {
        	// 待发货
            case 1:
                $where['delivery_status'] = 0;
                $where['received'] = 0;
                break;
            // 待收货
            case 2:
                $where['delivery_status'] = 1;
                $where['received'] = 0;
                break;
            // 已完成
            case 3:
                $where['delivery_status'] = 1;
                $where['received'] = 1;
                break;
        }

        $field = '`id`, `user_id` , `order_sn`, `delivery_status`, `received`, `total`, `consignee`, `add_time`, `is_pay`, `delivery_status`, `received`, `express_type`';
        $list = $this->where($where)->field($field)->order('`add_time` DESC')->limit($limitStart . ',' . $limit)->select();

        foreach ($list as $key => &$value) {
            $sql = "SELECT `od`.`goods_name`, `od`.`goods_image`, `g`.`id` AS `goods_id`, `od`.`price`,`od`.`unit_price`, `od`.`goods_number`, `g`.`sale_number`
                    FROM {$this->dbPrefix}point_order_detail AS `od`
                    LEFT JOIN {$this->dbPrefix}goods AS `g`
                    ON `od`.`goods_id` = `g`.`id`
                    WHERE `od`.`order_sn` = {$value['order_sn']}";
            $value['goodsList'] = M()->query($sql);
            if ( !empty($agentId) ) {
                $value['nickname'] = M('user')->where(array('id'=> $value['user_id']))->getField('nickname');
            }
        }
        return $list;
	}

    /**
     * [orderDetail 订单详情]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
     * @param     [type]        $parameter [description]
     * @return    [type]                   [description]
     */
	public function orderDetail($parameter) {
        $order_sn = !empty($parameter['order_sn']) ? $parameter['order_sn'] : '';
        $agentId = !empty($parameter['agentId']) ? $parameter['agentId'] : '';
        $userId = !empty($parameter['userId']) ? $parameter['userId'] : '';
        $agent = M('agent');
        $pointOrderDetail = M('point_order_detail');
        $where['order_sn'] = $parameter['order_sn'];
        if ( !empty($agentId) ) {
            $where['agent_id'] = $agentId;
        }
        if ( !empty($userId) ) {
            $where['user_id'] = $userId;
        }
        $list = $this->where($where)->field('`agent_id`, `express_type`, `consignee`, `province`, `city`, `county`, `address`, `telephone`')->find();
        if ( !empty($list) ) {
            $list['agent_name'] = getAgentName($list['agent_id'], false);
            $list['goodsList'] = $pointOrderDetail->where(array('order_sn'=> $order_sn))->field('`goods_name`, `price`, `goods_image`')->select();
        }
        return $list;
	}

    /**
     * [pointInfo 结算积分商品页]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)          2017          Xcrozz (http://www.xcrozz.com)
     * @param     [type]        $userId      [description]
     * @param     [type]        $goodsId     [description]
     * @param     [type]        $goodsNumber [description]
     * @return    [type]                     [description]
     */
    public function pointInfo($userId, $goodsId, $goodsNumber) {
        $goods = M('goods');
        if ( empty($goodsId) ) {
            return array(
                'status'    => '400000',
                'message'   => L('module_point_order_select_to_settled')
            );
        }

        $where = array(
            'id' => $goodsId,
            'goods_type' => '1',
            'is_on_sale' => '1',
            'is_delete' => '0'
        );
        $goodsInfo = $goods->where($where)->field('`goods_number`, `goods_price`, `goods_name`, `goods_image`, `agent_id`')->find();

        if ( empty($goodsInfo) ) {
            return array(
                'status'    => '400000',
                'message'   => L('module_point_order_off_the_shelves')
            );
        }
        if ( $goodsInfo['goods_number'] < $goodsNumber ) {
            return array(
                'status'    => '400000',
                'message'   => L('module_point_order_in_short_stock')
            );
        }

        $user = M('user')->field('`point`, `default_id`')->find($userId);
        $consignee = M('user_consignee')->field('`id`, `consignee`, `province`, `city`, `county`, `address`, `telephone`')->find($user['default_id']);
        if ( !empty($consignee) ) {
            $consignee['province_name'] = getRegionName($consignee['province']);
            $consignee['city_name'] = getRegionName($consignee['city']);
            $consignee['county_name'] = getRegionName($consignee['county']);
        }

        $map['id'] = $goodsInfo['agent_id'];
        $goodsInfo['agent_name'] = M('agent')->where($map)->getField('`agent_name`');

        return array(
            'consignee' => $consignee,
            'goodsInfo' => $goodsInfo
        );
    }

    /**
     * [toBeCommit 下单前检测]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
     * @param     [type]        $parameter [description]
     * @return    [type]                   [description]
     */
    public function toBeCommit($parameter) {
        $goods = M('goods');
        $userId = $parameter['userId'];
        $goodsId = $parameter['goodsId'];
        $goodsNumber = $parameter['goodsNumber'];
        $consigneeId = $parameter['consigneeId'];
        $expressType = $parameter['expressType'];
        if ( empty($consigneeId) && $expressType != '1' ) {
            return array(
                'status'    => '400000',
                'message'   => L('module_user_consignee_address')
            );
        }
        
        if ( empty($goodsId) ) {
            return array(
                'status'    => '400000',
                'message'   => L('module_point_order_select_to_settled')
            );
        }

        $where = array(
            'id' => $goodsId,
            'goods_type' => '1',
            'is_on_sale' => '1',
            'is_delete' => '0'
        );
        $goodsInfo = $goods->where($where)->field('`goods_number`, `goods_price`, `goods_name`, `goods_image`, `sale_number`, `agent_id`')->find();

        if ( empty($goodsInfo) ) {
            return array(
                'status'    => '400000',
                'message'   => L('module_point_order_off_the_shelves')
            );
        }
        if ( $goodsInfo['goods_number'] < $goodsNumber ) {
            return array(
                'status'    => '400000',
                'message'   => L('module_point_order_in_short_stock')
            );
        }

        $total  = $goodsInfo['goods_price'] * $goodsNumber;
        $point = M('user')->where(array('id'=> $userId))->getField('point');
        if ($point < $total) 
        {
            return array(
                'status'    => '400000',
                'message'   => L('module_point_order_insufficient_points')
            );
        }

        if ( !empty($consigneeId) ) {
            $consignee = M('user_consignee')->field('`consignee`, `province`, `city`, `county`, `address`, `telephone`')->find($consigneeId);
            $consignee['province_name'] = getRegionName($consignee['province']);
            $consignee['city_name'] = getRegionName($consignee['city']);
            $consignee['county_name'] = getRegionName($consignee['county']);
        } else {
            $consignee = array();
        }

        return array(
            'status' => '200000',
            'goodsInfo' => $goodsInfo,
            'consignee' => $consignee,
            'total' => $total,
        );
    }

    /**
     * [agentToBeCommit 商家下单前检测]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
     * @param     [type]        $parameter [description]
     * @return    [type]                   [description]
     */
    public function agentToBeCommit($parameter) {
        $goods = M('goods');
        $userId = $parameter['userId'];
        $goodsId = $parameter['goodsId'];
        $goodsNumber = $parameter['goodsNumber'];
        $postData = $parameter['postData'];
        $expressType = $parameter['expressType'];
        if ( $expressType != '1' ) {
            if ( empty($postData['consignee']) ) {
                return array(
                    'status'    => '400000',
                    'message'   => L('module_user_consignee_receiver')
                );
            }
            if ( empty($postData['telephone']) ) {
                return array(
                    'status'    => '400000',
                    'message'   => L('module_user_consignee_contact')
                );
            }
            // if ( !$postData['province'] || !$postData['city'] || !$postData['county']) {
            //     return array(
            //         'status'    => '400000',
            //         'message'   => '请选择地址'
            //     );
            // }
            if ( empty($postData['address']) ) {
                return array(
                    'status'    => '400000',
                    'message'   => L('module_user_consignee_address')
                );
            }
        }
        
        if ( empty($goodsId) ) {
            return array(
                'status'    => '400000',
                'message'   => L('module_point_order_select_to_settled')
            );
        }

        $where = array(
            'id' => $goodsId,
            'goods_type' => '1',
            'is_on_sale' => '1',
            'is_delete' => '0'
        );
        $goodsInfo = $goods->where($where)->field('`goods_number`, `goods_price`, `goods_name`, `goods_image`, `sale_number`, `agent_id`')->find();

        if ( empty($goodsInfo) ) {
            return array(
                'status'    => '400000',
                'message'   => L('module_point_order_off_the_shelves')
            );
        }
        if ( $goodsInfo['goods_number'] < $goodsNumber ) {
            return array(
                'status'    => '400000',
                'message'   => L('module_point_order_in_short_stock')
            );
        }

        $total  = $goodsInfo['goods_price'] * $goodsNumber;
        $point = M('user')->where(array('id'=> $userId))->getField('point');
        if ($point < $total) 
        {
            return array(
                'status'    => '400000',
                'message'   => L('module_point_order_insufficient_points')
            );
        }

        if ( $expressType != '1' ) {
            $consignee['province_name'] = getRegionName($postData['province']);
            $consignee['city_name'] = getRegionName($postData['city']);
            $consignee['county_name'] = getRegionName($postData['county']);
            $consignee['consignee'] = $postData['consignee'];
            $consignee['telephone'] = $postData['telephone'];
            $consignee['address'] = $postData['address'];
        } else {
            $consignee = array();
        }

        return array(
            'status' => '200000',
            'goodsInfo' => $goodsInfo,
            'consignee' => $consignee,
            'total' => $total,
        );
    }
}
