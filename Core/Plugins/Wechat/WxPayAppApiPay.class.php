<?php
namespace Plugins\Wechat;
use Plugins\Wechat\WxPayDataBase;

/**
 * 
 * 提交JSAPI输入对象
 * @author widyhu
 *
 */
class WxPayAppApiPay extends WxPayDataBase
{
	/**
	* 设置微信开放平台审核通过的应用APPID
	* @param string $value 
	**/
	public function SetAppid($value)
	{
		$this->values['appid'] = $value;
	}

	/**
	* 设置微信支付分配的商户号
	* @param string $value 
	**/
	public function SetPartnerid($value)
	{
		$this->values['partnerid'] = $value;
	}

	/**
	* 设置支付时间戳
	* @param string $value 
	**/
	public function SetTimeStamp($value)
	{
		$this->values['timestamp'] = $value;
	}

	/**
	* 随机字符串
	* @param string $value 
	**/
	public function SetNonceStr($value)
	{
		$this->values['noncestr'] = $value;
	}

	/**
	* 预支付交易会话ID
	* @param string $value 
	**/
	public function SetPrepayid($value)
	{
		$this->values['prepayid'] = $value;
	}

	/**
	* 设置订单详情扩展字符串
	* @param string $value 
	**/
	public function SetPackage($value)
	{
		$this->values['package'] = $value;
	}

	/**
	* 设置签名
	* @param string $value 
	**/
	// public function SetSign($value)
	// {
	// 	$this->values['sign'] = $value;
	// }

	/**
	 * [createParamter 创建app配置]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2015 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
	public static function createParamter($prepay_id) 
	{
		$object = new self();
		$timeStamp = time();
		$object->SetAppid(WX_APPID);
		$object->SetPartnerid(WX_MCHID);
		$object->SetPrepayid("{$prepay_id}");
        $object->SetTimeStamp("{$timeStamp}");
        $object->SetNonceStr($object->getNonceStr());
        $object->SetPackage("Sign=WXPay");
        $object->SetSign();
		return $object->values;
	}

	/**
	 * 
	 * 产生随机字符串，不长于32位
	 * @param int $length
	 * @return 产生的随机字符串
	 */
	public function getNonceStr($length = 32) 
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {  
			$str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
		} 
		return $str;
	}
}