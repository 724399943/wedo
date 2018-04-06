<?php
namespace Admin\Controller;
class PointOrderController extends BaseController {
    /**
     * [pointOrderList 积分订单列表]
     * @author shichun <672517056@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function pointOrderList() {
        $dbPrefix   = C('DB_PREFIX');
        $orderModel = M('point_order');
        $whereStr   = "";
        $leftJoin   = "";

        $order_sn     = I('get.order_sn',''); 
        $pay_state    = I('get.pay_state','-1'); 
        $startTime    = I('get.startTime',''); 
        $endTime      = I('get.endTime',''); 
        $express_type    = I('get.express_type','-1'); 
        $goods_name   = I('get.goods_name',''); 
        //订单号
        if ( $order_sn != '') {
            $whereStr .= " AND `o`.`order_sn` LIKE '%{$order_sn}%'";
        }
        //订单类型
        if ( $pay_state != '-1') {
            switch ($pay_state) {
                case '0':
                    $whereStr .= " AND `o`.`is_pay` = 0 ";
                    break;
                case '1':
                    $whereStr .= " AND `o`.`is_pay` = 1 AND `o`.`delivery_status`= 0 ";
                    break;
                case '2':
                    $whereStr .= " AND `o`.`is_pay` = 1 AND `o`.`delivery_status`= 1 ";
                    break;
                case '4':
                    $whereStr .= " AND `o`.`is_pay` = 1 AND `o`.`received`= 1 ";
                    break;
                default:
                    # code...
                    break;
            }
        }
        //配送方式
        if ($express_type != '-1') {
            $whereStr .= " AND `o`.`express_type` = '{$express_type}'";
            $link_parameter .= '/express_type/' . $express_type;
        }
        //下单时间
        if ($startTime != '' && $endTime != '') {
            $whereStr .= " AND `o`.`pay_time` BETWEEN '{$startTime}' AND '{$endTime}'";
        } else {
            $startTime = strtotime('2015-1-1');
            $endTime = strtotime('+1 days');    
        }
        //商品名称
        if ( $goods_name != '' ) {
          $whereStr .= " AND `u`.`goods_name` LIKE '%{$goods_name}%'";
          $link_parameter .= '/goods_name/' . $goods_name;
        }
       
        $return['order_sn'] = $order_sn;
        $return['telephone'] = $telephone;
        $return['startTime'] = $startTime;
        $return['endTime']  = $endTime;
        $return['pay_state'] = $pay_state;
        $return['express_type'] = $express_type;
        $return['goods_name'] = $goods_name;
        
        $countSql = "SELECT count(*) AS `count` FROM `{$dbPrefix}point_order` AS `o`
                   LEFT JOIN  `{$dbPrefix}point_order_detail` AS `u` ON `u`.`order_sn` = `o`.`order_sn` 
                   LEFT JOIN `{$dbPrefix}order` AS `order` ON `order`.`order_sn` = `u`.`order_sn`
                   {$leftJoin}
                    WHERE 1{$whereStr}";
        $countData = $orderModel->query($countSql);
        
        $page = new \Think\Page($countData['0']['count'], 25);
        $show = $page->show();
        $counting=$page->totalRows;
        $sql = "SELECT `o`.*, `u`.`goods_name` AS `g`,`order`.`status`
                FROM `{$dbPrefix}point_order` AS `o`
                LEFT JOIN `{$dbPrefix}point_order_detail` AS `u` ON `u`.`order_sn` = `o`.`order_sn`
                LEFT JOIN `{$dbPrefix}order` AS `order` ON `order`.`order_sn` = `o`.`order_sn`
                {$leftJoin} 
                WHERE 1{$whereStr} 
                ORDER BY `o`.`id` DESC 
                LIMIT {$page->firstRow}, {$page->listRows}";
        $orderList = $orderModel->query($sql);
        foreach ($orderList as $key => $value) {
            $orderList[$key]['invoice_info']    = json_decode($value['invoice_info'], true);
            $orderList[$key]['goodsListPrice']  = $value['total'] - $value['carriage']+$value['order_discount_money'];
        }

        $this->assign('show', $show);
        $this->assign('orderList', $orderList);
        $this->assign('counting',$counting);
        $this->assign('return', $return);
        $this->display();
    }

    /**
     * [pointOrderDetail 订单详情]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function pointOrderDetail() {
        $id = I('get.id');
        $orderModel = M('point_order');
        $pointOrder = $orderModel->where(array('id'=>$id))->find();
        $returnData = M('point_order_detail')->where(array('order_sn'=>$pointOrder['order_sn']))->field('id, goods_id, goods_name, unit_price, price, goods_number')->select();
        $user= M('user')->where(array('id'=>$pointOrder['user_id']))->find();
        $this->assign('returnData', $returnData);
        $this->assign('user', $user);
        $this->assign('pointOrder', $pointOrder);
        $this->display();
    }
}