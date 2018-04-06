<?php
namespace Shop\Controller;
use Think\Controller;
use Shop\Controller\PublicController;
class PaypalController extends Controller {
    private $clientId = '';
    private $clientSecret = '';
    // private $paymentUrl = 'https://www.paypal.com/row/cgi-bin/webscr';
    private $paymentUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
    private $validateUrl = 'https://www.paypal.com/cgi-bin/webscr';
    private $accessToken = 'access_token$sandbox$kpjgdb89nvycdnw5$4165b26928504decf8d5f158ce485097';
    // private $accessToken = 'access_token$production$jrd7jbtjjzcb4dsf$c42aca55366c76abae0a247a81994157';
    private $business;
    private $notifyUrl;
    private $webSite;
    private $gateway;
    public function __construct() {
        parent::__construct();
        $sessionId = I('request.session_id');
        if ( !empty($sessionId) ) {
            session_id($sessionId);
        }
        session_start();
        require_once('./Core/Plugins/Paypal/lib/Braintree.php');
        $this->gateway = new \Braintree_Gateway(array(
            'accessToken' => $this->accessToken,
        ));
		$c = M('config')->getField('config_sign,config_value');
        C($c);
        $this->webSite = trim(C('webSite'), '/');
        $this->notifyUrl = $this->webSite . '/Paypal/notify_url';
        $this->business = C('business');
    }

    /**
     * [h5pay h5页面支付]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function h5pay() {
        $returnUrl = $_SERVER['HTTP_REFERER'];
        $cancelUrl = $_SERVER['HTTP_REFERER'];
        $requestData = array();
        // 立即购买
        $requestData['cmd'] = '_xclick';
        // PayPal账户上的电子邮件地址
        $requestData['business'] = $this->business;
        // 物品名称（或购物车名称）
        $requestData['item_name'] = 'test';
        //定义币种以标示货币变量 值可以为 "USD"、"EUR"、"GBP"、"CAD"、"JPY"、"HKD"
        $requestData['currency_code'] = 'USD';
        //物品的价格（购物车中所有物品的总价格,因为是_Xclick模式）
        $requestData['amount'] = '0.01';
        // 送货地址。如果设为 "1"，则不会要求您的客户提供送货地址。该变量为可选项；如果省略或设为 "0"，将提示您的客户输入送货地址
        $requestData['no_shipping'] = '1';
        // 为付款加入提示。如果设为 "1"，则不会提示您的客户输入提示。该变量为可选项；如果省略或设为 "0"，将提示您的客户输入提示。
        $requestData['no_note'] = '1';
        // 仅与 IPN 一起使用。发送 IPN Form Post 的互联网 URL（即回调地址）
        $requestData['notify_url'] = $this->notifyUrl;
        // 您的客户完成付款后将返回的互联网 URL
        $requestData['return'] = $returnUrl;
        // 您的客户取消付款后将返回的互联网 URL
        $requestData['cancel_return'] = $cancelUrl;
        // 设置您的付款页面的背景色。如果设为 "1"，背景色将为黑色。该变量为可选项；如果省略或设为 "0"，背景色将为白色
        $requestData['cs'] = '1';
        // 账单号
        $requestData['invoice'] = I('get.sign', 'bidding', 'string').'_'.I('get.order_sn', '', 'string');
        $html = $this->buildRequestForm($requestData, 'POST');
        echo $html;
    }

    /**
     * [notify_url 支付回调]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function notify_url() {
        $postData = $_POST;
        $postData['cmd'] = '_notify-validate';
        $result = curl($this->paymentUrl, $postData, 'post');
        file_put_contents('post.txt', json_encode($postData) . "\r\n", FILE_APPEND);
        file_put_contents('result.txt', json_encode($result) . "\r\n", FILE_APPEND);
        if ( $result == 'VERIFIED' ) {
            // 确认“payment_status”为“Completed”，因为系统也会 为其他结果（如“Pending”或“Failed”）发送 IPN
            // if ( $postData['payment_status'] != 'Completed' ) {
            //     return;
            // }
            if ( $postData['receiver_email'] != $this->business ) {
                return;
            }
            $invoice = explode('_', $postData['invoice']);
            // 检查“txn_id”是否未重复，以防止欺诈者重复使用旧的已 完成的交易。
            $where['txn_id'] = $postData['txn_id'];
            $saveData = array(
                'txn_id' => $postData['txn_id'],
                'is_pay' => '1'
            );
            switch ( $invoice[0] ) {
                case 'bidding':
                    $biddingModel = M('bidding');
                    if ( $biddingModel->where($where)->count() > 0 ) {
                        return;
                    } else {
                        $biddingModel->where(array('order_sn'=> $invoice[1]))->save($saveData);
                    }
                    break;
                case 'goodscheck':
                    $goodsCheckModel = M('goods_check');
                    if ( $goodsCheckModel->where($where)->count() > 0 ) {
                        return;
                    } else {
                        $goodsCheckModel->where(array('order_sn'=> $invoice[1]))->save($saveData);
                    }
                    break;
                case 'issuing':
                    $messageCheckModel = M('message_check');
                    if ( $messageCheckModel->where($where)->count() > 0 ) {
                        return;
                    } else {
                        $messageCheckModel->where(array('order_sn'=> $invoice[1]))->save($saveData);
                    }
                    break;
                case 'edit':
                    $goodsForEditModel = D('GoodsForEdit');
                    if ( $goodsForEditModel->where($where)->count() > 0 ) {
                        return;
                    } else {
                        $goodsForEditData = $goodsForEditModel->where(array('order_sn'=> $invoice[1]))->find();
                        $goodsForEditData = array_merge($goodsForEditData, $saveData);
                        $goodsForEditModel->save($goodsForEditData);
                        $goodsForEditModel->saveGoodsData(json_decode($goodsForEditData['goods_data'], true), $goodsForEditData['agent_id']);
                    }
                    break;
            }
        } else {
            echo 'error';
        }
    }

    public function buildRequestForm($requestData, $method) {        
        $sHtml = "<form id='requestForm' name='requestForm' action='".$this->paymentUrl."' method='".$method."'>";
        while (list ($key, $val) = each ($requestData)) {
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }
        $sHtml = $sHtml."<script>document.forms['requestForm'].submit();</script>";
        return $sHtml;
    }

    /**
     * [clientToken description]
     * @Author kofu<[418382595@qq.com]>
     * @return [type]                   [description]
     */
    public function clientToken() {
        $clientToken = $this->gateway->clientToken()->generate();
        exit(statusCode(array('clientToken'=> $clientToken)));
    }

    /**
     * [createTransaction description]
     * @Author kofu<[418382595@qq.com]>
     * @return [type]                   [description]
     */
    public function createTransaction() {
        $payment_method_nonce = I('payment_method_nonce');
        $order_sn = I('order_sn', '', 'string');
        $result = $this->gateway->transaction()->sale([
            "amount" => '0.01',
            'merchantAccountId' => 'USD',
            "paymentMethodNonce" => $payment_method_nonce,
            "orderId" => $order_sn,
            "descriptor" => [
                "name" => "Test"
            ],
            // "shipping" => [
            //     "firstName" => "Jen",
            //     "lastName" => "Smith",
            //     "company" => "Braintree",
            //     "streetAddress" => "1 E 1st St",
            //     "extendedAddress" => "Suite 403",
            //     "locality" => "Bartlett",
            //     "region" => "IL",
            //     "postalCode" => "60103",
            //     "countryCodeAlpha2" => "US"
            // ],
            "options" => [
                'submitForSettlement' => true,
                // "paypal" => [
                //     "customField" => $_POST["PayPal custom field"],
                //     "description" => $_POST["Description for PayPal email receipt"]
                // ],
            ]
        ]);

        if ($result->success || !is_null($result->transaction)) {
            $orderModel = D('Order');
            $where = array(
                'user_id' => session('userId'),
                'order_sn' => $order_sn
            );
            $orderModel->where($where)->save(array('transaction_id'=> $result->transaction->id));
            exit(statusCode(array('transaction_id'=> $result->transaction->id, 'amount'=> '0.01')));
        } else {
            $errorString = "";

            foreach($result->errors->deepAll() as $error) {
                $errorString .= 'Error: ' . $error->code . ": " . $error->message . "\n";
            }

            $_SESSION["errors"] = $errorString;
            exit(statusCode(array(), 400000, $result->message));
        }
    }

    /**
     * [processorSettlementResponse description]
     * @Author kofu<[418382595@qq.com]>
     * @return [type]                   [description]
     */
    public function processorSettlementResponse() {
        $order_sn = I('order_sn', '', 'string');
        $orderModel = D('Order');
        $orderInfo = $orderModel->where(array('order_sn'=> $order_sn))->field('`order_sn`, `giving_point`, `total`')->find();
        if ( !empty($orderInfo) ) {
            $processData = array(
                'order_sn' => $orderInfo['order_sn'],
                'total_fee' => $orderInfo['total'],
                'pay_type' => '4'
            );
            $publicController = new PublicController;
            $publicController->processCallback($processData);
            exit(statusCode(array('orderInfo'=> $orderInfo)));
        } else {
            exit(statusCode(array(), 400000, '没有该订单号'));
        }
    }

    public function processorSettlementResponse1() {
        $transaction_id = I('transaction_id', '', 'string');
        $amount = I('amount', '', 'string');
        $result = $this->gateway->transaction()->submitForSettlement($transaction_id, $amount);
        file_put_contents('processorSettlementResponse.txt', json_encode($result));
        if ($result->success) {
            $orderModel = D('Order');
            $orderInfo = $orderModel->where(array('transaction_id'=> $transaction_id))->field('`order_sn`, `giving_point`')->find();
            $processData = array(
                'order_sn' => $orderInfo['order_sn'],
                'total_fee' => $amount,
                'pay_type' => '4'
            );
            $publicController = new PublicController;
            $publicController->processCallback($processData);
            exit(statusCode(array('orderInfo'=> $orderInfo)));
        } else if ($result->errors->deepSize() > 0) {
            print_r($result->errors);
        } else {
            echo $result->transaction->processorSettlementResponseCode;
            echo $result->transaction->processorSettlementResponseText;
        }
    }

    /**
     * [paypal_refund PayPal退款]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function paypal_refund($txn_id)
    {
        $url = "https://api.sandbox.paypal.com/v1/payments/sale/$txn_id/refund";
        $post_data = "{}";
        $header = array(
            "Content-Type: application/json",
            "Authorization: Bearer {$this->accessToken}"
        );
        $output = $this->http_post_paypal($url, $post_data, $header);

        if (false === $output || false === $this->accessToken) {
            $this->error('网络异常，请您稍后重试');
        }
        file_put_contents('refund.txt', $output."\r\n", FILE_APPEND);

        $output = json_decode($output, TRUE);
        if ($txn_id == $output['sale_id'] && "completed" == $output['completed']) {
            // success
        } elseif ("TRANSACTION_REFUSED" == $output['name']) {
            $this->error('退款已提交，请勿重复');
        }
    }

    public function http_post_paypal($url, $post_data, $header=array()) {
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回array(‘Expect:‘)
        $output = curl_exec($ch);
        
        if ( curl_errno($ch) ){
            return false;
        }
    
        return $output;
    } 
}