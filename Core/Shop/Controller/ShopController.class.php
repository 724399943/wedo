<?php
namespace Shop\Controller;
use Think\Controller;
use Shop\Controller\ConsigneeController;
use Shop\Controller\PublicController;
use Plugins\Money\Money;

class ShopController extends BaseController {
    /**
     * [shoppingCart 购物车]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function shoppingCart() 
    {
        if (IS_POST) {
            $userId = session('userId');
            $shoppingCart = D('GoodsShoppingCart');
            $returnData = $shoppingCart->getShoppingCart($userId);
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [addShoppingCart 加入购物车]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c) 2016 Xcrozz (http://www.xcrozz.com)
     */
    public function addShoppingCart(){
        if (IS_POST) {
            $goodsId = I('goods_id');
            $isBuy = I('is_buy', '0');  //是否立即购买 1立即购买
            $goodsNumber = I('goods_number', 1, 'intval');
            $userId = session('userId');
            $shoppingCart = D('GoodsShoppingCart');
            if (empty($goodsId)) {
                exit(statusCode(array(), 400000, L('_PC_USER_GOODS_EXISTS_')));
            }
            if ($goodsNumber <= 0) {
                exit(statusCode(array(), 400000, L('_PC_GOODS_CANT_LESS_THAN_')));
            }
            // 处理立即购买商品
            $map = array(
                'is_buy'    => '1',
                'user_id'   => $userId
            );
            $shoppingCart->where($map)->delete();

            $result = $shoppingCart->checkGoods($goodsId, $goodsNumber, $userId);
            if ( $result['status'] != '200000' ) {
                exit(statusCode(array(), 400000, $result['message']));
            }

            // 商品存在则递增，否则添加购物车
            $where = array(
                'user_id'   => $userId,
                'goods_id'  => $goodsId,
                'is_buy'    => $isBuy
            );
            if( $shoppingCart->where($where)->count() > 0 ){
                $result = $shoppingCart->goodsInc($goodsId, $userId, $goodsNumber);
            } else {
                $data = array(
                    'user_id' => $userId,
                    'goods_id' => $goodsId,
                    'goods_number' => $goodsNumber,
                    'add_time' => time(),
                    'is_buy' => $isBuy
                );
                $result = $shoppingCart->add($data);
            }
            if ($result !== false) {
                exit(statusCode());
            } else {
                exit(statusCode(array(), 400021));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [editShoppingCart 修改购物车的数量]
     * @author wulong <1191540273@qq.com>
     * @modify kofu <[418382595]>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function editShoppingCart()
    {
        $userId = session('userId');
        $shoppingCart = D('GoodsShoppingCart');
        if (IS_POST) {
            $id = I('id');
            $goodsId = I('goods_id');
            $goodsNumber = I('goods_number', 1, 'intval');
            if (empty($id)) {
                exit(statusCode(array(), 400000, L('_PC_USER_GOODS_EXISTS_')));
            }
            if ($goodsNumber <= 0) {
                exit(statusCode(array(), 400000, L('_PC_GOODS_CANT_LESS_THAN_')));
            }

            $shopping = $shoppingCart->find($id);
            if (empty($shopping)) {
                exit(statusCode(array(), 400000, L('_PC_GOODS_SHOPPING_CART_NO_GOODS_')));
            }

            if (empty($goodsId)) {
                $goodsId = $shopping['goods_id'];
            } else {
                $data['goods_id'] = $goodsId;
            }

            $result = $shoppingCart->checkGoods($goodsId, $goodsNumber, $userId);
            if ( $result['status'] != '200000' ) {
                exit(statusCode(array(), 400000, $result['message']));
            }
            $goodsPrice = $result['goodsDetail']['goods_price'];

            $data['goods_number'] = $goodsNumber;
            $result = true;
            $shoppingCart->startTrans();
            $data['id'] = $id;
            if ( $shoppingCart->save($data) === false ) {
                $result = false;
            }

            $total = $this->getShoppingCartTotal();
            $price = $goodsNumber * $goodsPrice;

            if ($result !== false) {
                $shoppingCart->commit();
                exit(statusCode(array('total'=> $total, 'price'=> $price)));
            } else {
                $shoppingCart->roolback();
                exit(statusCode(array(), 100002));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [getShoppingCartTotal 计算购物车总价]
     * @author wulong <1191540273@qq.com>
     * @modify kofu <[418382595@qq.com]>
     * @copyright Copyright (c)           2015 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function getShoppingCartTotal()
    {
        $userId = session('userId');
        $dbPrefix = C('DB_PREFIX');
        $sql = "SELECT `sc`.`goods_number`,`g`.`goods_price`,
                `g`.`id` AS `goods_id`,`g`.`agent_id`,
                `g`.`category_path`
                FROM {$dbPrefix}goods_shopping_cart AS `sc`
                LEFT JOIN {$dbPrefix}goods AS `g` 
                ON `sc`.`goods_id` = `g`.`id`
                WHERE `sc`.`user_id` = '{$userId}'
                AND `g`.`is_delete` = '0'
                AND `g`.`is_on_sale` = '1'
                AND `sc`.`is_buy` = '0'";
        $shopping = M()->query($sql);
        $total = 0;
        foreach ($shopping as $key => &$value) {
            $total += $value['goods_number'] * $value['goods_price'];
        }
        return $total;
    }

    /**
     * [delShoppingCart 删除购物车]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function delShoppingCart() 
    {
        $userId = session('userId');
        $shoppingCart = D('GoodsShoppingCart');
        if ( IS_POST ) {
            $id = I('id');
            $id = trim($id, ',');
            if ( empty($id) ) {
                exit(statusCode(array(), 400000, L('_PC_GOODS_SELECT_GOODS_TO_DELETED_')));
            }
            $where = array(
                'id'        => array('IN', $id),
                'user_id'   => $userId,
            );
            if ( $shoppingCart->where($where)->delete() ) {
                exit(statusCode());
            } else {
                exit(statusCode(array(), 100002));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }


    /**
     * [orderInfo 结算]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function orderInfo() 
    {
        if (IS_POST) {
            $ids = I('ids');
            $userId = session('userId');
            $shoppingCart = D('GoodsShoppingCart');
            $returnData = $shoppingCart->toBeOrder($ids, $userId);
            if ( $returnData['status'] == '200000' ) {
                unset($returnData['status']);
                // $consignee = new ConsigneeController();
                // $returnData['consignList'] = $consignee->getConsigneeList();
                exit(statusCode($returnData));
            } else {
                exit(statusCode(array(), $returnData['status'], $returnData['message']));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [commitOrder 下单]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function commitOrder() 
    {
        if ( IS_POST ) {
            $userModel = M('user');
            $orderModel = M('order');
            $orderDetailModel = M('order_detail');
            $shoppingCart = D('GoodsShoppingCart');
            $userId = session('userId');
            $consigneeId = I('consignee_id');
            // 备注，格式为'agent_id'=> 'x', 'message'=> 'x'
            $message = I('message', '', 'htmlspecialchars_decode'); 
            // 配送方式，格式为'agent_id'=> 'x', 'express_type'=> 'x'（0：送货上门，1：到店取货）
            $expressContent = I('express_content', '', 'htmlspecialchars_decode'); 
            // 收货地址，格式为'agent_id'=> 'x', 'consignee_id'=> 'x'（收货地址id）
            // $consignee_content = I('consignee_content'); 
            $ids = I('ids');
            $parameter = array(
                'ids' => $ids,
                'userId' => $userId,
                'expressContent' => $expressContent,
                'consigneeId' => $consigneeId
            );
            $returnData = $shoppingCart->toBeCommit($parameter);
            if ($returnData['status'] == '400000') {
                exit(statusCode(array(), 400000, $returnData['message']));
            }
            $result = true;
            $mysql = M();
            $mysql->startTrans();
            $list = $returnData['list'];
            $total = $returnData['total'];
            $consignee = $returnData['consignee'];
            if ( empty($list) ) {
                exit(statusCode(array(), 400020));
            }
            $publicController = new PublicController;
            $order_sn  = $publicController->makeOrderSn();
            $mergeData = array(
                'total_sn'  => $order_sn,
                'order_sn'  => $order_sn
            );
            $goodsArray = array();
            foreach ($list as $key => &$value) {
                $value = array_merge($value, $mergeData);
                $goodsArray[] = $value['goods_id'];
            }
            
            if ($orderDetailModel->addAll($list) === false) {
                $result = false;
                exit(statusCode(array(), 100002));
            }
            // 消费赠送积分
            $saleGetPoint = C('saleGetPoint');
            $data = array(
                'user_id'           => $userId,
                'message'           => $message,
                'express_content'   => $expressContent,
                'total'             => $total,
                'giving_point'      => $saleGetPoint * $total,
                'add_time'          => time(),
            );
            $data = array_merge($data, $mergeData);
            if ( !empty($consignee) ) {
                $consigneeData = array(
                    'consignee'     => $consignee['consignee'],
                    'province'      => $consignee['province'],
                    'city'          => $consignee['city'],
                    'county'        => $consignee['county'],
                    'address'       => $consignee['address'],
                    'telephone'     => $consignee['telephone'],
                );
                $data = array_merge($data, $consigneeData);
            }
            if( $orderModel->add($data) === false ){
                $result = false;
                exit(statusCode(array(), 100002));
            }

            // 删除购物车商品
            $where = array(
                'user_id'   => $userId,
                'goods_id'  => array('IN', implode(',', $goodsArray))
            );
            if ( $shoppingCart->where($where)->delete() === false ) {
                $result = false;
                exit(statusCode(array(), 100002));
            }

            // 增加用户下单数量
            if ( $userModel->where(array('id'=> $userId))->setInc('order_number') === false ) {
                $result = false;
                exit(statusCode(array(), 100002));
            }

            if ( $result === true ) {
                $mysql->commit();
                exit(statusCode(array('order_sn'=> $order_sn)));
            } else {
                $mysql->rollback();
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }
}
