<?php
namespace Plugins\Money;
use Think\Controller;
/**
 * 余额
 */
class Money extends Controller {
    /**
     * [money 收入/支出余额]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)        2016          Xcrozz (http://www.xcrozz.com)
     * @param     [type]        $to_id      [用户id]
     * @param     [type]        $money      [金额]
     * @param     [type]        $type       [0：收入，1：支出]
     * @param     [type]        $eventType  [事件类型（0：购物，1：扫码支付，2：增益，3：充值，4：提现，5：商品认证，6：商品置顶，7：发布消息，8：余额支付，9：商家注册，10：商品编辑）]
     * @return    [type]                    [description]
     */
    public function money($parameter) {
        $from_id = (!empty($parameter['from_id'])) ? $parameter['from_id'] : 0;
        $to_id = (!empty($parameter['to_id'])) ? $parameter['to_id'] : 0;
        $money = (!empty($parameter['money'])) ? $parameter['money'] : 0;
        $type = (!empty($parameter['type'])) ? $parameter['type'] : 0;
        $eventType = (!empty($parameter['eventType'])) ? $parameter['eventType'] : 0;
        $order_sn = (!empty($parameter['order_sn'])) ? $parameter['order_sn'] : '';
        $processMoney = ( empty($parameter['processMoney']) ) ? true : false;
        $description = (!empty($parameter['description'])) ? $parameter['description'] : '';
        $user = M('user');
        $agent = M('agent');
        $moneyLog = M('money_log');
        if ( $processMoney === true ) {
            if( $type == 0 ) {
                $state = $user->where(array('id'=> $to_id))->setInc('money', $money);
            } else {
                $state = $user->where(array('id'=> $to_id))->setDec('money', $money);
            }
        }

        if( $state !== false ) {
            $addData = array(
                'from_id'       => $from_id,
                'to_id'         => $to_id,
                'type'          => $type,
                'money'         => $money,
                'event_type'    => $eventType,
                'add_time'      => time()
            );

            if ( !empty($order_sn) ) {
                $addData['order_sn'] = $order_sn;
            }
            if ( !empty($description) ) {
                $addData['description'] = $description;
            }

            // 收支来源、去向
            switch ( $eventType ) {
                case '0':
                    $addData['from'] = ( $type == '1' ) ? '4' : '5';
                    $addData['to'] = ( $type == '1' ) ? '5' : '3';
                    break;
                case '1':
                    $addData['from'] = ( $type == '1' ) ? '4' : '5';
                    $addData['to'] = ( $type == '1' ) ? '5' : '3';
                    break;
                case '2':
                    $addData['from'] = '5';
                    $addData['to'] = '4';
                    break;
                case '3':
                    $addData['from'] = '5';
                    $addData['to'] = '4';
                    break;
                case '4':
                    $addData['from'] = '3';
                    $addData['to'] = '5';
                    break;
                case '5':
                    $addData['from'] = '3';
                    $addData['to'] = '5';
                    break;
                case '6':
                    $addData['from'] = '3';
                    $addData['to'] = '5';
                    break;
                case '7':
                    $addData['from'] = '3';
                    $addData['to'] = '5';
                    break;
                case '8':
                    $addData['from'] = '4';
                    $addData['to'] = '5';
                    break;
                case '9':
                    $addData['from'] = '5';
                    $addData['to'] = '3';
                    break;
            }

            $moneyLog->add($addData);
            return true;
        } else {
            return false;
        }
    }

    private function eventType($parameter) {
        $event_type = $parameter['event_type'];
        $money = $parameter['money'];
        $type = $parameter['type'];
        $language = $parameter['language'];
        $isMerchant = $parameter['isMerchant'];
        $order_sn = $parameter['order_sn'];
        $agentName = $parameter['agentName'];
        $userName = $parameter['userName'];
        if ( $language == 'zh-cn' ) {
            $eventType = array(
                '0'     => (empty($type) ? '支付+' : '支付-RM') . $money . '-订单' . $order_sn,
                '1'     => (empty($type) ? '支付+' : '支付-RM') . $money . '-' . $agentName,
                '2'     => (empty($type) ? '增益+' : '增益-RM') . $money . '',
                '3'     => (empty($type) ? '充值+' : '充值-RM') . $money . '',
                '4'     => (empty($type) ? '提现+' : '提现-RM') . $money . '',
                '5'     => (empty($type) ? '商品认证+' : '商品认证-RM') . $money . '',
                '6'     => (empty($type) ? '商品置顶+' : '商品置顶-RM') . $money . '',
                '7'     => (empty($type) ? '发布消息+' : '发布消息-RM') . $money . '',
                '8'     => (empty($type) ? '余额支付+' : '余额支付-RM') . $money . '',
                '9'     => (empty($type) ? '商家注册+' : '商家注册-RM') . $money . '',
                '10'    => (empty($type) ? '商品编辑+' : '商品编辑-RM') . $money . '',
            );
        } else {
            if ( empty($isMerchant) ) {
                $eventType = array(
                    '0'     => (empty($type) ? 'Shopping+RM' : 'Shopping-RM') . $money . '-Order Number:' . $order_sn,
                    '1'     => (empty($type) ? 'Shopping+RM' : 'Shopping-RM') . $money . '-' . $agentName,
                    '2'     => (empty($type) ? 'Appreciation+RM' : 'Appreciation-RM') . $money,
                    '3'     => (empty($type) ? 'Recharge+RM' : 'Recharge-RM') . $money,
                    '4'     => (empty($type) ? 'Withdraw+RM' : 'Withdraw-RM') . $money,
                    '5'     => (empty($type) ? 'verified Products+RM' : 'verified Products-RM') . $money,
                    '6'     => (empty($type) ? 'Product bump to top+RM' : 'Product bump to top-RM') . $money,
                    '7'     => (empty($type) ? 'Publish the news+RM' : 'Publish the news-RM') . $money,
                    '8'     => (empty($type) ? 'Balance paid+RM' : 'Balance paid-RM') . $money,
                    '9'     => (empty($type) ? 'Register+RM' : 'Register-RM') . $money,
                    '10'    => (empty($type) ? 'edited products+RM' : 'edited products-RM') . $money,
                );
            } else {
                $eventType = array(
                    '0'     => (empty($type) ? 'Order from+RM' : 'Order from-RM') . $money . '-Order Number:' . $order_sn,
                    '1'     => (empty($type) ? 'Order from+RM' : 'Order from-RM') . $money . '-' . $userName,
                    '2'     => (empty($type) ? 'Appreciation+RM' : 'Appreciation-RM') . $money,
                    '3'     => (empty($type) ? 'Recharge+RM' : 'Recharge-RM') . $money,
                    '4'     => (empty($type) ? 'Withdraw+RM' : 'Withdraw-RM') . $money,
                    '5'     => (empty($type) ? 'verified Products+RM' : 'verified Products-RM') . $money,
                    '6'     => (empty($type) ? 'Product bump to top+RM' : 'Product bump to top-RM') . $money,
                    '7'     => (empty($type) ? 'Publish the news+RM' : 'Publish the news-RM') . $money,
                    '8'     => (empty($type) ? 'Balance paid+RM' : 'Balance paid-RM') . $money,
                    '9'     => (empty($type) ? 'Register+RM' : 'Register-RM') . $money,
                    '10'    => (empty($type) ? 'edited products+RM' : 'edited products-RM') . $money,
                );
            }
        }
        return $eventType[$event_type];
    }

    /**
     * [getMoneyLog 获取消费log]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
     * @param     [type]        $parameter [description]
     * @return    [type]                   [description]
     */
    public function getMoneyLog($parameter) {
        $to_id = !empty($parameter['to_id']) ? $parameter['to_id'] : '';
        $isMerchant = !empty($parameter['isMerchant']) ? $parameter['isMerchant'] : '';
        $agentName = !empty($parameter['agentName']) ? $parameter['agentName'] : '';
        $page = !empty($parameter['page']) ? $parameter['page'] : 0;
        $type = $parameter['type'] != '' ? $parameter['type'] : -1;
        $eventType = $parameter['event_type'] != '' ? $parameter['event_type'] : '-1';
        $date = !empty($parameter['date']) ? $parameter['date'] : '';
        $startTime = !empty($parameter['startTime']) ? $parameter['startTime'] : '';
        $endTime = !empty($parameter['endTime']) ? $parameter['endTime'] : '';
        $limitStart = !empty($parameter['limitStart']) ? $parameter['limitStart'] : 0;
        $limit = !empty($parameter['limit']) ? $parameter['limit'] : 10;
        $moneyLog = M('money_log');
        $userModel = M('user');
        $agentModel = M('agent');
        $where['to_id'] = $to_id;
        if ( $eventType != '-1' ) {
            $where['event_type'] = $eventType;
        }else{
            unset($where['event_type']);
        }  
        if ( !empty($date) ) {
            $startTime = strtotime($date);
            $endTime = $startTime + 86400;
            $where['add_time'] = array('BETWEEN', array($startTime, $endTime));
        }
        if ( !empty($startTime) &&  !empty($endTime)) {
            $where['add_time'] = array('BETWEEN', array($startTime, $endTime));
        }
        $count = 0;
        if ( !empty($type) ) {
            $count = $moneyLog->where($where)->count();
        }else{
            $where['id'] = '0';
            $count = $moneyLog->where($where)->count();
        }
        $list = $moneyLog->where($where)->order('`id` DESC')->limit($limitStart.','.$limit)->select();
        // $language = $userModel->where(array('id'=>$to_id))->getField('language');
        $language = session('language');
        foreach ($list as $key => &$value) {
            // if($language == 'zh-cn') {
            //     $value['event'] = $this->eventType($value['event_type'], $value['money'], $value['type']);
            // } else{
            //     $value['event'] = $value['description'];
            // }
            if ( empty($isMerchant) && $value['event_type'] == '1' ) {
                $to_id = $moneyLog->where(['order_sn'=>$value['order_sn'], 'from_id'=>$value['to_id']])->getField('to_id');
                $agentName = $agentModel->where(['user_id'=>$to_id])->getField('agent_name');
            } elseif ( !empty($isMerchant) && $value['event_type'] == '1' ) {
                $userName = $userModel->where(['id'=>$value['from_id']])->getField('nickname');
            }
            $value['event'] = $this->eventType([
                'event_type' => $value['event_type'],
                'money' => $value['money'],
                'type' => $value['type'],
                'language' => $language,
                'isMerchant' => $isMerchant,
                'order_sn' => $value['order_sn'],
                'agentName' => $agentName,
                'userName' => $userName,
            ]);
        }
        $returnData['list']     = $list;
        $returnData['page']     = $page + 1;
        $returnData['count']    = ceil($count / $limit);
        return $returnData;
    }

    /**
     * [countMoney 计算结算金额]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
     * @param     [type]        $parameter [description]
     * @return    [type]                   [description]
     */
    public function countMoney($parameter) {
        $to_id = !empty($parameter['to_id']) ? $parameter['to_id'] : '';
        $event_type = !empty($parameter['event_type']) ? $parameter['event_type'] : '';
        $where = array(
            'to_id' => $to_id,
            'event_type' => array('IN', $event_type)
        );
        $moneyLog = M('money_log');
        $total = $moneyLog->where($where)->field('SUM(`money`) AS `total`')->find();
        $total = !empty($total['total']) ? $total['total'] : 0;
        return $total;
    }
}