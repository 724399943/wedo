<?php
namespace Plugins\Wechat;
use Plugins\Wechat\WxPayApi AS WxPayApi;
use Plugins\Wechat\WxPayResults AS WxPayResults;
use Plugins\Wechat\WxPayNotifyReply;
/**
 * 
 * 回调基础类
 * @author kofu 418382595@qq.com
 *
 */
class WxJsApiPayNotify extends WxPayNotifyReply
{
	final public function notifyCallback() {
		//获取通知的数据
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		// file_put_contents('pay', $xml . "\r\n", FILE_APPEND);
		//当返回false的时候，表示回调失败获取签名校验失败，此时直接回复失败
		$result = WxPayResults::Init($xml);
		if($result == false){
			$this->SetReturn_code("FAIL");
			$this->SetReturn_msg('签名失败');
			$this->ReplyNotify(false);
			return false;
		} else {
			//该分支在成功回调到NotifyCallBack方法，处理完成之后流程
			$this->SetReturn_code("SUCCESS");
			$this->SetReturn_msg("OK");
			$this->ReplyNotify(false);
			return $result;
		}
	}

	/**
	 * 
	 * 回复通知
	 * @param bool $needSign 是否需要签名输出
	 */
	final private function ReplyNotify($needSign = true)
	{
		//如果需要签名
		if($needSign == true && 
			$this->GetReturn_code($return_code) == "SUCCESS")
		{
			$this->SetSign();
		}
		WxPayApi::replyNotify($this->ToXml());
	}
}