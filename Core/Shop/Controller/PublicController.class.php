<?php
namespace Shop\Controller;
use Think\Controller;
use Plugins\Message\Message;
use Plugins\Money\Money;
use Plugins\Point\Point;
use Think\Image;
use Plugins\Huanxin\Easemob;
class PublicController extends Controller {
    /**
     * [hourCheck 每小时检测]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function hourCheck() {
        $this->autoAuditGoods();
        $this->checkRecommendGoods();
        $this->checkAuthGoods();
    }

    /**
     * [dayCheck 每天检测]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function dayCheck() {
        // 更新用户每日收益
        // $this->checkUserEarning();
        // 更新竞价状态
        $this->checkBiddingStatus();
        // 更新推荐商品
        $this->checkRecommendGoods();
        $this->checkFavorableGoods();
        $this->checkAuthGoods();
        $this->checkRecommendAgent();
    }

    /**
     * [checkBiddingStatus 更新竞价状态]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function checkBiddingStatus() {
        $biddingModel = M('bidding');
        $list = $biddingModel->where(array('status'=> '1'))->select();
        $time = time();
        foreach ($list as $key => $value) {
            if ( $value['end_time'] <= $time ) {
                $value['status'] = '3';
                $biddingModel->save($value);
            }
        }
        $where = array(
            'is_pay' => '1',
            'start_time' => array('GT', $time),
            'end_time' => array('LT', $time + 86400),
            'status' => '0',
        );
        $list = $biddingModel->where($where)->select();
        foreach ($variable as $key => $value) {
            $value['status'] = '1';
            $biddingModel->save($value);
        }
    }

    /**
     * [checkUserEarning 更新用户每日收益]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function checkUserEarning() {
        $user = M('user');
        $money = new Money();
        $valueAddedRatio = C('valueAddedRatio');
        $valueAddedRatio = empty($valueAddedRatio) ? 1/100 : $valueAddedRatio/100;
        $list = $user->where(array('money'=> array('EGT', '1')))->select();
        foreach ($list as $key => $value) {
            $earning = bcmul($value['money'], $valueAddedRatio, 2);
            $earning = empty($earning) ? 0.1 : $earning;
            $earning = $earning == '0.00' ? 0.1 : $earning;
            // 记录用户收益记录
            $parameter = array(
                'to_id'     => $value['id'],
                'money'     => $earning,
                'type'      => 0,
                'eventType' => 2,
            );
            if ( $money->money($parameter) === false ) {
                file_put_contents('checkUserEarning.txt', '', FILE_APPEND);
            }

            $saveData = array(
                'id' => $value['id'],
                'money' => $value['money'] + $earning,
                'last_earning' => $earning,
                'all_earning' => $value['all_earning'] + $earning
            );
            if ( $user->save($saveData) === false ) {
                file_put_contents('checkUserEarning.txt', '', FILE_APPEND);   
            }
            unset($parameter, $saveData);
        }
    }

    /**
     * [checkRecommendGoods 更新推荐商品]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function checkRecommendGoods() {
        $goodsModel = M('goods');
        $goodsList = $goodsModel->where(array('is_recommend'=> '1'))->select();
        $time = time();
        foreach ($goodsList as $key => &$value) {
            if ( $value['recommend_finish_time'] <= $time ) {
                $value['is_recommend'] = '0';
                $value['bidding_money'] = '0';
                $goodsModel->save($value);
            }
        }
    }

    public function checkFavorableGoods() {
        $time = time();
        $goodsModel = M('goods');
        $goodsModel->where(['is_delete'=> '0'])->save(['is_favorable'=> '0']);
        $where = [
            'favorable_start_time' => ['ELT', $time],
            'favorable_end_time' => ['EGT', $time],
        ];
        $list = $goodsModel->where($where)->select();
        foreach ($list as $value) {
            $value['is_favorable'] = '1';
            $value['bidding_money'] = '0';
            $goodsModel->save($value);
        }
    }

    public function checkAuthGoods() 
    {
        $time = time();
        $goodsModel = M('goods');
        $goodsModel->where(['is_delete'=> '0'])->save(['is_auth'=> '0']);
        $where = [
            'auth_finish_time' => ['EGT', $time],
        ];
        $list = $goodsModel->where($where)->select();
        foreach ($list as $value) {
            $value['is_auth'] = '1';
            $value['bidding_money'] = '0';
            $goodsModel->save($value);
        }
    }

    public function checkRecommendAgent() {
        $time = time();
        $agentModel = M('agent');
        $list = $agentModel->where(array('end_show_time'=> array('ELT', $time)))->select();
        foreach ($list as $key => &$value) {
            $value['bidding_money'] = '0';
            $agentModel->save($value);
        }
    }

    public function autoAuditGoods()
    {
        $goodsCheckModel = M('goods_check');
        $list = $goodsCheckModel->where(['status'=>'0', 'is_pay'=>'1'])->select();
        $time = time();
        foreach ($list as $key => $value) {
            if ( $value['auto_audit_time'] <= $time ) {
                $value['status'] = '1';
                $goodsCheckModel->save($value);
                switch ($value['check_type']) {
                    case '0':
                        $saveData = array(
                            'id' => $value['goods_id'],
                            'is_auth' => '1',
                            'auth_finish_time' => $value['end_time']
                        );
                        M('goods')->save($saveData);
                        break;
                    
                    case '1':
                        $saveData = array(
                            'id' => $value['goods_id'],
                            'is_recommend' => '1',
                            'recommend_finish_time' => $value['end_time']
                        );
                        M('goods')->save($saveData);
                        break;
                }
            }
            
        }
    }

    /**
     * [processCallback 处理统一回调]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
     * @param     [type]        $parameter [description]
     * @return    [type]                   [description]
     */
    public function processCallback($parameter) 
    {
        $order_sn = !empty($parameter['order_sn']) ? $parameter['order_sn'] : '';
        $total_fee = !empty($parameter['total_fee']) ? $parameter['total_fee'] : '';
        $pay_type = !empty($parameter['pay_type']) ? $parameter['pay_type'] : '';
        $merchant_sn = !empty($parameter['merchant_sn']) ? $parameter['merchant_sn'] : '';
        $pay_sn = !empty($parameter['pay_sn']) ? $parameter['pay_sn'] : '';
        $merorderno = !empty($parameter['merorderno']) ? $parameter['merorderno'] : '';

        $goods = M('goods');
        $order = M('order');
        $orderDetail = M('order_detail');
        $dbPrefix = C('DB_PREFIX');
        //order详细信息
        $where['order_sn'] = $order_sn;
        $order_information = $order->where($where)->find();
        if ( empty($order_information) ) {
            return;
        }
        if ( intval($order_information['is_pay']) === 1) {
            return;
        }
        $total = (float)$order_information['total'];
        $total_fee = (float)$total_fee;
        if ( $total !== $total_fee ) {
            return;
        }
  
        unset($total);
        $list = $orderDetail->where($where)->select();
        $expressData = array();
        $messageData = array();
        $message = json_decode($order_information['message'], true);
        $expressContent = json_decode($order_information['express_content'], true);
        foreach ($message as $key => $value) {
            $messageData[$value['agent_id']] = $value['message'];
        }
        foreach ($expressContent as $key => $value) {
            $expressData[$value['agent_id']] = $value['express_type'];
        }

        // 开启事务
        $result = true;
        $mysql = M();
        $mysql->startTrans();
        switch ($pay_type) {
            case '1' :
                $saveData['pay_remark'] = "支付宝支付{$total_fee}";
                $saveData['merchant_sn'] = $merchant_sn;
                $saveData['pay_sn'] = $pay_sn;
                // $saveData['merorderno'] = $merorderno;
                break;
            case '2' :
                $saveData['pay_remark'] = "微信支付{$total_fee}";
                $saveData['merchant_sn'] = $merchant_sn;
                $saveData['pay_sn'] = $pay_sn;
                // $saveData['merorderno'] = $merorderno;
                break;
            case '3' :
                $saveData['pay_remark'] = "银联支付{$total_fee}";
                $saveData['merchant_sn'] = $merchant_sn;
                $saveData['pay_sn'] = $pay_sn;
                // $saveData['merorderno'] = $merorderno;
                break;
            case '4' :
                $saveData['pay_remark'] = "paypal支付{$total_fee}";
                // $saveData['merchant_sn'] = $merchant_sn;
                // $saveData['pay_sn'] = $pay_sn;
                // $saveData['merorderno'] = $merorderno;
                break;
        }
        $saveData['is_pay'] = '1';
        $saveData['pay_time'] = time();
        $saveData['real_pay'] = $total_fee;
        $saveData['pay_type'] = $pay_type;
        $saveData['is_split'] = 0;  // 是否拆分
        $saveData['status']   = '1';
        $classify_order = array(); // 供应商所对应的子单
        foreach ($list as $key => $value) {
            // 扣减库存
            if ( $goods->where(array('id' => $value['goods_id']))->setDec('goods_number', $value['goods_number'] ) === false) {
                $result = false;
            }

            // 增加销量
            if ( $goods->where(array('id' => $value['goods_id']))->setInc('sale_number', $value['goods_number'] ) === false) {
                $result = false;
            }            

            // 构造拆分子单数据，以供应商id对应子单的键形式，例'1'=> '0,1', '2'=> '2'
            $supplier_id = $goods->where(array('id' => $value['goods_id']))->getField('supplier_id');
            if (array_key_exists($supplier_id, $classify_order)) {
                $childIds = $classify_order[$supplier_id];
                $classify_order[$supplier_id] = $childIds . ',' . $key;
                unset($childIds);
            } else {
                $classify_order[$supplier_id] = $key;
            }
            unset($supplier_id);
        }

        // 拆分子单 
        $count = count($classify_order);
        $agentMoneyData = array();
        if (!empty($classify_order) && $count > 1) {
            foreach ($classify_order as $key => $value) {
                $childSn = $this->makeOrderSn();
                $data = array();
                $total = 0;        // 总计
                $supplier_id = $key;     // 供应商id
                $freight = 0;        // 运费

                if (strpos($value, ',') !== false) {
                    $orderIds = explode(',', $value);
                    foreach ($orderIds as $ids_key => $ids_value) {
                        $orderInfo = $list[$ids_value];
                        $orderInfo['order_sn'] = $childSn;
                        unset($orderInfo['id']);
                        $total += $orderInfo['price'];
                        $data[] = $orderInfo;
                        unset($orderInfo);
                    }
                    $return = $orderDetail->addAll($data);
                } else {
                    $orderInfo = $list[$value];
                    $data = $orderInfo;
                    $data['order_sn'] = $childSn;
                    unset($data['id']);
                    $total += $orderInfo['price'];
                    $return = $orderDetail->add($data);
                    unset($orderInfo);
                }
                unset($data);
                // $supplier_freight = freight($supplier_id);
                // if ($total < $supplier_freight['free_price']) {
                //     $freight = $supplier_freight['original_price'];
                //     $total += $freight;
                // }
                if ( $return !== false ) {
                    $data = $order_information;
                    $data['agent_id'] = $supplier_id;
                    $data['order_sn'] = $childSn;
                    $data['merchant_sn'] = $merchant_sn;
                    $data['pay_sn'] = $pay_sn;
                    $data['is_pay'] = '1';
                    $data['pay_time'] = time();
                    $data['total'] = $total;
                    $data['carriage'] = $freight;
                    $data['supplier_id'] = $supplier_id;
                    $data['pay_type'] = $pay_type;
                    $data['is_split'] = '1';
                    $data['real_pay'] = $total;
                    $data['status']   = '1';
                    switch ($pay_type) {
                        case '1' :
                            $data['pay_remark'] = "支付宝支付{$total}";
                            break;
                        case '2' :
                            $data['pay_remark'] = "微信支付{$total}";
                            break;
                        case '3' :
                            $data['pay_remark'] = "银联支付{$total}";
                            break;
                        case '4' :
                            $data['pay_remark'] = "paypal支付{$total}";
                            break;
                    }
                    $data['express_type'] = $expressData[$supplier_id];
                    $data['buyer_message'] = !empty($messageData[$supplier_id]) ? $messageData[$supplier_id] : '';
                    unset($data['id']);

                    if ( $order->add($data) === false ) {
                        $result = false;
                    }
                    unset($data, $freight);
                    $saveData['is_split'] = 2;
                } else {
                    $result = false;
                }
                $agentMoneyData[$supplier_id]['total'] = $total;
                $agentMoneyData[$supplier_id]['order_sn'] = $childSn;
            }
        } else {
            $saveData['supplier_id'] = key($classify_order);
            $saveData['agent_id'] = $saveData['supplier_id'];
            $saveData['express_type'] = $expressData[$saveData['supplier_id']];
            $saveData['buyer_message'] = !empty($messageData[$saveData['supplier_id']]) ? $messageData[$saveData['supplier_id']] : '';
            $agentMoneyData[$saveData['supplier_id']]['total'] = $total_fee;
            $agentMoneyData[$saveData['supplier_id']]['order_sn'] = $order_sn;
        }
        // 结算商家记录
        $agentModel = D('Agent');
        $userModel = M('user');
        $agentSaleModel = M('agent_sales_statistics');
        $point = new Point();
        //结算金钱记录
        $money = new Money();
        foreach ($agentMoneyData as $key => $value) {
            $supplier_id = $key;
            $agentData = $agentModel->getAgentInfo($supplier_id, '`user_id`');
            $userId = $agentData['user_id'];
            $parameter1 = array(
                'from_id'       => $order_information['user_id'],
                'to_id'         => $userId,
                'eventType'     => '0',
                'type'          => '0',
                'money'         => $value['total'],
                'order_sn'      => $value['order_sn'],
                'description'   => "Order form+RM{$value['total']} Order number:{$value['order_sn']}",
            );
            if ( $money->money($parameter1) === false ) {
                $result = false;
            }
            // 记录用户消费记录
            $parameter2 = array(
                'from_id'       => $userId,
                'to_id'         => $order_information['user_id'],
                'eventType'     => '0',
                'type'          => '1',
                'money'         => $value['total'],
                'processMoney'  => false,
                'order_sn'      => $value['order_sn'],
                'description'   => "Payment-RM{$value['total']}-Order number:{$value['order_sn']}",
            );
            if ( $money->money($parameter2) === false ) {
                $result = false;
            }
            // 增加店铺订单数量
            if ( $userModel->where(array('id'=> $userId))->setInc('order_number') === false ) {
                $result = false;
            }
            //增加店铺收入
            if( $agentModel->where(array('id'=>$supplier_id))->setInc('all_income',$value['total']) === false){
                $result = false;
            }
            //增加店铺销量
            $goods_number = $orderDetail->where(array('order_sn'=>$value['order_sn']))->
            field('SUM(`goods_number`) AS `goods_number`')->find();
            $goods_number = !empty($goods_number['goods_number']) ? $goods_number['goods_number'] : 1;
            if( $agentModel->where(array('id'=>$supplier_id))->setInc('all_sales',$goods_number) === false ){
                $result = false;
            }
            //添加agent_sale_static表中记录
            $date = date('Ym');
            $saleData = $agentSaleModel->where(array('date'=>$date))->find();
            if( empty($saleData)){
                //表中无记录
                $saleData = array(
                    'agent_id' => $supplier_id,
                    'month_sales' => $goods_number,
                    'month_income' => $value['total'],
                    'date' => $date,
                    'add_time' => time(),
                    );
                if( $agentSaleModel->add($saleData) === false){
                    $result = false;
                }
            }else{
                //表中有记录
                $saleSaveData = $saleData;
                $saleSaveData['month_sales'] = $saleSaveData['month_sales'] + $goods_number;
                $saleSaveData['month_income'] = $saleSaveData['month_income'] + $value['total'];
                if ( $agentSaleModel->where(array('id'=>$saleData['id']))->save($saleSaveData) === false ) {
                    $result = false;
                }
            }
            // 卖家完成订单获取积分
            $finishOrderPoint = C('finishOrderPoint');
            $finishOrderPoint = !empty($finishOrderPoint) ? $finishOrderPoint * $value['total'] : $value['total'];
            $parameter = array(
                'from_id' => '0',
                'to_id' => $userId,
                'num' => $finishOrderPoint,
                'type' => '1',
                'event_type' => '5',
                'order_sn' => $value['order_sn'],
                'description' => "Shopping+{$finishOrderPoint}point(s)",
            );

            if ( $point->point($parameter) === false ) {
                $result = false;
            }
        }

        // 消费赠送积分
        $saleGetPoint = $order_information['giving_point'];
        $parameter = array(
            'from_id' => '0',
            'to_id' => $order_information['user_id'],
            'num' => $saleGetPoint,
            'type' => '1',
            'event_type' => '2',
            'order_sn' => $order_information['order_sn'],
            'description' => "Shopping+{$saleGetPoint}point(s)",
        );

        if ( $point->point($parameter) === false ) {
            $result = false;
        }

        if ( $order->where(array('order_sn' => $order_sn))->save($saveData) === false ) {
            $result = false;
        }

        if ( $result !== false ) {
            $mysql->commit();
        } else {
            $mysql->rollback();
        }
    }

    public function makeOrderSn() {
        // //订单生成规则
        $order = M('order');
        $orderId = $order->field('max(id) as `maxId`')->find(); //得到订单的最大自增ID
        $tempStr = rand(1000, 9999) . $orderId['maxId'];        //生成随机数
        $masterOrder = C('CHANNEL') . date('ymd') . str_pad($tempStr, 10, "0", STR_PAD_LEFT);          //合并成订单号

        //如果有重复的订单号的话，则回滚
        if($order->where(array('order_sn'=> $masterOrder))->count() > 0) {
            return $this->makeOrderSn();
        }
        return $masterOrder;
    }

    /**
     * [photoUpload 上传图片]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function photoUpload() {
        if (IS_POST) {
            $dir = I('post.dir');
            $image = I('post.image');
            if (empty($image)) {
                exit(statusCode(array(), 400000, '请上传图片！'));
            }
            if ( $this->checkDir($dir) === false ) {
                exit(statusCode(array(), 400000, '请选择正确的文件夹！'));
            }

            $imageData = base64_upload($image, $dir); //保存图片
            if ( !$imageData['boolean'] ) {
                exit(statusCode(array(), $imageData['status']));
            } else {
                exit(statusCode(array('image'=> $imageData['data'])));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    private function checkDir($dir) {
        $data = array('Agent', 'Comment', 'User');
        if ( in_array($dir, $data) ) {
            return true;
        } else {
            return false;
        }
    }

    public function buildBase64Thumb() {
        // $this_img = I('request.img');
        $this_img = 'http://i5.hexunimg.cn/2016-09-12/185985253.jpg';
        header("content-type:image/png");
        $imgg = $this->yuan_img($this_img);
        imagepng($imgg, './test.png');
        imagedestroy($imgg);
    }

    function yuan_img($imgpath = './tx.jpg') {
        $ext     = pathinfo($imgpath);
        $src_img = null;
        switch ($ext['extension']) {
        case 'jpg':
            $src_img = imagecreatefromjpeg($imgpath);
            break;
        case 'png':
            $src_img = imagecreatefrompng($imgpath);
            break;
        }
        $wh  = getimagesize($imgpath);
        $w   = $wh[0];
        $h   = $wh[1];
        $w   = min($w, $h);
        $h   = $w;
        $img = imagecreatetruecolor($w, $h);
        //这一句一定要有
        imagesavealpha($img, true);
        //拾取一个完全透明的颜色,最后一个参数127为全透明
        $bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
        imagefill($img, 0, 0, $bg);
        $r   = $w / 2; //圆半径
        $y_x = $r; //圆心X坐标
        $y_y = $r; //圆心Y坐标
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgbColor = imagecolorat($src_img, $x, $y);
                if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
            }
        }
        return $img;
    }

    /**
     * [getChildZone 得到下级地区]
     * @author StanleyYuen <[350204080@qq.com]>
    */
    public function getChildZone() {
        if ( IS_POST ) {
            $pid = I('post.pid');

            if ( empty($pid) ) {
                $list = array();
            } else {
                $list = M('region')->where(array('pid'=>$pid))->select();
                $list = !empty($list)? $list : array();
            }

            exit(statusCode(array('list'=> $list)));
        }
    }

    /**
     * [checkPayment 检测支付状态]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function checkPayment() {
        $sign = I('sign', '', 'string');
        $order_sn = I('order_sn', '', 'string');
        switch ( $sign ) {
            case 'bidding':
                $biddingModel = M('bidding');
                $is_pay = $biddingModel->where(array('order_sn'=> $order_sn))->getField('is_pay');
                break;
            case 'goodscheck':
                $goodsCheckModel = M('goods_check');
                $is_pay = $goodsCheckModel->where(array('order_sn'=> $order_sn))->getField('is_pay');
                break;
            case 'issuing':
                $messageCheckModel = M('message_check');
                $is_pay = $messageCheckModel->where(array('order_sn'=> $order_sn))->getField('is_pay');
                break;
            case 'issuing':
                $goodsForEditModel = M('goods_for_edit');
                $is_pay = $goodsForEditModel->where(array('order_sn'=> $order_sn))->getField('is_pay');
                break;
        }
        $returnData['payment'] = ( $is_pay == '1' ) ? 'paid' : 'notpay';
        exit(statusCode($returnData));
    }

   /**
    * [imRegister 把用户表注册环信]
    * @author kofu <418382595@qq.com>
    * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
    * @return    [type]        [description]
    */
    public function imRegister(){
        $list = M('user')->field('id,nickname')->select();
        $options = imConf();
        $Easemob = new Easemob($options);
        foreach ($list as $key => $value) {
            $username = "wedo{$value['id']}";
            $save = $Easemob->createUser($username,$username,$value['nickname']);
        }
        exit;
    }

    public function test($id,$type=7) {
        $options = imConf();
        $Easemob = new Easemob($options);
        $ext = new \stdClass();
        $ext->type = $type;
        if ( $type == '7' ) {
            $ext->order_sn = '123456789123456789';
        } elseif( $type == '8' ) {
            $ext->id = '1';
        }
        $result = $Easemob->sendText('wedoAdmin', 'users', array('wedo'.$id), 'Your order has been shipped', $ext);
        dump($result);
    }

    /**
     * [getServiceTel 平台客服电话]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function getServiceTel() {
        $c = M('config')->getField('config_sign,config_value');
        C($c);
        exit(statusCode(array('list'=> C('serviceTel'))));
    }

    public function cc() {
        M('goods')->where(['goods_main_id'=>'0'])->save(['comment_number'=>'0']);
        $list = M('goods_comment')->field('goods_id')->select();
        foreach ($list as $key => $value) {
            $goodsData = M('goods')->where(['id'=>$value['goods_id']])->field('id,goods_main_id')->find();
            if ( !empty($goodsData['goods_main_id']) ) {
                M('goods')->where(['id'=>$goodsData['goods_main_id']])->setInc('comment_number');
            } else {
                M('goods')->where(['id'=>$goodsData['id']])->setInc('comment_number');
            }
        }
    }

    public function remove() {
        $time = time();
        $where = "(favorable_end_time < {$time} OR auth_finish_time < {$time} OR recommend_finish_time < {$time}) AND bidding_money != '0'";
        $list = M('goods')->where($where)->select();
        foreach ($list as $key => $value) {
            $value['bidding_money'] = '0';
            M('goods')->save($value);
        }
    }

    public function gtp() {
        $list = M('goods_to_point')->select();
        foreach ($list as $key => $value) {
            $value['status'] = '1';
            M('goods_to_point')->save($value);
            M('goods')->where(['id'=>$value['goods_id']])->save(['goods_type'=>'1']);
            M('goods')->where(['goods_main_id'=>$value['goods_id']])->save(['goods_type'=>'1']);
        }
    }

    public function updateAgentStatistics()
    {
        $where = [
            'is_pay' => '1',
            'is_out_date' => '0',
            'is_split' => ['IN', [0,1]],
        ];
        $list = M('order')->where($where)->field('order_sn,agent_id,total,add_time')->select();
        foreach ($list as $key => $value) {
            $date = date('Ym', $value['add_time']);
            $saleData = M('agent_sales_statistics')->where(array('date'=>$date))->find();
            $goods_number = M('order_detail')->where(['order_sn'=>$value['order_sn']])->field('SUM(`goods_number`) AS `total`')->find();
            $goods_number = $goods_number['total'];
            if( empty($saleData)){
                //表中无记录
                $saleData = array(
                    'agent_id' => $value['agent_id'],
                    'month_sales' => $goods_number,
                    'month_income' => $value['total'],
                    'date' => $date,
                    'add_time' => time(),
                    );
                M('agent_sales_statistics')->add($saleData);
            }else{
                //表中有记录
                $saleSaveData = $saleData;
                $saleSaveData['month_sales'] = $saleSaveData['month_sales'] + $goods_number;
                $saleSaveData['month_income'] = $saleSaveData['month_income'] + $value['total'];
                M('agent_sales_statistics')->where(array('id'=>$saleData['id']))->save($saleSaveData);
            }
        }
    }

    public function lockGoods()
    {
        $list = M('agent')->where(['is_on_sale'=>'1'])->select();
        foreach ($list as $key => $value) {
            M('goods')->where(['agent_id'=>$value['id'],'is_delete'=>'0'])->save(['is_lock'=>'1']);
        }
    }
}