<?php

/**
 * 助通SMS类, 验证码发送接口
 */
class Ztsms {

    //账号baiyang666  密码 9DMsrSpq 产品id 验证码 676767 通知 887362 营销 435227
    private $gwUrl = '';  //网关地址
    private $username = ''; //用户账号
    private $password = '';  //用户密码
    private $productid = ''; //内容
    private $xh = '';        //留空
    private $message = '';        //留空

    function __construct($url, $username, $password, $productid) {
        $this->gwUrl = $url;
        $this->username = $username;
        $this->password = $password;
        $this->productid = $productid;
    }

    function sendSMS($mobiles = array(), $text = '')
    {
		//$result['success'] = 0;
		//$result['msg'] = "请联系客服";
		//return $result;
		
        $mobile = $mobiles[0];
        $text = iconv("UTF-8", "UTF-8", $text);

        $contents = array(
            'username' => $this->username,
            'password' => $this->password,
            'mobile' => $mobile,
            'content' => $text,
            'dstime' => '',
            'productid' => $this->productid,
            'xh' => $this->xh,
        );

        $url = $this->gwUrl . http_build_query($contents);
        $data = $this->api_curl($url);
        $result = $this->parseMessage($data);
        return $result;
    }

    function parseMessage($data = "")
    {

        $result = array('success' => 0, 'msg' => '');
        $errors = array(
            '-1' => "用户名或者密码不正确或用户禁用或者是管理账户",
            '0' => "发送短信失败",
            '2' => "余额不够或扣费错误",
            '3' => "扣费失败异常",
            '5' => "短信定时成功",
            '6' => "有效号码为空",
            '7' => "短信内容为空",
            '8' => "无签名",
            '9' => "没有Url提交权限",
            '10' => "发送号码过多,最多支持2000个号码",
            '11' => "产品ID异常或产品禁用",
            '12' => "参数异常",
            '13' => "30分种重复提交",
            '14' => "用户名或密码不正确，产品余额为0，禁止提交，联系客服",
            '15' => "Ip验证失败",
            '19' => "短信内容过长，最多支持500个",
            '20' => "定时时间不正确"
        );

        if ($data) $data_arr = explode(",", $data); //返回格式以,隔开的信息

        if (is_array($data_arr)) {
            if ($data_arr[0] == 1) {
                $this->message = "发送短信成功";
                $result['success'] = 1;
                $result['msg'] = $this->message;
            } else {
                $this->message = $errors[$data_arr[0]] . ", 错误代码为" . $data_arr[0];
            }
        } else {
            $this->message = "短信网关请求失败";
        }

        $result['msg'] = $this->message;
        return $result;

    }

    function chkError()
    {
        return $this->message;
    }

    /**
     * curl 请求方法
     *
     * @param string $url 请求地址
     * @param array $data post方式数据
     * @param string $method 请求方式
     * @return bool 结果
     */
    function api_curl($url, $data=array(), $method='GET')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        if('POST' == $method)
        {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            //设置HTTP头信息
            curl_setopt($ch,CURLOPT_HTTPHEADER,array("X-HTTP-Method-Override: $method"));
        }
        $result = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        return $result;

    }
}