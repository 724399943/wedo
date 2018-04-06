<?php
namespace Plugins\Wechat;

class WxLogin {
	/**
	 * [getAccessToken 获取access_token]
	 * @author Fu <418382595@qq.com>
	 * @copyright Copyright (c)     2015          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $appid  [应用唯一标识]
	 * @param     [type]        $secret [应用密钥AppSecret]
	 * @param     [type]        $code   [填写第一步获取的code参数]
	 * @return    [type]                [description]
	 */
	public function getAccessToken($appid, $secret, $code) {
		$url 	= "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
		// $data 	= array(
		// 	'appid' => $appid, 
		// 	'secret' => $secret, 
		// 	'code' => $code, 
		// 	'grant_type' => 'authorization_code',
		// ); 

		//  ---------- success return -----------
		/*
		{ 
			// 接口调用凭证
			"access_token":"ACCESS_TOKEN",      		
			// access_token接口调用凭证超时时间，单位（秒）
			"expires_in":7200, 							
			// 用户刷新access_token
			"refresh_token":"REFRESH_TOKEN",			
			// 授权用户唯一标识
			"openid":"OPENID", 							
			// 用户授权的作用域，使用逗号（,）分隔
			"scope":"SCOPE" 							
			// 当且仅当该网站应用已获得该用户的userinfo授权时，才会出现该字段。
			"unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"  
		}
		*/
		//  ---------- success return -----------
		$result = curl($url, array(), 'get');
		return $result;
	}

	/**
	 * [checkAccessToken 检验授权凭证（access_token）是否有效]
	 * @author Fu <418382595@qq.com>
	 * @copyright Copyright (c)           2015          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $access_token [调用接口凭证]
	 * @param     [type]        $openid       [普通用户标识]
	 * @return    [type]                      [description]
	 */
	public function checkAccessToken($access_token, $openid) {
		$url = "https://api.weixin.qq.com/sns/auth?access_token={$access_token}&openid={$openid}";
		$result = curl($url, array(), 'post');
		return $result;
		// 成功返回{"errcode":0} 否则{"errcode":40003}
	}

	/**
	 * [getUserInfo 获取用户个人信息]
	 * @author Fu <418382595@qq.com>
	 * @copyright Copyright (c)           2015          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $access_token [description]
	 * @param     [type]        $openid       [description]
	 * @return    [type]                      [description]
	 */
	public function getUserInfo($access_token, $openid) {
		$url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}";
		$result = curl($url, array(), 'post');

		/* ---------- success return -----------
		{ 
			//普通用户的标识，对当前开发者帐号唯一
			"openid":"OPENID",
			//普通用户昵称
			"nickname":"NICKNAME",
			//普通用户性别，1为男性，2为女性
			"sex":1,
			//普通用户个人资料填写的省份
			"province":"PROVINCE",
			//普通用户个人资料填写的城市
			"city":"CITY",
			//国家，如中国为CN
			"country":"COUNTRY",
			//用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空
			"headimgurl": "http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",   
			//用户特权信息，json数组，如微信沃卡用户为（chinaunicom）
			"privilege":[
			"PRIVILEGE1", 
			"PRIVILEGE2"
			],
			//用户统一标识。针对一个微信开放平台帐号下的应用，同一用户的unionid是唯一的
			"unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"
		}
		---------- success return ----------- */
		return $result;
	}

	/**
	 * [refreshAccessToken 刷新或续期access_token使用]
	 * @author Fu <418382595@qq.com>
	 * @copyright Copyright (c)            2015          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $appid         [应用唯一标识]
	 * @param     [type]        $refresh_token [description]
	 * @return    [type]                       [填写通过access_token获取到的refresh_token参数]
	 */
	public function refreshAccessToken($appid, $refresh_token) {
		$url = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid={$appid}&grant_type=refresh_token&refresh_token={$refresh_token}";
		//  ---------- success return -----------
		// { 
		// 	"access_token":"ACCESS_TOKEN",      接口调用凭证
		// 	"expires_in":7200, 					access_token接口调用凭证超时时间，单位（秒）
		// 	"refresh_token":"REFRESH_TOKEN",	用户刷新access_token
		// 	"openid":"OPENID", 					授权用户唯一标识
		// 	"scope":"SCOPE" 					用户授权的作用域，使用逗号（,）分隔
		// }
		//  ---------- success return -----------
		$result = curl($url, array(), 'post');
		return $result;
	}
}	