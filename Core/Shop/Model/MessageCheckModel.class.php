<?php
namespace Shop\Model;
use Think\Model;
use Plugins\Money\Money;
class MessageCheckModel extends Model {
	protected $_validate = array(
		// 新增
		array('goods_id', 'require', '{%_PC_MESSAGE_SELECT_PRODUCT_}', 1, 'regex', 1),
		array('content', 'require', '{%_PC_MESSAGE_ENTER_TEXT_BE_PUBLISH_}', 1, 'regex', 1),
		array('image', 'require', '{%module_bidding_advertisement_photo}', 1, 'regex', 1),
	);

	protected $_auto = array(
		array('add_time', 'time', 1, 'function'),
	);

	/**
	 * [getIssuedMessage 已发布的消息]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $parameter [description]
	 * @return    [type]                   [description]
	 */
	public function getIssuedMessage($parameter) {
		$userId = $parameter['userId'];
		$type = !empty($parameter['type']) ? $parameter['type'] : 0;
		$page = !empty($parameter['page']) ? $parameter['page'] : 0;
		$limitStart = !empty($parameter['limitStart']) ? $parameter['limitStart'] : 0;
		$limit = !empty($parameter['limit']) ? $parameter['limit'] : 10;
		$where = array(
			'is_pay' => '1',
			'user_id' => $userId
		);
		$list = $this->where($where)->order('`id` DESC')->limit($limitStart.','.$limit)->select();
		$count = 0;
		if ( !empty($type) ) {
			$count = $this->where($where)->count();
			$goods = M('goods');
			foreach ($list as $key => &$value) {
				$map['id'] = $value['goods_id'];
				$value['goods_name'] = $goods->where($map)->getField('goods_name');
			}
		}
		$returnData['list'] 	= $list;
		$returnData['page'] 	= $page + 1;
		$returnData['count']	= ceil($count / $limit);
		return $returnData;
	}

	/**
	 * [getIssuingDetail 发布消息详情]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c) 2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $id [description]
	 * @return    [type]            [description]
	 */
	public function getIssuingDetail($id) {
        $data = $this->field('`content`, `add_time`, `image`,`title`')->find($id);
        $data['add_time'] = time_format($data['add_time']);
        return $data;
	}

	/**
	 * [makeOrderSn 生成订单号]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
	public function makeOrderSn() {
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

    /**
	 * [payForIssuing 支付发布消息]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $parameter [description]
	 * @return    [type]                   [description]
	 */
	public function payForIssuing($parameter) {
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
			'eventType' => 7
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
}