<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>WEDO管理后台</title>
    <link rel="stylesheet" href="/Static/Public/Admin/css/style.default.css" type="text/css" />
    <link href="/Static/Public/fxw.ico" mce_href="/Static/Public/fxw.ico" rel="bookmark" type="image/x-icon" /> 
    <link href="/Static/Public/fxw.ico" mce_href="/Static/Public/fxw.ico" rel="icon" type="image/x-icon" /> 
    <link href="/Static/Public/fxw.ico" mce_href="/Static/Public/fxw.ico" rel="shortcut icon" type="image/x-icon" />
    <!--[if IE 9]>
        <link rel="stylesheet" media="screen" href="/Static/Public/Admin/css/style.ie9.css"/>
    <![endif]-->
    <!--[if IE 8]>
        <link rel="stylesheet" media="screen" href="/Static/Public/Admin/css/style.ie8.css"/>
    <![endif]-->
    <!--[if lt IE 9]>
    	<script src="/Static/Public/Admin/js/plugins/css3-mediaqueries.js"></script>
    <![endif]-->
</head>

<body class="loginpage">
	<div class="loginbox">
    	<div class="loginboxinner">
            <div class="logo">
            	<h1 class="logo">
                    <img src="http://placehold.it/134x60" />
                </h1>
				<span class="slogan">WEDO-管理系统</span>
            </div><!--logo-->
            
            <br clear="all" /><br />
            
            <div class="nousername">
				<div class="loginmsg">密码不正确.</div>
            </div><!--nousername-->
            
            <div class="nopassword">
				<div class="loginmsg">密码不正确.</div>
                <div class="loginf">
                    <div class="thumb"><img src="http://usr.im/260x85" /></div>
                    <div class="userlogged">
                        <h4></h4>
                        <a href="index.html">Not <span></span>?</a> 
                    </div>
                </div>
            </div>
            
            <form id="login" action="" method="post">
            	
                <div class="username">
                	<div class="usernameinner">
                    	<input type="text" name="account" id="account" />
                    </div>
                </div>
                
                <div class="password">
                	<div class="passwordinner">
                    	<input type="password" name="password" id="password" />
                    </div>
                </div>
                <?php if($is_show == 1): ?><div style="display: block" class="in-yan">
                        <div class="yan"><input style="height: 26px;margin-bottom: 33px;" type="text" class="text" id="verify" name="verify_c" value="" placeholder="验证码"><img id="verifyImg" alt="验证码" src="<?php echo U('Login/verify_c');?>" title="点击刷新" onclick="this.src='<?php echo U('Login/verify_c');?>?t='+Date.parse(new Date());">
                        </div>
                        <span id="JchangeVerify" class="txt" style="display:none;cursor: pointer" onclick="document.getElementById('verifyImg').src='<?php echo U('Login/verify_c');?>?t='+Date.parse(new Date());">换一张</span>
                    </div><?php endif; ?>
                <input type="submit" class="submit_btn" value="登录">
                
                <div class="keep">
                    <label>
                        <input type="checkbox" value="1" checked="checked" name="rememberPassword" />保存一周
                    </label>
                </div>
            
            </form>
            
        </div><!--loginboxinner-->
    </div><!--loginbox-->

    <script type="text/javascript" src="/Static/Public/Admin/js/plugins/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="/Static/Public/Admin/js/plugins/fxw-base.js"></script>
</body>
</html>