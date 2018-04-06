<?php
namespace Shop\Controller;
use Plugins\Money\Money;
class MoneyController extends BaseController {
    private $money;
    public function __construct() {
        parent::__construct();
        $this->money = new Money;
    }

    /**
     * [commitOrder 下单]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function commitOrder() 
    {
        if (IS_POST) {
            $money      = I('money');
            $userId     = session('userId');
            $rechargeOrder = M('recharge_order');
            if (empty($money)) {
                exit(statusCode(array(), 400000, '请输入充值金额！'));
            }
            $order_sn = $this->makeOrderSn();
            $data = array(
                'user_id'   => $userId,
                'order_sn'  => $order_sn,
                'money'     => $money,
                'add_time'  => time(),
            );
            if ($rechargeOrder->add($data)) {
                exit(statusCode(array('order_sn'=> $order_sn)));
            } else {
                exit(statusCode(array(), 400000, '下单失败！'));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [makeOrderSn 生成订单号]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function makeOrderSn() 
    {
        // //订单生成规则
        $rechargeOrder = M('recharge_order');
        $orderId = $rechargeOrder->field('max(id) as `maxId`')->find(); //得到订单的最大自增ID
        $tempStr = rand(1000, 9999) . $orderId['maxId'];        //生成随机数
        $masterOrder = C('CHANNEL') . date('ymd') . str_pad($tempStr, 10, "0", STR_PAD_LEFT);          //合并成订单号

        //如果有重复的订单号的话，则回滚
        if($rechargeOrder->where(array('order_sn'=> $masterOrder))->count() > 0) {
            return $this->makeOrderSn();
        }
        return $masterOrder;
    }

    /**
     * [rechargeMoney 充值金额]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function rechargeMoney() {
        if ( IS_POST ) {
            $userId = session('userId');
            $order_sn = I('order_sn');
            $money = I('money');
            $rechargeOrder = M('recharge_order');
            $where = array(
                'order_sn' => $order_sn,
                'user_id' => $userId
            );
            $rechargeInfo = $rechargeOrder->where($where)->find();
            if ( empty($rechargeInfo['is_pay']) && $money == $rechargeInfo['money'] ) {
                $parameter = array(
                    'to_id'         => $userId,
                    'eventType'     => '3',
                    'type'          => '0',
                    'money'         => $money,
                    'description'   => "充值+{$money}元"
                );
                ( $this->money->money($parameter) !== false ) ? 
                    exit(statusCode()) :
                    exit(statusCode(array(), 100002));
            } else {
                exit(statusCode(array(), 100002));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [rechargeAmount 增值金额]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function rechargeAmount() 
    {
        if ( IS_POST ) {
            $userModel = D('User');
            $userId = session('userId');
            $field = '`last_earning`, `all_earning`';
            $userInfo = $userModel->getUserInfo($userId, $field);
            $userInfo['valueAddedRatio'] = C('valueAddedRatio');
            $returnData['list'] = $userInfo;
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [moneyLog 收支明细]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function moneyLog() 
    {
        if ( IS_POST ) {
            $userId = session('userId');
            $agentId = session('agentId');
            $parameter = array(
                'to_id'     => $userId,
                'isMerchant'    => $agentId,
                'agentName'     => empty($agentId) ? '': session('userInfo.nickname'),
                'limitStart'=> $this->limitStart,
                'limit'     => PAGE_LIMIT
            );
            $returnData = $this->money->getMoneyLog($parameter);
            exit(statusCode($returnData));
        } else {
            $this->display();
        }
    }

    /**
     * [balancePaid 余额支付]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function balancePaid() {
        if ( IS_POST ) {
            $money = I('money', '', 'string');
            $userId = session('userId');
            $userModel = D('User');
            $userInfo = $userModel->getUserInfo($userId, '`money`');
            if ( $userInfo['money'] >= $money ) {
                $parameter = array(
                    'to_id'         => $userId,
                    'eventType'     => '8',
                    'type'          => '1',
                    'money'         => $money,
                    'description'   => "余额支付-{$money}元",
                );
                ( $this->money->money($parameter) !== false ) ? 
                    exit(statusCode()) :
                    exit(statusCode(array(), 100002));
            } else {
                exit(statusCode(array(), 400000, L('module_bidding_balance_issufficient')));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [sweepTheCodeToPay 扫码支付]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function sweepTheCodeToPay() {
        if ( IS_POST ) {
            $userModel = D('User');
            $agentModel = D('Agent');
            $money = I('money', '', 'string');
            $agent_id = I('agent_id', '', 'string');
            $userId = session('userId');
            $agentInfo = $agentModel->getAgentInfo($agent_id, '`agent_name`, `user_id`');
            if ( empty($agentInfo) ) {
                exit(statusCode(array(), 100002));
            }
            $userInfo = $userModel->getUserInfo($userId, '`nickname`');
            // 记录用户扫码记录
            $parameter1 = array(
                'from_id'       => $agentInfo['user_id'],
                'to_id'         => $userId,
                'eventType'     => '1',
                'type'          => '1',
                'money'         => $money,
                'processMoney'  => false,
                'description'   => "支付-{$money}元-{$agentInfo['agent_name']}",
            );
            $result1 = $this->money->money($parameter1);
            // 记录商家收款记录
            $parameter2 = array(
                'from_id'       => $userId,
                'to_id'         => $agentInfo['user_id'],
                'eventType'     => '1',
                'type'          => '0',
                'money'         => $money,
                'description'   => "{$userInfo['nickname']}扫码支付+{$money}元",
            );
            $result2 = $this->money->money($parameter2);
            if ( $result1 !== false && $result2 !== false ) {
                exit(statusCode());
            } else {
                exit(statusCode([], 400000, '支付失败'));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [getMinRechargeMoney 最低充值金额]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function getMinRechargeMoney() {
        if ( IS_POST ) {
            $minRechargeMoney = C('minRechargeMoney');
            exit(statusCode(array('minRechargeMoney'=> $minRechargeMoney)));
        } else {
            exit(statusCode(array(), 100001));
        }
    }
}
