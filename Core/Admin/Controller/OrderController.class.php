<?php
namespace Admin\Controller;
use Think\Controller;
// 订单控制器
class OrderController extends BaseController {
    /**
     * [index 订单概况]
     * @author StanleyYuen <[350204080@qq.com]>
     */
    public function index() {
        $order = M('order');
        $orderReturn = M('order_goods_return');
        // 7天下单笔数
        $startTime = strtotime('-7 day');
        $endTime = time();
        $orderResult['sevenDaysOrder'] = $order->where(array('add_time'=> array('between', "$startTime, $endTime")))->count(); 

        // 待付款
        $orderResult['notPayOrder'] = $order->where(array('is_pay'=> 0, 'pay_type'=> 1, 'is_out_date'=> 0, 'is_delete'=> 0))->count(); 

        // 待发货
        $orderResult['notDeliveryOrder'] = $order->where("((`pay_type` = 0 AND `is_pay` = 0) or (`pay_type` = 1 AND `is_pay` = 1)) AND `delivery_status` = 0 AND `received` = 0 AND `is_delete` = 0 AND `is_out_date` = 0")->count(); 

        // 退换货
        $orderResult['returnOrder'] = $orderReturn->where(array('status'=>0, 'is_out_date'=> 0))->count();
        
        // 昨日下单笔数
        $lastDayTime = strtotime('-1 day');
        $orderResult['LastDaysOrder'] = $order->where(array('add_time'=> array('between', "$lastDayTime, $endTime")))->count(); 
        
        // 昨日付款笔数
        $orderResult['LastDaysPayOrder'] = $order->where(array('is_pay'=> 1, 'add_time'=> array('between', "$lastDayTime, $endTime")))->count(); 

        $this->assign('result', $orderResult);
        $this->display();
    }

    /**
     * [orderList 订单详情]
     * @author shichun <[672517056@qq.com]>  
     */
    public function orderList() {
        $dbPrefix = C('DB_PREFIX');
        $orderModel = M('order_detail');
        $whereStr = "";
        $leftJoin = "";
        $link_parameter = '';
        $order_sn     = I('get.order_sn','', 'urldecode'); 
        $startTime    = I('get.startTime'); 
        $endTime      = I('get.endTime'); 
        $status    = I('get.status','-1'); 
        $agent_name   = I('get.agent_name','','urldecode'); 
        $express_type    = I('get.express_type','-1'); 
        //订单号
        if ( !empty($order_sn)) {
            $whereStr .= " AND `od`.`order_sn` LIKE '%{$order_sn}%'";
            $link_parameter .= '/order_sn/' . $order_sn;
        }
        //配送方式
        if ($express_type != '-1') {
            $whereStr .= " AND `o`.`express_type` = '{$express_type}'";
            $link_parameter .= '/express_type/' . $express_type;
        }
        //订单状态
        if ( $status != '-1') {
            switch ($status) {
                case '0':
                    $whereStr .= " AND `o`.`is_out_date` = 1 ";
                    //已取消
                    break;
                case '1':
                    $whereStr .= " AND `o`.`is_pay` = 0 AND `o`.`is_out_date` = 0  ";
                    //待付款
                    break; 
                case '2':
                    $whereStr .= " AND `o`.`is_pay` = 1 AND `o`.`delivery_status`= 0 AND `o`.`received`= 0";
                    //待发货
                    break;
                case '3':
                    $whereStr .= " AND `o`.`is_pay` = 1 AND `o`.`delivery_status`= 1 AND `o`.`received`= 1 AND `o`.`is_comment` = 0 ";
                    //待评价
                    break;
                case '4':
                    $whereStr .= " AND `o`.`is_pay` = 1 AND `o`.`delivery_status`= 1 AND `o`.`received`= 0 ";
                    //待收货
                    break;
                case '5':
                    $whereStr .= " AND `o`.`is_pay` = 1 AND `o`.`delivery_status`= 1 AND `o`.`received`= 1 ";
                    //已收货
                    break;
                default:
                    # code...
                    break;
            }
            $link_parameter .= '/status/' . $status;
        }
        //下单时间
        if ($startTime != '' && $endTime != '') {
            $whereStr .= " AND `od`.`add_time` BETWEEN '{$startTime}' AND '{$endTime}'";
        } else {
            $startTime = strtotime('2016-1-1');
            $endTime = strtotime('+1 days');
        }
        //店铺搜索
        if ( !empty($agent_name)) {
            $whereStr   .= " AND `a`.`agent_name` LIKE '%{$agent_name}%' ";
            $link_parameter .= '/agent_name/' . $agent_name;
        }
       
        $return['order_sn'] = $order_sn;
        $return['startTime'] = $startTime;
        $return['endTime']  = $endTime;
        $return['status'] = $status;
        $return['agent_name'] = $agent_name;
        $return['express_type'] = $express_type;
     
        $countSql = "SELECT count(*) AS `count` 
                FROM `{$dbPrefix}order_detail` AS `od`
                LEFT JOIN `{$dbPrefix}order` AS `o` ON `o`.`order_sn` = `od`.`order_sn`
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `a`.`id` = `o`.`agent_id`
                    {$leftJoin}
                    WHERE 1{$whereStr} ";
        $countData = $orderModel->query($countSql);
        $page = new \Think\Page($countData['0']['count'], 25);
        $show = $page->show();
        $counting=$page->totalRows;
        $sql = "SELECT `od`.*,
               `a`.`agent_name`,`o`.`status`,`o`.`express_type`,`o`.`is_out_date`,`o`.`is_comment`,`o`.`is_pay`,`o`.`delivery_status`,`o`.`received`,`o`.`is_remind`
                FROM `{$dbPrefix}order_detail` AS `od`
                LEFT JOIN `{$dbPrefix}order` AS `o` ON `o`.`order_sn` = `od`.`order_sn`
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `a`.`id` = `o`.`agent_id`
                {$leftJoin} 
                WHERE 1{$whereStr} 
                ORDER BY `od`.`id` DESC 
                LIMIT {$page->firstRow}, {$page->listRows}";
        $orderList = $orderModel->query($sql);
        $userModel = M('user');
        foreach ($orderList as $key => &$value) {
            $value['nickname'] = $userModel->where(array('id'=> $value['user_id']))->getField('nickname');
        }
        
        $this->assign('show', $show);
        $this->assign('orderList', $orderList);
        $this->assign('counting',$counting);
        $this->assign('return', $return);
        $this->display();
    }

    /**
     * [ajaxOrderDetail ajax获取订单详情
     * @author StanleyYuen <350204080@qq.com>
     * @copyright Copyright (c) 2016 Xcrozz (http://www.xcrozz.com)
     */
    public function ajaxOrderDetail() {
        if (IS_POST) {
            $order_sn = I('post.order_sn');
            $whereArr = array(
                'order_sn' => $order_sn,
            );
            $returnData = M('order')->where($whereArr)->field('id,order_sn,user_id,address,telephone,consignee,status,express_type,is_out_date,is_pay,delivery_status,received,is_remind')->select();
            echo json_encode($returnData);
        }
    }

   
    /**
     * [OrderDetail 订单详情
     * @author shichun <672517056@qq.com>
     * @copyright Copyright (c) 2016 Xcrozz (http://www.xcrozz.com)
     */
    public function orderDetail() {
        $dbPrefix = C('DB_PREFIX');
        $id = I('get.id');
        $orderModel = M('order_detail');
        $orderDetail = $orderModel->where(array('id'=>$id))->find();
        $order_sn = $orderDetail['order_sn'];
        $returnData = M('order')->where(array('order_sn'=>$order_sn))->find();
        $user = M('user')->where(array('id'=>$returnData['user_id']))->find();
        $comment = M('goods_comment')->where(array('order_sn'=>$orderDetail['order_sn']))->field('contain,reply_contain')->find();
        $this->assign('returnData', $returnData);
        $this->assign('user', $user);
        $this->assign('comment', $comment);
        $this->assign('orderDetail', $orderDetail);
        $this->display();
    }

     /**
     * [export 订单导出]
     * @author shichun <672517056@qq.com>
     * @copyright Copyright (c)           2015 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     * @modify    xu 565657400@qq.com
     */
    public function export(){
        $id = I('get.ids','','htmlspecialchars');
        $field = I('get.field','','htmlspecialchars');

        $id = trim($id,',');
        $field = explode(',',trim($field,',')); //dump($fields);
        //根据ID查询
        // $ids['g.id'] = array('in',$id);
        $xlsName  = "订单表";
        // $xlsModel = M('order_detail');
        $dbPrefix = C('DB_PREFIX');
        //获取字段对应的注释
        $xlsCell = array(
            array('id','序号'),
            array('order_sn','订单编号'),
            array('add_time','下单时间'),
            array('goods_name','商品名称'),
            array('goods_type','商品型号'),
            array('goods_number','数量'),
            array('unit_price','商品单价'),
            array('price','实付金额'),
            array('nickname','用户昵称'),
            array('express_type','配送方式'),
            array('type','订单状态'),
            array('agent_name','店铺名称')
        );
        $sql = "SELECT `od`.*,
               `a`.`agent_name`,`o`.`status`,`o`.`express_type`,`o`.`is_out_date`,`o`.`is_comment`,`o`.`is_pay`,`o`.`delivery_status`,`o`.`received`,`o`.`is_remind`
                FROM `{$dbPrefix}order_detail` AS `od`
                LEFT JOIN `{$dbPrefix}order` AS `o` ON `o`.`order_sn` = `od`.`order_sn`
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `a`.`id` = `o`.`agent_id`
                {$leftJoin} WHERE 1{$whereStr} ORDER BY `od`.`id`";
        $xlsData = M()->query($sql);
        $xlsArr = array();
        foreach ($xlsData as $k => &$v)
        {   
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
            $nickname = M('user')->where(array('id'=>$v['id']))->field('nickname')->find();
            $v['nickname'] = $nickname['nickname'];
            //判断商品型号
            if ($v['goods_type'] == '0') {
                    $v['goods_type'] = "普通商品"; 
            }else if ($v['goods_type'] == '1') {
                    $v['goods_type'] = "赠品";  
            }else{
                    $v['goods_type'] = '套餐';
            }
            //判断订单状态
            if ($v['is_out_date'] == '1') {
                    $v['type'] = "已取消"; 
            }else if ($v['is_pay'] == '0' && $v['is_out_date'] == '0') {
                    $v['type'] = "待付款"; 
            }else if ($v['is_pay'] == '1' && $v['delivery_status'] == '0' && $v['received'] == '0') {
                    $v['type'] = "待发货";  
            }else if ($v['is_pay'] == '1' && $v['delivery_status'] == '1' && $v['received'] == '1' && $v['is_comment'] == '0') { 
                    $v['type'] = "待评价"; 
            }else if ($v['is_pay'] == '1' && $v['delivery_status'] == '1' && $v['received'] == '0') {
                    $v['type'] = "待收货";
            }else if ($v['is_pay'] == '1' && $v['delivery_status'] == '1' && $v['received'] == '1') { 
                    $v['type'] = "已收货"; 
            }
            $v['consigneeList'] = $v['province'].$v['city'].$v['county'].$v['address'];
            $xlsArr[$v['id']] = $v;
        }

        foreach($xlsArr as &$val)
        {   unset($val['id']);
            unset($val['is_pay']);
            unset($val['delivery_status']);
            unset($val['received']);
            unset($val['province']);
            unset($val['city']);
            unset($val['county']);
            unset($val['address']);
            
            $xlsDatas[] = $val;
        }

        exportExcel($xlsName,$xlsCell,$xlsData);
    }
    

    
    /**
     * [makePrice 修改价格
     * @author xu <565657400@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function makePrice(){
        $data     = I('post.');
        $price    = $data['make_price'];
        $id = $data['id'];
        if($price>0){
            $save = M('order')->where(array('id'=>$id))->data(array('make_price'=>$price))->save();
            if($save !== false){
                exit(json_encode(array('error'=>0,'msg'=>'修改成功！')));
            }
        }else{
            exit(json_encode(array('error'=>1,'msg'=>'修改失败！')));
        }
        
    }

     /**
     * [pointOrderList 积分订单]
     * @author xu <[565657400@qq.com]>   2016-9-13
     */
    public function pointOrderList() {
        $dbPrefix   = C('DB_PREFIX');
        $orderModel = M('order');
        $whereStr   = "";
        $leftJoin   = "";

        $order_sn     = I('get.order_sn',''); 
      
        $startTime    = I('get.startTime','');
        $endTime      = I('get.endTime',''); 
        $express_type    = I('get.express_type',''); 
        $agent_name   = I('get.agent_name',''); 
        $status  = I('get.status',''); 
        $link_parameter = '';
        if (!empty($order_sn) ) {
            $whereStr .= " AND `o`.`order_sn` LIKE '%{$order_sn}%'";
            $link_parameter .= '/goods_name/' . $goods_name;
        }
        if (!empty($status) ) {
            $whereStr .= " AND `o`.`status` = '{$status}'";
            $link_parameter .= '/status/' . $status;
        }
        if ( !empty($express_type)) {
          
            $whereStr .= " AND `o`.`express_type` = '{$express_type}'";
            $link_parameter .= '/express_type/' . $express_type;
        
        }
        if ($startTime != '' && $endTime != '') {
            $whereStr .= " AND `o`.`pay_time` BETWEEN '{$startTime}' AND '{$endTime}'";
        } else {
            $startTime = strtotime('2015-1-1');
            $endTime = strtotime('+1 days');
        }
        if ( !empty($agent_name)) {
            $whereStr .= " AND `0`.`agent_name` = '{$agent_name}'";
            $link_parameter .= '/agent_name/' . $agent_name;
        }
        
        $return['order_sn'] = $order_sn;
        $return['telephone'] = $telephone;
        $return['startTime'] = $startTime;
        $return['endTime']  = $endTime;
        $return['status'] = $status;
        $return['agent_name'] = $agent_name;
        $return['express_type'] = $express_type;

        $countSql = "SELECT count(*) AS `count` FROM `{$dbPrefix}point_order` AS `o`
                    {$userLeftJoin}
                    {$leftJoin}
                    WHERE 1{$whereStr} AND `is_delete` = '0'";
        $countData = $orderModel->query($countSql);

        $page = new \Think\Page($countData['0']['count'], 25);
        $show = $page->show();
        $counting=$page->totalRows;
        $sql = "SELECT `o`.*, 
                `u`.`username` AS `uname`, `u`.`nickname` AS `unickname`
                FROM `{$dbPrefix}order` AS `o`
                LEFT JOIN `{$dbPrefix}user` AS `u` ON `u`.`id` = `o`.`user_id`
                {$leftJoin}
                WHERE 1{$whereStr} AND `is_delete` = '0'
                ORDER BY `o`.`id` DESC
                LIMIT {$page->firstRow}, {$page->listRows}";
        $orderList = $orderModel->query($sql);
        foreach ($orderList as $key => $value) {
            $orderList[$key]['invoice_info']    = json_decode($value['invoice_info'], true);
            // $orderList[$key]['goodsListPrice']  = $value['total'] - $value['carriage'];
            $orderList[$key]['goodsListPrice']  = $value['total'] - $value['carriage']+$value['order_discount_money'];
        }

        $this->assign('show', $show);
        $this->assign('orderList', $orderList);
        $this->assign('counting',$counting);
        $this->assign('return', $return);
        $this->display();
    }

    /**
     * [pointOrderDetail 积分订单详情
     * @author xu <565657400@qq.com>
     * @copyright Copyright (c) 2016 Xcrozz (http://www.xcrozz.com)
     */
    public function pointOrderDetail() {
        $id = I('get.id');
        $orderModel  = M('point_order');
        $orderDetail = $orderModel->where(array('id'=>$id))->find();
        $returnData  = M('point_order_detail')->where(array('order_sn'=>$orderDetail['order_sn']))->field('id,goods_id, goods_name, unit_price, price, goods_number, goods_type,detection_type')->select();
        $user= M('user')->where(array('id'=>$orderDetail['user_id']))->find();
        $this->assign('returnData',$returnData);
        $this->assign('user',$user);
        $this->assign('orderDetail',$orderDetail);
        $this->display();
    }

    /**
     * [returnGoodsList 代理商售后订单列表]
     * @author xu <[565657400@qq.com]>
     * @modify kofu <[418382595@qq.com]> 2016/10/19 11:05:06
     */
    public function returnGoodsList() {
        $returnModel = M('order_goods_return');
        $dbPrefix = C('DB_PREFIX');
        $data = I('get.');
        $where =  " `r`.`status` = '1' AND `r`.`agent_id` = '1' AND `r`.`d_status` = '1' AND `r`.`received` = '1' AND `r`.`is_delete` = '0' ";
    if ( $data['order_sn'] != '') {
           // $where .= ' AND r.order_sn LIKE %'.$data["order_sn"].'%';
            $where .= " AND `r`.`order_sn` LIKE '%{$data['order_sn']}%'";
        }
        if ( $data['phone'] != '') {
           // $where .= ' AND u.phone = '.$data["phone"];
           $where .= " AND `u`.`phone` LIKE '%{$data['phone']}%'";
        }
        if ( $data['return_type'] != 0) {
           $where .= ' AND `r`.`return_type` = '.$data["return_type"];
        }
        if ( $data['sole_state'] != 0) {

            switch ($data['sole_state']) {
                // 待审核
                case '1':
                    $where .= ' AND `r`.`status` = 0';
                    break;
                // 待客户发货
                case '2':
                    if($data['return_type'] != 3){
                        $where .= ' AND `r`.`status` = 1 AND `r`.`d_status` = 0'; 
                    }
                    break;
                //待收货
                case '3':
                    if($data['return_type'] != 3){
                        $where .= ' AND `r`.`status` = 1 AND `r`.`d_status` = 1 AND `r`.`received` = 0'; 
                    } 
                    break;
                //待平台发货
                case '4':
                    if($data['return_type'] == 2){
                        $where .= ' AND `r`.`status` = 1 AND `r`.`d_status` = 1 AND `r`.`received` = 1 AND `r`.`return_d_status` = 0'; 
                    }
                    break;
                //待退款
                case '5':
                    if($data['return_type'] == 3){
                        $where .= ' AND `r`.`status` = 1';
                    }
                    if($data['return_type'] == 2){
                        $where .= ' AND `r`.`status` = 1 AND `r`.`d_status` = 1 AND `r`.`received` = 1';
                    }
                    break;
                 // 已成功
                 case '6':
                        $where .= ' AND `r`.`status` = 3';
                    break;
                // 已拒绝
                 case '7':
                        $where .= ' AND `r`.`status` = 2';
                    break;
               
            }
           
        }

       // $count   = $returnModel->where($where)->count();
        // $count = $returnModel->join(" AS r LEFT JOIN {$dbPrefix}user AS u ON `r`.user_id = `u`.id")->where($where)->count();

        // $page    = new \Think\Page($count, 20);
        
        $sql = 'SELECT count(*) AS `count` FROM '.$dbPrefix.'order_goods_return AS `r`
                LEFT JOIN '.$dbPrefix.'user AS `u` ON `r`.`user_id` = `u`.`id`
                WHERE'.$where.' LIMIT 1';
        $count = M()->query($sql);
        $count = $count[0]['count']?$count[0]['count']:0;
        $page    = new \Think\Page($count, 20);
        $show    = $page->show();
        $counting=$page->totalRows;
        // $returnGoodsList = $returnModel->join(" AS r LEFT JOIN {$dbPrefix}order AS od ON `r`.order_sn = `od`.order_sn")->join(" LEFT JOIN {$dbPrefix}user AS u ON `r`.user_id = `u`.id ")
        //                                ->field(' `r`.* , `od`.is_pay ,`od`.total , `od`.point_pay , `od`.real_pay , `od`.agent_id ,`od`.supplier_id , `u`.nickname ,`u`.phone ')
        //                                ->where($where)
        //                                ->order('`r`.update_time DESC')
        //                                ->limit("{$page->firstRow} ". ',' ." {$page->listRows}")
        //                                ->select();
        $sql = 'SELECT `r`.*, `od`.`is_pay` ,`od`.`total` , `od`.`point_pay` , `od`.`real_pay` , 
                `od`.`agent_id` ,`od`.`supplier_id` , `u`.`nickname` ,`u`.`phone` 
                FROM '.$dbPrefix.'order_goods_return AS `r`
                LEFT JOIN '.$dbPrefix.'order AS `od` ON `r`.`order_sn` = `od`.`order_sn`
                LEFT JOIN '.$dbPrefix.'user AS `u` ON `od`.`user_id` = `u`.`id`
                WHERE'.$where.' ORDER BY `r`.`update_time` DESC
                LIMIT '.$page->firstRow.','.$page->listRows;
        $returnGoodsList = M()->query($sql);
        $this->assign('show',$show);
        $this->assign('return',$data);
        $this->assign('returnGoodsList',$returnGoodsList);
        $this->assign('counting',$counting);         
        $this->display('returnGoodsList');
    }
    /**
     * [agreeReturn 同意
     * @author xu <565657400@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function agreeReturn(){
        if(IS_POST){
            $id = I('post.id');
            $save = M('order_goods_return')->where(array('id'=>$id))->data(array('status'=>1))->save();
            if($save !== false){
                exit(json_encode(array('error'=>0,'msg'=>'操作成功！')));
            }
            
        }else{
            exit(json_encode(array('error'=>1,'msg'=>'非法操作')));
        }
    }

    /**
     * [refundReturn 退款
     * @author xu <565657400@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function refundReturn(){
        if(IS_POST){
            $id = I('post.id');
            $save = M('order_goods_return')->where(array('id'=>$id))->data(array('status'=>3))->save();
            if($save !== false){
                exit(json_encode(array('error'=>0,'msg'=>'操作成功！')));
            }
            
        }else{
            exit(json_encode(array('error'=>1,'msg'=>'非法操作')));
        }
    }

    /**
     * [refuseReturn 拒绝退换货款
     * @author xu <565657400@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function refuseReturn(){
        if(IS_POST){
            $id = I('post.id');
            $refuse_season = I('post.refuse_season');
            $save = M('order_goods_return')->where(array('id'=>$id))->data(array('status'=>2,'refuse_season'=>$refuse_season))->save();
            if($save !== false){
                exit(json_encode(array('error'=>0,'msg'=>'操作成功！')));
            }
            
        }else{
            exit(json_encode(array('error'=>1,'msg'=>'非法操作')));
        }
    }

    /**
     * [receivedReturn 确认收货
     * @author xu <565657400@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function receivedReturn(){
        if(IS_POST){
            $id = I('post.id');
            $save = M('order_goods_return')->where(array('id'=>$id))->data(array('received'=>1))->save();
            if($save !== false){
                exit(json_encode(array('error'=>0,'msg'=>'操作成功！')));
            }
            
        }else{
            exit(json_encode(array('error'=>1,'msg'=>'非法操作')));
        }
    }

    /**
     * [deliveryReturn 平台发货(换货)
     * @author xu <565657400@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function deliveryReturn(){
        if(IS_POST){
            $data = I('post.');
            $save = M('order_goods_return')->where(array('id'=>$data['id']))->data(array('return_express'=>$data['return_express'],'return_express_sn'=>$data['return_express_sn'],'return_d_status'=>1))->save();
            if($save !== false){
                exit(json_encode(array('error'=>0,'msg'=>'操作成功！')));
            }
            
        }else{
            exit(json_encode(array('error'=>1,'msg'=>'非法操作')));
        }
    }

    /**
     * [PointDelivery 积分订单发货
     * @author xu <565657400@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function PointDelivery(){
        if(IS_POST){
            $data = I('post.');
            $save = M('point_order')->where(array('id'=>$data['id']))->data(array('delivery_status'=>1,'express'=>$data['express'],'express_sn'=>$data['express_sn']))->save();
            if($save !== false){
                exit(json_encode(array('error'=>0,'msg'=>'操作成功！')));
            }
            
        }else{
            exit(json_encode(array('error'=>1,'msg'=>'非法操作')));
        }
    }
    
    /**
     * [editReturnGoods 查看售后详情]
     * @author xu <[565657400@qq.com]>
     */
    public function editReturnGoods() {
        $type = I('get.type', 0);
        $id   = I('get.id');
        $returnModel = M('order_goods_return')->where(array('id'=>$id))->find();
        $returnOrder = M('order')->where(array('order_sn'=>$returnModel['order_sn']))->find();
        $returnModel['image'] = json_decode($returnModel['image']);
        $returnUser = M('user')->where(array('id'=>$returnOrder['user_id']))->find();
        $returnData = M('order_detail')->where(array('order_sn'=>$returnModel['order_sn']))->field('id,goods_id, goods_name, unit_price, price, goods_number, goods_type,detection_type')->select();

        $this->assign('type', $type);
        $this->assign('returnOrder',$returnOrder);
        $this->assign('returnModel',$returnModel);
       // $this->assign('returnModelDetail',$returnModelDetail);
        $this->assign('returnUser',$returnUser);
        $this->assign('returnData',$returnData);
        $this->display('editReturnGoods');
    }

    /**
     * [bindBarcode 检测商品绑定二维码]
     * @author StanleyYuen <[350204080@qq.com]>
     *   fix xu 565657400@qq.com 9/26
     */
    public function bindBarcode() {
        if (IS_POST) {
            $barcode = I('post.barcode', '');
            $id = I('post.id', '', 'int');
            $order_sn = I('post.order_sn');
            $id ? '' : (exit(json_encode(array('error'=>1, 'msg'=>'参数丢失！'))));
            $orderDetailModel = M('order_detail');
            $save = $orderDetailModel->where(array('id'=>$id))->save(array('barcode'=>$barcode,'detection_type'=>2)); 
            if($save !== false){
                $status = $orderDetailModel->where(array('order_sn'=>$order_sn ,'detection_type'=>1))->count();
                if(empty($status)){
                     M('order')->where(array('order_sn'=>$order_sn))->data(array('detection_status'=>2))->save();   
                }
                exit(json_encode(array('error'=>0)));
            }else{
                exit(json_encode(array('error'=>1, 'msg'=>'绑定失败！')));
            }
        }
    }

    /**
     * [pointBindBarcode 检测商品绑定二维码](积分商品)
     * @author StanleyYuen <[350204080@qq.com]>
     *   fix xu 565657400@qq.com 9/26
     */
    public function pointBindBarcode() {
        if (IS_POST) {
            $barcode = I('post.barcode', '');
            $id = I('post.id', '', 'int');
            $order_sn = I('post.order_sn');
            $id ? '' : (exit(json_encode(array('error'=>1, 'msg'=>'参数丢失！'))));
            $orderDetailModel = M('point_order_detail');
            $save = $orderDetailModel->where(array('id'=>$id))->save(array('barcode'=>$barcode,'detection_type'=>2)); 
            if($save !== false){
                $status = $orderDetailModel->where(array('order_sn'=>$order_sn ,'detection_type'=>1))->count();
                if(empty($status)){
                     M('point_order')->where(array('order_sn'=>$order_sn))->data(array('detection_status'=>2))->save();   
                }
                exit(json_encode(array('error'=>0)));
            }else{
                exit(json_encode(array('error'=>1, 'msg'=>'绑定失败！')));
            }
        }
    }
    /**
     * [returnBindBarcode 检测商品绑定二维码](售后换货)
     * @author StanleyYuen <[350204080@qq.com]>
     *   fix xu 565657400@qq.com 9/26
     */
    public function returnBindBarcode() {
        if (IS_POST) {
            $barcode = I('post.barcode', '');
            $id = I('post.id', '', 'int');
            $order_sn = I('post.order_sn');
            $id ? '' : (exit(json_encode(array('error'=>1, 'msg'=>'参数丢失！'))));
            $orderDetailModel = M('order_detail');
            $save = $orderDetailModel->where(array('id'=>$id))->save(array('barcode'=>$barcode,'detection_type'=>2)); 
            if($save !== false){
                $status = $orderDetailModel->where(array('order_sn'=>$order_sn ,'detection_type'=>1))->count();
                if(empty($status)){
                     M('order')->where(array('order_sn'=>$order_sn))->data(array('detection_status'=>2))->save();   
                }
                exit(json_encode(array('error'=>0)));
            }else{
                exit(json_encode(array('error'=>1, 'msg'=>'绑定失败！')));
            }
        }
    }

   


    /**
     * [exportReturn 售后订单导出]
     * @author xu <565657400@qq.com>
     * @copyright Copyright (c)           2015 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function exportReturn()
    {
        $id = I('get.ids','','htmlspecialchars');
            
        $id = trim($id,',');
        
        //根据ID查询
        // $ids['g.id'] = array('in',$id);
        $xlsName  = "售后订单表";
        // $xlsModel = M('order_detail');
        $dbPrefix = C('DB_PREFIX');
        //获取字段对应的注释
        $xlsCell = array(
            array('id','id序号'),
            array('order_sn','订单号'),
            array('update_time','操作时间'),
            array('total','订单金额'),
            array('real_pay','实际支付'),
            array('point_pay','所用积分'),
            array('nickname','用户昵称'),
            array('phone','用户账户'),
            array('return_type','售后类型'),
            array('agent_id','卖家'),
            array('supplier_id','供应商'),
            array('status','售后状态')
        );
        $sql = 'SELECT `r`.*, `od`.`is_pay` ,`od`.`total` , `od`.`point_pay` , `od`.`real_pay` , 
                `od`.`agent_id` ,`od`.`supplier_id` , `u`.`nickname` ,`u`.`phone` 
                FROM '.$dbPrefix.'order_goods_return AS `r`
                LEFT JOIN '.$dbPrefix.'order AS `od` ON `r`.`order_sn` = `od`.`order_sn`
                LEFT JOIN '.$dbPrefix.'user AS `u` ON `od`.`user_id` = `u`.`id`
                WHERE `r`.`id` IN ('.$id.')';
        $xlsData = M()->query($sql);
        foreach ($xlsData as $k => &$v)
        {   
           $v['update_time'] = date('Y-m-d H:i:s' , $v['update_time']);
           $v['real_pay']    = empty($v['is_pay'])? '未支付':$v['real_pay'];
           $v['return_type'] = $v['return_type'] == 2?'退货退款':'退款';
           $v['agent_id']    = getAgentname($v['agent_id']);
           $v['supplier_id']    = getAgentname($v['supplier_id']);
           switch ($v['status']) {
               case '1':
                    if($v['return_type'] == 3){
                        $v['status'] = '待退款';
                    } elseif ($v['d_status'] == 0) {
                        $v['status'] =='待客户发货';
                    } elseif ($v['received'] == 0) {
                        $v['status'] =='待收货';
                    } elseif ($v['return_type'] == 1) {
                        if($v['return_d_status'] == 1){
                            $v['status'] =='换货成功';
                        }else{
                            $v['status'] =='待供货商发货';
                        }
                    }else{
                       $v['status'] = '待退款' ;
                    }
                   break;
                case '2':
                    $v['status'] = '已拒绝';
                    break;
                case '3':
                    $v['status'] = '已成功';
                    break;
               default:
                    $v['status'] = '待审核';
                   break;
           }
       
            
        }
        exportExcel($xlsName,$xlsCell,$xlsData);
    }

}