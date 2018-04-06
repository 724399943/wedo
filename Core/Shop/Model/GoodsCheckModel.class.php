<?php
namespace Shop\Model;
use Think\Model;
use Plugins\Money\Money;

class GoodsCheckModel extends Model {
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
		// 商品认证
		array('goods_id', 'require', '{%module_goods_check_select_verification}', 1, 'regex', 4),

		// 商品置顶
		array('goods_id', 'require', '{%module_goods_check_choose_topped}', 1, 'regex', 5),
	);

	/**
	 * [_auto 自动完成]
     * @author kofu <[418382595@qq.com]>
	 */
	protected $_auto = array(
        // 商品认证
        array('add_time', 'time', 4, 'function'),
        array('order_sn', 'makeCheckSn', 4, 'callback'),
        array('start_time', 'strtotime', 4, 'function'),
        array('end_time', getEndtime, 4, 'callback'),
        array('auto_audit_time', getAutoAuditTime, 4, 'callback'),

        // 商品置顶
        array('add_time', 'time', 5, 'function'),
		array('order_sn', 'makeCheckSn', 5, 'callback'),
        array('start_time', 'strtotime', 5, 'function'),
        array('end_time', getEndtime, 5, 'callback'),
        array('auto_audit_time', getAutoAuditTime, 5, 'callback'),
	);

    protected function getEndtime() {
        $startTime = I('post.start_time');
        $check_type = I('post.check_type', '0');
        $effectTime = ( empty($check_type) ) ? C('authEffectTime') : C('topEffectTime');
        return strtotime($startTime) + ( $effectTime * 86400 );
    }

    protected function getAutoAuditTime() {
        $check_type = I('post.check_type', '0');
        $auditTime = ( empty($check_type) ) ? C('authAuditTime') : C('topAuditTime');
        return time() + ( $auditTime * 3600 );
    }    

    /**
     * [payForCheck 支付商品审核]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
     * @param     [type]        $parameter [description]
     * @return    [type]                   [description]
     */
	public function payForCheck($parameter) {
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
     * [makeCheckSn 生成订单号]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function makeCheckSn() {
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
