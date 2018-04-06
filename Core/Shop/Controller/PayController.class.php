<?php
namespace Shop\Controller;
use Think\Controller;
use Shop\Controller\WxPayController;
use Shop\Controller\AlipayController;
//支付控制器
Class PayController extends BaseController{
    /**
     * [wechatPay 微信支付]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function wechatPay() {
        if (IS_POST) {
            $order_sn = I('post.order_sn');
            $rechargeOrder = M('recharge_order');
            $total = $rechargeOrder->where(array('order_sn'=>$order_sn))->getField('money');
            $wxpay = new WxPayController();
            $appApiParameters = $wxpay->createParameters($order_sn, $total);
            die(statusCode(array('appApiParameters'=> $appApiParameters)));
        } else {
            exit(statusCode(array(), 100001));            
        }
    }

    /**
     * [alipay 支付宝支付]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function alipay() {
        if (IS_POST) {
            $order_sn = I('post.order_sn');
            $rechargeOrder = M('recharge_order');
            $total = $rechargeOrder->where(array('order_sn'=>$order_sn))->getField('money');
            $alipay = new AlipayController();
            $appApiParameters = $alipay->createAppParameters($order_sn, $total);
            die(statusCode(array('appApiParameters'=> $appApiParameters)));
        } else {
            exit(statusCode(array(), 100001));            
        }
    }

}