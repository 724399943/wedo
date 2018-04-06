<?php
namespace Shop\Controller;
use Think\Controller;
use Plugins\Message\Message;
use Plugins\Huanxin\Easemob;
class OrderController extends BaseController {
    /**
     * [orderList 订单列表]
     * @author kofu <[418382595@qq.com]>
     */
    public function orderList() 
    {
        if (IS_POST) {
            $orderModel = D('Order');
            $parameter  = array(
                'userId'        => session('userId'),
                'agentId'       => session('agentId'),
                'orderType'     => I('order_type', 'all'),
                'userType'      => I('user_type', 'buyer'),
                'expressType'   => I('express_type', '-1'),
                'orderSn'       => I('order_sn', ''),
                'date'          => I('date', ''),
                'type'          => I('type', '0'),
                'startTime'     => I('startTime', ''),
                'endTime'       => I('endTime', ''),
                'page'          => $this->page,
                'limitStart'    => $this->limitStart,
                'limit'         => PAGE_LIMIT
            );
            $returnData = $orderModel->getOrder($parameter);
            exit(statusCode($returnData));
        } else {
            $startTime = I('get.startTime', '');
            $endTime = I('get.endTime', '');
            if ( empty($startTime) && empty($endTime) ) {
                $startTime = strtotime('-1 years');
                $endTime = strtotime('+1 days');
            }
            $return = array(
                'order_sn' => I('get.order_sn', ''),
                'order_type' => I('get.order_type', 'all'),
                'express_type' => I('get.express_type', '-1'),
                'startTime' => $startTime,
                'endTime' => $endTime,
            );
            $this->assign('return', $return);
            $this->display();
        }
    }

    /**
     * [delivery 订单发货]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function delivery() {
        if ( IS_POST ) {
            $userModel = M('user');
            $order = M('order');
            $agentId = session('agentId');
            $where = array(
                'supplier_id' => $agentId,
                'order_sn' => I('order_sn'),
            );
            $orderDetail = $order->where($where)->field('`id`, `user_id`, `total_sn`, `order_sn`')->find();
            if ( empty($orderDetail) ) {
                exit(statusCode(array(), 100002));
            }
            $nowTime = time();
            $remark = I('remark');
            $order->startTrans();
            $saveData = array(
                'id'                => $orderDetail['id'],
                'delivery_status'   => '1',
                'delivery_time'     => $nowTime,
                'status'            => '2',
                'remark'            => $remark,
            );
            if( $order->save($saveData) !== false ) {
                // 设置主单发货状态
                if ( $orderDetail['total_sn'] != $orderDetail['order_sn'] ) {
                    $where = array(
                        'total_sn'  => $orderDetail['total_sn'],
                        'order_sn'  => array('NEQ', $orderDetail['total_sn']),
                        'delivery_status'   => '0'
                    );
                    if ( $order->where($where)->count() <= 0 ) {
                        unset($saveData['id']);
                        if ( $order->where(array('order_sn'=> $orderDetail['total_sn']))->save($saveData) === false ) {
                            $order->rollback();
                            exit(statusCode(array(), 100002));
                        }
                    }  
                }

                // 推送站内订单消息
                $message = new Message;

                $language = $userModel->where(array('id'=>$orderDetail['user_id']))->getField('language');
                if($language == 'zh-cn'){
                    $title = "您有一笔订单已发货";
                    $content = "亲爱的顾客，您的订单{$orderDetail['order_sn']}已经发货啦，详情可在订单中心查看！";
                }else{
                    $title = "Your order has been shipped";
                    $content = "Dear customer, your order {$orderDetail['order_sn']} has been shipped and details can be viewed in the order center";
                }
                $parameter = array(
                    // 'title' => '您有一笔订单已发货',
                    // 'content' => "亲爱的顾客，您的订单{$orderDetail['order_sn']}已经发货啦，详情可在订单中心查看！",
                    'title' => $title,
                    'content' => $content,
                    'type' => '1',
                    'agent_id' => $agentId,
                    'message_type' => '2',
                    'order_sn' => $orderDetail['order_sn'],
                    'receiver_id' => $orderDetail['user_id'],
                );
                if ( $message->Sendmessage($parameter) === false ) {
                    $result = false;
                }

                $options = imConf();
                $Easemob = new Easemob($options);
                $ext = new \stdClass();
                $ext->type = '7';
                $ext->order_sn = $orderDetail['order_sn'];
                $Easemob->sendText('wedoAdmin', 'users', array('wedo'.$orderDetail['user_id']), 'Your order has been shipped', $ext);

                $order->commit();
                exit(statusCode());
            } else {
                $order->rollback();
                exit(statusCode(array(), 100002));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [received 确认收货]
     * @author kofu <[418382595@qq.com]>
     */
    public function received() 
    {
        if ( IS_POST ) {
            $orderModel = M('order');
            $order_sn = I('order_sn');
            $userId = session('userId');
            $where = array(
                'user_id' => $userId,
                'order_sn' => $order_sn
            );
            $saveData = array(
                'received' => '1',
                'received_time' => time(),
                'status' => '3'
            );
            if ( $orderModel->where($where)->save($saveData) !== false ) {
                exit(statusCode());
            } else {
                exit(statusCode(array(), 100002));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     *[cancelTheOrder 取消订单]
     * @author kofu <[418382595@qq.com]>
     */
    public function cancelTheOrder() 
    {
        if ( IS_POST ) {
            $orderModel = M('order');
            $userModel = M('user');
            $creditLogModel = M('user_credit_log');
            $order_sn = I('order_sn');
            $userId = session('userId');
            $where = array(
                'user_id' => $userId,
                'order_sn' => $order_sn
            );
            $saveData = array(
                'is_out_date' => '1',
                'status' => '5'
            );
            if ( $orderModel->where($where)->save($saveData) !== false ) {
                // 扣除信用
                $userCancelOrderCredit = C('userCancelOrderCredit');
                $userCredit = $userModel->where(array('id'=> $userId))->getField('`credit`');
                $surplus_credit = $userCredit - $userCancelOrderCredit;
                $addData = array(
                    'user_id' => $userId,
                    'credit_type' => '1',
                    'change_type' => '1',
                    'credit' => $userCancelOrderCredit,
                    'surplus_credit' => $surplus_credit,
                    'order_sn' => $order_sn,
                    'add_time' => time(),
                );
                $creditLogModel->add($addData);
                $userModel->where(array('id'=> $userId))->save(array('credit'=> $surplus_credit));
                exit(statusCode());
            } else {
                exit(statusCode(array(), 100002));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     *[merchantCancel 取消订单]
     * @author kofu <[418382595@qq.com]>
     */
    public function merchantCancel() 
    {
        if ( IS_POST ) {
            $orderModel = M('order');
            $userModel = M('user');
            $creditLogModel = M('user_credit_log');
            $order_sn = I('order_sn');
            $where = array(
                'supplier_id' => session('agentId'),
                'order_sn' => $order_sn
            );
            $saveData = array(
                'is_out_date' => '1',
                'status' => '5'
            );
            if ( $orderModel->where($where)->save($saveData) !== false ) {
                // 扣除信用
                $userId = session('userId');
                $agentCancelOrderCredit = C('agentCancelOrderCredit');
                $userCredit = $userModel->where(array('id'=> $userId))->getField('`credit`');
                $surplus_credit = $userCredit - $agentCancelOrderCredit;
                $addData = array(
                    'user_id' => $userId,
                    'credit_type' => '1',
                    'change_type' => '1',
                    'credit' => $agentCancelOrderCredit,
                    'surplus_credit' => $surplus_credit,
                    'order_sn' => $order_sn,
                    'add_time' => time(),
                );
                $creditLogModel->add($addData);
                $userModel->where(array('id'=> $userId))->save(array('credit'=> $surplus_credit));
                exit(statusCode());
            } else {
                exit(statusCode(array(), 100002));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }
    
    /**
     * [orderDetail 订单详情]
     * @author kofu <[418382595@qq.com]>
     */
    public function orderDetail() 
    {
        if ( IS_POST ) {
            $order = D('Order');
            $parameter = array(
                'userId' => session('userId'),
                'order_sn' => I('order_sn', '', 'string'),
                'longitude' => I('longitude', '', 'string'),
                'latitude' => I('latitude', '', 'string'),
            );
            $returnData = $order->getOrderDetail($parameter);
            if ( $returnData['status'] == '400000' ) {
                exit(statusCode(array(), 100002));
            }
            exit(statusCode($returnData));
        } else {
            $this->display();
        }
    }

    /**
     * [purchaseHistory 购买记录]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function purchaseHistory() {
        if ( IS_POST ) {
            $userId = session('userId');
            $order = D('Order');
            $parameter = array(
                'userId' => $userId,
                'limitStart' => $this->limitStart,
                'limit' => PAGE_LIMIT
            );
            $returnData['list'] = $order->getPurchaseHistory($parameter);
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [saveRemark 编辑备注]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function saveRemark() {
        if ( IS_POST ) {
            $orderModel = M('order');
            $order_sn = I('order_sn', '', 'string');
            $remark = I('remark', '', 'string');
            $where = array(
                'supplier_id' => session('agentId'),
                'order_sn' => $order_sn
            );
            if ( $orderModel->where($where)->save(array('remark'=> $remark)) !== false ) {
                exit(statusCode());
            } else {
                exit(statusCode(array(), 100002));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [realTimeNavigation 实时导航]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function realTimeNavigation() {
        if ( IS_POST ) {
            // 获取商家以及用户位置
            $order_sn = I('order_sn');
            $userId = session('userId');
            $userModel = D('User');
            $dbPrefix = C('DB_PREFIX');
            $sql = "SELECT `u`.`longitude`, `u`.`latitude` 
                    FROM `{$dbPrefix}order` AS `o` 
                    LEFT JOIN `{$dbPrefix}user` AS `u` ON `o`.`user_id` = `u`.`id` 
                    WHERE `o`.`order_sn` = '{$order_sn}'";
            $customerData = $userModel->query($sql);
            $customerData = $customerData[0];
            $userData = $userModel->getUserInfo($userId, '`longitude`, `latitude`');
            $returnData = array(
                'customerData' => $customerData,
                'userData' => $userData,
            );
            exit(statusCode($returnData));
        } else {
            $this->display();
        }
    }
}