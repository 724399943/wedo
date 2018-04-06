<?php
namespace Shop\Controller;
use Think\Controller;
use Plugins\Money\Money;
use Plugins\Alipay\AlipayTradeAppPayClient;
use Plugins\Alipay\AlipayTradeAppPayRequest;
class AlipayController extends Controller {
    public function _initialize() {
        $c = M('config')->getField('config_sign,config_value');
        C($c);
    }

    /**
     * [createAppParameters 构造app支付参数]
     * @author kofu <[418382595@qq.com]>
     * @copyright Copyright (c)       2015          Xcrozz (http://www.xcrozz.com)
     * @param     [type]        $order_sn [description]
     * @param     [type]        $total    [description]
     * @return    [type]                  [description]
     */
    public function createAppParameters($order_sn, $total) {
        $systemName = C('systemName');
        $client = new AlipayTradeAppPayClient;
        // 支付宝分配给开发者的应用ID
        $client->appId      = C('ALIPAY_APPID');
        // 商户生成签名字符串所使用的签名算法类型，目前支持RSA2和RSA，推荐使用RSA2
        $client->signType   = "RSA2";
        // 私钥
        $client->rsaPrivateKey  = C('ALIPAY_PRIVATE_KEY');
        // 公钥
        $client->alipayrsaPublicKey = C('ALIPAY_PUBLIC_KEY');
        // 业务请求参数的集合，最大长度不限，除公共参数外所有请求参数都必须放在这个参数中传递，具体参照各产品快速接入文档
        $bizcontent = "{\"body\":\"充值金额为：$total\","
                . "\"subject\": \"$systemName-充值订单编号：$order_sn\","
                . "\"out_trade_no\": \"$order_sn\","
                . "\"timeout_express\": \"30m\","
                . "\"total_amount\": \"$total\","
                . "\"product_code\":\"QUICK_MSECURITY_PAY\""
                . "}";
        
        $request = new AlipayTradeAppPayRequest();
        $request->setNotifyUrl(C('ALIPAY_NOTIFY_URL'));
        $request->setBizContent($bizcontent);
        //这里和普通的接口调用不同，使用的是sdkExecute
        $response = $client->sdkExecute($request);
        $response['body'] = "充值金额为：".$total;
        $response['subject'] = $systemName."-订单编号：".$order_sn;
        $response['out_trade_no'] = $order_sn;
        $response['timeout_express'] = "30m";
        $response['total_amount'] = $total;
        $response['product_code'] = "QUICK_MSECURITY_PAY";
        $response['rsaPrivateKey'] = C('ALIPAY_PRIVATE_KEY');
        $response['alipayrsaPublicKey'] = C('ALIPAY_PUBLIC_KEY');
        //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
        return $response;//就是orderString 可以直接给客户端请求，无需再做处理。
    }

    /**
     * [notify_url 支付宝回调]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function notify_url() {
        $client = new AlipayTradeAppPayClient;
        $client->alipayrsaPublicKey = C('ALIPAY_PUBLIC_KEY');
        file_put_contents('post.txt', $_POST . "\r\n", FILE_APPEND);
        $flag = $client->rsaCheckV1($_POST, NULL, "RSA2");
        if($flag) {//验证成功
            //商户订单号
            $out_trade_no = $_POST['out_trade_no'];
            //支付宝交易号
            $trade_no = $_POST['trade_no'];
            //交易状态
            $trade_status = $_POST['trade_status'];
            //交易金额
            $total_amount = $_POST['total_amount'];
            if($_POST['trade_status'] == 'TRADE_FINISHED') {
                // 更新订单信息并且记录异步回调信息
                $this->executeCallback($out_trade_no, $total_amount);
                //注意：
                //该种交易状态只在两种情况下出现
                //1、开通了普通即时到账，买家付款成功后。
                //2、开通了高级即时到账，从该笔交易成功时间算起，过了签约时的可退款时限（如：三个月以内可退款、一年以内可退款等）后。
            }
            else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                // 更新订单信息并且记录异步回调信息
                $this->executeCallback($out_trade_nom, $total_amount);
                //注意：
                //该种交易状态只在一种情况下出现——开通了高级即时到账，买家付款成功后。
            }
            echo "success";     //请不要修改或删除
        }
        else {
            //验证失败
            echo "fail";
            //调试用，写文本函数记录程序运行情况是否正常
            logResult("失败");
        }
    }

    /**
     * [executeCallback 回调更新订单]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016          Xcrozz (http://www.xcrozz.com)
     * @param     [type]        $out_trade_no [description]
     * @param     [type]        $total_amount [description]
     * @return    [type]                      [description]
     */
    private function executeCallback($out_trade_no, $total_amount) {
        $order_sn       = $out_trade_no;
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
        $total_amount   = (float) $total_amount;
        if ($total !== $total_amount) {
            return;
        }
        $result         = true;
        $mysql          = M();
        $mysql->startTrans();
        $data['id']         = $order['id'];
        $data['is_pay']     = '1';
        $data['pay_type']   = '2';
        $data['pay_time']   = time();
        if( $rechargeOrder->save($data) !== false ){
            // 增加余额并记录log
            $Money = new Money;
            $return = ($order['is_shop'] != '0') ? $Money->money($order['user_id'], $total, 0, 0, $order['shop_id'], 1) : $Money->money($order['user_id'], $total, 0, 0);
            if ( $return === false ) {
                $result = false;
            }
        } else {
            $result = false;
        }
        if ($result !== false) {
            $mysql->commit();
        } else {
            $mysql->rollback();
        }
    }
}