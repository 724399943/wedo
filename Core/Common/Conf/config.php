<?php
return array(
	'DEFAULT_MODULE'     => 'Shop',  // 默认模块
	'DB_TYPE'  			 => 'mysql',
	'DB_HOST' 		     => '192.168.1.161', 
	'DB_NAME'  			 => 'wedo',
	'DB_USER'  			 => 'wedo',
	'DB_PWD'   			 => '123456',
	'DB_PORT'  			 => 3306,
	'DB_PREFIX' 		 => 'wd_',
	'DB_CHARSET'		 => 'utf8',

    'MODULE_ALLOW_LIST'  => array('Admin'),
    'DEFAULT_MODULE'     => 'Shop',

	'BASE_COOKIE_HOST'   => '.wedo.com',

	'URL_MODEL'			 => 2,
    'TMPL_PARSE_STRING'  => array(
         '__PUBLIC__' 	 => '/Static/Public'
    ),

    'TPH_FILE_PATH' 	 => '/Core/TPH',

    'TAGLIB_BUILD_IN'    => 'cx,diy',
    
    'URL_404_REDIRECT'   =>  '/Static/Public/Shop/404.html',
    'UPLOAD_PATH'		 => './Static/Uploads/',

    // 环信配置
	'Client_id'          =>'YXA6RyTFAP--EeaiXRs_8dOKDw',
    'Client_secret'      =>'YXA6B5zIyiLbJq4py8o6zM-BkvmgDEA',
    'Org_name'           =>'1171161130115692',
    'App_name'           =>'zsjj',

	'SESSION_AUTO_START' => TRUE,

    // 支付宝配置
    'ALIPAY_APPID'       => '2016111202742377',
    'ALIPAY_NOTIFY_URL'  => 'http://zs.hpingtai.com/Shop/AliPay/notify_url',
    'ALIPAY_PRIVATE_KEY' => 'MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCPkjEHmW5VbJZUKAGxA/rgbiSSzQ19962/8l4o7jrz6Dw28IovRt/as0Foa8RAhtU+hLeDhBN/YGdYrrCUELFkrvlt5kbd1u/6PTuJWvpMvv0TLDovM1EYyLCDQ/PoGIcqEhW2CELGHEmkzUqdTqXk168sMV5Mmyzx3jVYJGjEy4iBIB5MgsiYlTNV+7jd0jUqgzpwAEAkYziIshJ0a9qv04ZhgDUww0xTJy5huxcDG2+VbY7GtPZQnMRJxvtdjoWyJaF2Te2cQd+rSjNiOtBMKXqhAa4XG+A6tyiPFKrRyvlDfBHOqKhqjMkktR1Z5KotGUDYDLlIRlmGdzx62Z7ZAgMBAAECggEAHVDWnhygQOUpVQnOPpz3bzhOU4N3S891NQqCW405kHBNS1KWhMzODNQpeO74ZcyiYl+rMTmY2ZuPvrX87F70OgDYga5Rrh6jZc93BrcDAQTGnFGdl+G8n3jrQgJwHWZUwyxqLn9FUzqXdwVMRQ64JSA9bqKuBKpy5PhgoBzfH7+3GZOIhiA4+lNhHxhODO0yJeQnxjs/zaUDYWo7R4Df1alGsXYyuP5o6MhlwO/nklqyPE1bQ+CmlaEdjwJsvHwNQou2S7lNfb4TtkXpZRqZa70jFhLlcwurfx6ZrEUmy5sGI8509StbHS5CYjZXRLDPk40AXENCSpwMrhoTYoVUWQKBgQDzX0l87hAKjilNeI7vae80RjfOgZsBTymxNJbT/4HsMGtqTqocm078rYOYQSoXssfAKmADnzfxVKY6ikdcVhzRoCIRG6U7KZBPq7rgRSkH078WtG/Iuidltg9gT5dlc9q+54/gcPWsKKSVq4lm5PEf0HKVyFLf29fyjsvx7QxrgwKBgQCXBT7tF7qS/MF1aKCIAQFclL7cBtMDgyQBtxfm7A23vKPnX/2J9vVTrggCdHpWY653O54tDGbR0yUd0C4rdyY4el6bf6oW5lMbrhxb0UacT7vyE466vgAF2OcgToKHz4ltYesHyL7hAX0UlpQ/+6odHyfWO7HECd23FOx/JoXxcwKBgGoZQXVoZ6/iWBlBFLdFLJZSgmPR1tyUQW3SqmqTunVYiouW7cx43M+FaZhH2GzqcPDSyHbrw7y/FZTx0bhshjXRJOjmb3tCXHqPOHIrVH7oDid81DrjBOfvnfZz3GHLRzLwyqWjOUcrlMz3MnicGI6xFjM8WPzk39kL7ddza3W3AoGAcmIwxRch4065oJm8bQaF539qB/DLRm+/h+ULn4XO5gllA0w7FpIMQnMf8ewS9PfPHtPy/B/WiLop9KjHL3ixZDmbCp/pTirpa5+2Lv0VRDNIIRQbGOnrzoH4nQmZ6A+TAVwWzfCIUpf0CvH6G3Qb5q7AV40jGV53pcGdVrjw3m0CgYAJpvYhTjsGIOEodLYUk2nJCWnTOS0dSBpBb5/A25JOITon1w7mQnlThZmuuQXeSxuTt2Q97yFjR7ctT+C//9etkFYLvKK1F19XkmiBtuVb29Sbne6AzfqMwnrNmyTRa+6P/+RKDENiZehtZ2EWgXc78xvKbShackC43wbLxfgNqQ==',
    'ALIPAY_PUBLIC_KEY'  => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAj5IxB5luVWyWVCgBsQP64G4kks0Nffetv/JeKO468+g8NvCKL0bf2rNBaGvEQIbVPoS3g4QTf2BnWK6wlBCxZK75beZG3dbv+j07iVr6TL79Eyw6LzNRGMiwg0Pz6BiHKhIVtghCxhxJpM1KnU6l5NevLDFeTJss8d41WCRoxMuIgSAeTILImJUzVfu43dI1KoM6cABAJGM4iLISdGvar9OGYYA1MMNMUycuYbsXAxtvlW2OxrT2UJzEScb7XY6FsiWhdk3tnEHfq0ozYjrQTCl6oQGuFxvgOrcojxSq0cr5Q3wRzqioaozJJLUdWeSqLRlA2Ay5SEZZhnc8etme2QIDAQAB',

    // 配置邮件发送服务器
    'MAIL_HOST' =>'smtp.gmail.com',//smtp服务器的名称
    'MAIL_PORT' =>'465',//smtp服务器的名称
    'MAIL_SMTPAUTH' =>TRUE, //启用smtp认证
    'MAIL_USERNAME' =>'jackdaniels241194@gmail.com',//你的邮箱名
    'MAIL_FROM' =>'jackdaniels241194@gmail.com',//发件人地址
    'MAIL_FROMNAME'=>'WEDO',//发件人姓名
    'MAIL_PASSWORD' =>'jack054321',//邮箱密码
    'MAIL_CHARSET' =>'utf-8',//设置邮件编码
    'MAIL_ISHTML' =>TRUE, // 是否HTML格式邮件
);
 
