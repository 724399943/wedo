<?php
namespace Shop\Controller;
use Think\Controller;
use Plugins\Point\Point;

class PointController extends BaseController {
    private $point;
    public function __construct() {
        parent::__construct();
        $this->point = new Point();
    }

    /**
     * [index 我的积分]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function index() {
        $point = D('User')->getUserInfo($userId, 'point');
        if ( IS_POST ) {
            $userId = session('userId');
            $returnData['list'] = $point;
            exit(statusCode($returnData));
        } else {
            $this->assign('point', $point['point']);
            $this->display();
        }
    }

    /**
     * [pointGoods 积分商品]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function pointGoods(){
        $goods_name =  I('request.goods_name', '', 'urldecode');
        $express_type = I('request.express_type', '-1');
        if (IS_POST) {
            $goodsModel = D('Goods');
            $parameter = array(
                'goods_name'       => $goods_name,
                'express_type'  => $express_type,
                'goods_type'    => '1',
                'is_on_sale'    => '1',
                'page'          => $this->page,
                'limitStart'    => $this->limitStart,
                'limit'         => PAGE_LIMIT
            );
            $returnData = $goodsModel->getGoodsList($parameter);
            $returnData['list'] = $goodsModel->checkGoodsCollect(session('userId'), $returnData['list']);
            exit(statusCode($returnData));
        } else {
            $return = array(
                'goods_name' => $goods_name,
                'express_type' => $express_type,
            );
            $this->assign('return', $return);
            $this->display();
        }
    }

    /**
     * [goodsDetail 商品详情]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function goodsDetail() 
    {
        if (IS_POST) {
            $goods      = D('Goods');
            $parameter  = array(
                'goodsId'   => I('goods_id'),
                'userId'    => session('userId'),
                'longitude' => I('longitude', '113.37763'),
                'latitude'  => I('latitude', '23.13275'),
                'isTemp'    => session('is_temp')
            );
            $returnData = $goods->getGoodsDetail($parameter);
            exit(statusCode($returnData));
        } else {
            $this->display();
        }
    }

    /**
     * [pointOrder 积分订单]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function pointOrder(){;
        $userId = session('userId');
        $point = D('PointOrder');
        if (IS_POST) {
            $parameter = array(
                'type'      => I('type', '0'),
                'userId'    => $userId,
                'limitStart'=> $this->limitStart,
                'limit'     => PAGE_LIMIT
            );
            $returnData['list'] = $point->pointOrder($parameter);
            exit(statusCode($returnData));
        } else {
            $this->display();
        }   
    }

    /**
     * [orderDetail 订单详情]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function orderDetail(){
        $userId = session('userId');
        $order_sn = I('order_sn');
        if ( empty($order_sn) ) {
            exit(statusCode(array(), 100002));
        }
        $pointOrder = D('PointOrder');
        $parameter = array(
            'userId' => $userId,
            'order_sn' => $order_sn,
        );
        $list = $pointOrder->orderDetail($parameter);
        if ( IS_POST ) {
            $returnData['list'] = $list;
            exit(statusCode($returnData));
        } else {
            $this->assign('list', $list);
            $this->display();
        }
    }

    /**
     * [pointReceived 确认收货]
     * @author wulong <1191540273@qq.com>
     * @modify kofu <[418382595@qq.com]>
     * @copyright Copyright (c)           2015 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function pointReceived(){
        if (IS_POST) {
            $order_sn = I('order_sn');
            if (empty($order_sn)) {
                exit(statusCode(array(), 100002));
            }
            $pointOrder = M('point_order');
            $where['order_sn'] = $order_sn;
            $orderInfo = $pointOrder->where($where)->find();

            if (!empty($orderInfo)) {
                if ($pointOrder->where($where)->save(array('received_time'=> time(), 'received'=> '1')) !== false) {
                    exit(statusCode());
                } else {
                    exit(statusCode(array(), 100002));
                }
            } else {
                exit(statusCode(array(), 100002));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [makePointOrderSn description]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function makePointOrderSn() {
        // //订单生成规则
        $order = M('point_order');
        $orderId = $order->field('max(id) as `maxId`')->find(); //得到订单的最大自增ID
        $tempStr = rand(1000, 9999) . $orderId['maxId'];        //生成随机数
        $masterOrder = C('CHANNEL') . date('ymd') . str_pad($tempStr, 10, "0", STR_PAD_LEFT);          //合并成订单号

        //如果有重复的订单号的话，则回滚
        if($order->where(array('order_sn'=>$masterOrder))->count() > 0) {
            return $this->makePointOrderSn();
        }
        return $masterOrder;
    }

    /**
     * [pointInfo 积分商品结算页]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function pointInfo() {
        $goodsId = I('goods_id', '');
        $goodsNumber = I('goods_number', 1);
        $userId = session('userId');
        $point = D('PointOrder');
        if ( IS_POST ) {
            $returnData = $point->pointInfo($userId, $goodsId, $goodsNumber);
            if ( $returnData['status'] == '400000' ) {
                exit(statusCode(array(), $returnData['status'], $returnData['message']));
            } else {
                exit(statusCode($returnData));
            }
        } else {
            // exit(statusCode(array(), 100001));
            $this -> display();
        }
    }

    /**
     * [commitPointInfo 提交积分商品订单]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function commitPointInfo() {
        if ( IS_POST ) {
            $goods = M('goods');
            $userId = session('userId');
            $goodsId = I('post.goods_id','');
            $goodsNumber = I('post.goods_number', 1);
            $consigneeId = I('post.consignee_id','');
            $expressType = I('post.express_type', 0);
            $parameter = array(
                'userId' => $userId,
                'goodsId' => $goodsId,
                'goodsNumber' => $goodsNumber,
                'consigneeId' => $consigneeId,
                'expressType' => $expressType
            );
            $pointOrder = D('PointOrder');
            $returnData = $pointOrder->toBeCommit($parameter);
            if ( $returnData['status'] == '400000' ) {
                exit(statusCode(array(), 400000, $returnData['message']));
            }
            $consignee = $returnData['consignee'];
            $goodsInfo = $returnData['goodsInfo'];
            $total = $returnData['total'];

            $orderSn = $this->makePointOrderSn();
            $data = array(
                'total_sn'      => $orderSn,
                'order_sn'      => $orderSn,
                'goods_id'      => $goodsId,
                'goods_name'    => $goodsInfo['goods_name'],
                'goods_image'   => $goodsInfo['goods_image'],
                'price'         => $goodsInfo['goods_price'] * $goodsNumber,
                'unit_price'    => $goodsInfo['goods_price'],
                'goods_number'  => $goodsNumber,
                'add_time'      => time()
            );

            $result = true;
            $mysql  = M();
            $mysql->startTrans();
            $pointOrderDetail = M('point_order_detail');
            if ( $pointOrderDetail->add($data) === false ) {
                $result = false;
                exit(statusCode(array(), 400000, 'unfortunately,wedo has stopped'));
            } else {
                unset($data);
                // 扣除积分
                $parameter = array(
                    'from_id' => 0,
                    'to_id' => $userId,
                    'num' => $total,
                    'type' => 0,
                    'event_type' => 7,
                    'order_sn' => $orderSn,
                    'description' => "Conversion-{$total}point(s)",
                );
                if ( $this->point->point($parameter) === false ) 
                {
                    $result = false;
                    exit(statusCode(array(), 400000, 'unfortunately,wedo has stopped'));
                }

                // 增加销量 && 扣减库存
                $goodsData = array(
                    'id' => $goodsId,
                    'goods_number' => $goodsInfo['goods_number'] - 1,
                    'sale_number' => $goodsInfo['sale_number'] + 1
                );
                if ( $goods->save($goodsData) === false ) {
                    $result = false;
                    exit(statusCode(array(), 400000, 'unfortunately,wedo has stopped'));
                }
                
                $data = array(
                    'agent_id'          => $goodsInfo['agent_id'],
                    'supplier_id'       => $goodsInfo['agent_id'],
                    'user_id'           => $userId,
                    'order_sn'          => $orderSn,
                    'total_sn'          => $orderSn,
                    'total'             => $total,
                    'is_pay'            => 1,
                    'pay_time'          => time(),
                    'express_type'      => $expressType,
                    'add_time'          => time(),
                );

                if ( $expressType == '0' ) {
                    $mergeData = array(
                        'consignee' => $consignee['consignee'],
                        'telephone' => $consignee['telephone'],
                        'province'  => $consignee['province_name'],
                        'city'      => $consignee['city_name'],
                        'address'   => $consignee['address'],
                    );
                    if ( !empty($consignee['county_name']) ) {
                        $mergeData['county'] = $consignee['county_name'];
                    }
                    $data = array_merge($data, $mergeData);
                }

                $pointOrder = M('point_order');
                if ( $pointOrder->add($data) === false ) {
                    $result = false;
                    exit(statusCode(array(), 400000, 'unfortunately,wedo has stopped'));
                }
            }

            if ($result !== false) {
                $mysql->commit();
                exit(statusCode(array('order_sn'=> $orderSn)));
            } else {
                $mysql->rollback();
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [agentCommitPointInfo 商家提交积分商品订单]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function agentCommitPointInfo() {
        if ( IS_POST ) {
            $goods = M('goods');
            $userId = session('userId');
            $postData = I('post.');
            $goodsId = I('post.goods_id','');
            $goodsNumber = I('post.goods_number', 1);
            $expressType = I('post.express_type', 0);
            $parameter = array(
                'userId' => $userId,
                'goodsId' => $goodsId,
                'goodsNumber' => $goodsNumber,
                'postData' => $postData,
                'expressType' => $expressType
            );
            $pointOrder = D('PointOrder');
            $returnData = $pointOrder->agentToBeCommit($parameter);
            if ( $returnData['status'] == '400000' ) {
                exit(statusCode(array(), 400000, $returnData['message']));
            }
            $consignee = $returnData['consignee'];
            $goodsInfo = $returnData['goodsInfo'];
            $total = $returnData['total'];

            $orderSn = $this->makePointOrderSn();
            $data = array(
                'total_sn'      => $orderSn,
                'order_sn'      => $orderSn,
                'goods_id'      => $goodsId,
                'goods_name'    => $goodsInfo['goods_name'],
                'goods_image'   => $goodsInfo['goods_image'],
                'price'         => $goodsInfo['goods_price'] * $goodsNumber,
                'unit_price'    => $goodsInfo['goods_price'],
                'goods_number'  => $goodsNumber,
                'add_time'      => time()
            );

            $result = true;
            $mysql  = M();
            $mysql->startTrans();
            $pointOrderDetail = M('point_order_detail');
            if ( $pointOrderDetail->add($data) === false ) {
                $result = false;
                exit(statusCode(array(), 100002));
            } else {
                unset($data);
                // 扣除积分
                $parameter = array(
                    'from_id' => 0,
                    'to_id' => $userId,
                    'num' => $total,
                    'type' => 0,
                    'event_type' => 7,
                    'order_sn' => $orderSn,
                    'description' => "Conversion-{$total}point(s)",
                );
                if ( $this->point->point($parameter) === false ) 
                {
                    $result = false;
                    exit(statusCode(array(), 100002));
                }

                // 增加销量 && 扣减库存
                $goodsData = array(
                    'id' => $goodsId,
                    'goods_number' => $goodsInfo['goods_number'] - 1,
                    'sale_number' => $goodsInfo['sale_number'] + 1
                );
                if ( $goods->save($goodsData) === false ) {
                    $result = false;
                    exit(statusCode(array(), 100002));
                }
                
                $data = array(
                    'agent_id'          => $goodsInfo['agent_id'],
                    'supplier_id'       => $goodsInfo['agent_id'],
                    'user_id'           => $userId,
                    'order_sn'          => $orderSn,
                    'total_sn'          => $orderSn,
                    'total'             => $total,
                    'is_pay'            => 1,
                    'pay_time'          => time(),
                    'express_type'      => $expressType,
                    'add_time'          => time(),
                );

                if ( $expressType == '0' ) {
                    $mergeData = array(
                        'consignee' => $consignee['consignee'],
                        'telephone' => $consignee['telephone'],
                        'province'  => $consignee['province_name'],
                        'city'      => $consignee['city_name'],
                        'county'    => $consignee['county_name'],
                        'address'   => $consignee['address'],
                    );
                    $data = array_merge($data, $mergeData);
                }

                $pointOrder = M('point_order');
                if ( $pointOrder->add($data) === false ) {
                    $result = false;
                    exit(statusCode(array(), 100002));
                }
            }

            if ($result !== false) {
                $mysql->commit();
                exit(statusCode(array('order_sn'=> $orderSn)));
            } else {
                $mysql->rollback();
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [commitSuccess 订单支付成功页]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function commitSuccess() {
        $this->display();
    }

    /**
     * [shareCallback 分享成功回调]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function shareCallback() {
        if ( IS_POST ) {
            $userId = session('userId');
            $shareGetPoint = C('shareGetPoint');
            // 推荐有奖赠送积分
            $parameter = array(
                'from_id' => '0',
                'to_id' => $userId,
                'num' => $shareGetPoint,
                'type' => '1',
                'event_type' => '8',
                'description' => "Share-{$total}point(s)",
            );
            ( $this->point->point($parameter) !== false ) ?
                exit(statusCode()) : 
                exit(statusCode(array(), 100002));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [sharePointLog 推荐有奖积分明细]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function sharePointLog() {
        if (IS_POST) {
            $userId     = session('userId');
            $parameter = array(
                'eventType' => '8',
                'to_id'     => $userId,
                'page'      => $this->page,
                'limitStart'=> $this->limitStart,
                'limit'     => PAGE_LIMIT
            );
            $returnData = $this->point->getPointLog($parameter);
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [pointLog 积分明细]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function pointLog() {
        if (IS_POST) {
            $userId     = session('userId');
            $parameter = array(
                'to_id'     => $userId,
                'type'      => I('type', '0'),
                'page'      => $this->page,
                'limitStart'=> $this->limitStart,
                'limit'     => PAGE_LIMIT
            );
            $returnData = $this->point->getPointLog($parameter);
            exit(statusCode($returnData));
        } else {
            $this->display();
        }
    }
}