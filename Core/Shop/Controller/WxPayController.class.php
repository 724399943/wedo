<?php
namespace Shop\Controller;
use Think\Controller;
use Plugins\Wechat\WxPayApi;
use Plugins\Wechat\WxPayUnifiedOrder;
use Plugins\Wechat\WxPayAppApiPay;
// use Shop\Controller\PublicController;

class WxPayController extends Controller {
    public function _initialize() {
        $c = M('config')->getField('config_sign,config_value');
        C($c);
        define(WX_APPID, C('appid'));
        define(WX_MCHID, C('mchid'));
        define(WX_KEY, C('wxpaykey'));
        define(WX_APPSECRET, C('appsecret'));
        define(WX_NOTIFY_URL,trim(C('webSite'), '/') . U('WxPay/notify_url'));
        // define(WX_SSLCERT_PATH, C('CACERT_PATH') . "/cacert/apiclient_cert.pem");
        // define(WX_SSLKEY_PATH, C('CACERT_PATH') . "/cacert/apiclient_key.pem");
    }

    /**
     * [createParameters 创建APP支付参数]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)       2015          Xcrozz (http://www.xcrozz.com)
     * @param     [type]        $order_sn [description]
     * @param     [type]        $total    [description]
     * @param     [type]        $open_id  [description]
     * @return    [type]                  [description]
     */
    public function createParameters($order_sn, $total) {
        $systemName = C('systemName');
        // 统一下单类
        $WxPayUnifiedOrder = new WxPayUnifiedOrder();

        //  ---------设置下单相关配置start---------
        // 设置商品或支付单简要描述
        $body = $systemName . '-充值订单编号：' . $order_sn;
        $WxPayUnifiedOrder->SetBody($body);

        // 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
        $WxPayUnifiedOrder->SetOut_trade_no($order_sn);

        // 设置符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型
        $WxPayUnifiedOrder->SetFee_type('CNY');

        // 设置订单总金额，只能为整数，详见支付金额
        $total = $total * 100;
        $WxPayUnifiedOrder->SetTotal_fee($total);

        // $time = empty($time) ? time() : $time;
        // 设置订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。
        // $WxPayUnifiedOrder->SetTime_start(date('YmdHis', $time));

        // 设置订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。
        // $WxPayUnifiedOrder->SetTime_expire(date('YmdHis', $time + (C('ORDER_EFFECTIVE_TIME') * 3600)));

        // 设置接收微信支付异步通知回调地址
        $WxPayUnifiedOrder->SetNotify_url(WX_NOTIFY_URL);

        // 设置取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
        $WxPayUnifiedOrder->SetTrade_type('APP');

        //  ---------设置下单相关配置end---------
        
        // 获取prepay_id
        $result = WxPayApi::unifiedOrder($WxPayUnifiedOrder);
        $prepay_id = $result['prepay_id'];

        // 获取jsapi配置
        $appApiParamter = WxPayAppApiPay::createParamter($prepay_id);
        return $appApiParamter;
    }

    /**
     * [notify_url 支付回调]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2015 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function notify_url() {
        // 回调入口
        $WxJsApiPayNotify = new WxJsApiPayNotify();
        // 返回不为空值则表示验证签名成功 可执行相关逻辑操作
        $result = $WxJsApiPayNotify->notifyCallback();
        if (!empty($result)) {
            $order_sn       = $result['out_trade_no'];
            $total_fee      = (float) $result['total_fee'] / 100;
            $this->executeCallback($order_sn, $total_fee);
            
            $wxpaynotify = M('wxpaynotify');
            $wxpaynotify->add($result);
        } else {
            file_put_contents('payfalse', $result . time());
        }
    }

    /**
     * [executeCallback 回调更新订单]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)        2016          Xcrozz (http://www.xcrozz.com)
     * @param     [type]        $order_sn  [订单号]
     * @param     [type]        $total_fee [实际支付金额]
     * @return    [type]                   [description]
     */
    public function executeCallback($order_sn, $total_fee) {
        $dbPrefix       = C('DB_PREFIX');
        $rechargeOrder  = M('recharge_order');
        $order = $rechargeOrder->where(array('order_sn'=> $order_sn))->find();
        if (empty($order)) {
            return;
        }
        if ( (int) $order['is_pay'] === 1 ) {
            return;
        }
        if ($order['total'] != $total) {
            return;
        }
        $total          = (float) $order['total'];
        $total_fee      = (float) $total_fee;
        if ($total !== $total_fee) {
            return;
        }
        $result         = true;
        $mysql          = M();
        $mysql->startTrans();
        $data['id']         = $order['id'];
        $data['is_pay']     = '1';
        $data['pay_type']   = '1';
        $data['pay_time']   = time();
        if( $rechargeOrder->save($data) === false ){
            $result = false;
        }

        // 增加余额并记录log
        $Money = new Money;
        $return = ($order['is_shop'] != '0') ? $Money->money($order['user_id'], $total, 0, 0, $order['shop_id'], 1) : $Money->money($order['user_id'], $total, 0, 0);
        if ( $return === false ) {
            $result = false;
        }

        if ($result !== false) {
            $mysql->commit();
        } else {
            $mysql->rollback();
        }
    }
}