<?php
return array(
	'VERIFY_CODE_INTERVAL_TIME'		=> 60,			//发送手机短信的间隔时间 /秒
	'VERIFY_CODE_EFFECTIVE_TIME'	=> 60 * 3,		//手机接收的短信的有效时间 /秒
	'VERIFY_DATE_COUNT'				=> 50,			//每天发送手机验证码次数上限

	'SESSION_AUTO_START'			=> false,
	'ONLINE'						=> false,
	'DEFAULT_THEME'					=> 'Shop',
	'DISTANCE_METER'				=> '2147483647', 
	'LANG_SWITCH_ON' 				=> true,   // 开启语言包功能
	'LANG_AUTO_DETECT' 				=> true, // 自动侦测语言 开启多语言功能后有效
	'LANG_LIST'        				=> 'zh-cn,en-us', // 允许切换的语言列表 用逗号分隔
	'DEFAULT_LANG'     				=> 'en-us', // 默认语言
	'VAR_LANGUAGE'     				=> 'l', // 默认语言切换变量
);