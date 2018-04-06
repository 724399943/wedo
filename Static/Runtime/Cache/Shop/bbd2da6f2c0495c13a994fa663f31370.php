<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo C('systemName');?></title>
    <meta name="renderer" content="webkit">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit">
    <link rel="shortcut icon" href="/Static/Public/xcrozz.ico" type="image/x-icon">
    <link href="/Static/Public/Shop/css/base.css" rel="stylesheet">
    <link href="/Static/Public/Shop/css/style.css" rel="stylesheet">
    <script src="/Static/Public/Wechat/js/jquery-1.8.3.min.js" type="text/javascript"></script>
    <link href="/Static/Public/Shop/css/login.css" rel="stylesheet">
    <style type="text/css">
        body{background: #fff;}
    </style>
</head>
<div class="login-box">
    <div class="l-top">
        <a href="/" class="logo">
            <img src="/Static/Public/Wechat/images/wedo_logo.png" width="134" alt="">
        </a>
    </div>
    <div class="box clearfix">
        <img src="/Static/Public/Shop/images/loginpic.jpg" alt="" class="pic">
        <form method="post" id="Jform">
            <div class="login-input-b">
                <div class="login-data">
                    <div class="tit"><?php echo (L("_PC_LOGIN_MERCHANT_LOG_IN_")); ?><span class="fl"><?php echo (L("_PC_LOGIN_NO_ACCOUNT_YET_")); ?><a href="<?php echo U('Login/register');?>"><?php echo (L("_PC_LOGIN_SIGN_UP_")); ?></a></span></div>
                    <div class="in-box">
                        <em class="ico"></em>
                        <input placeholder="<?php echo (L("_PC_LOGIN_PHONE_NUMBER_OR_EMAIL_")); ?>" type="text" class="text" name="account" id="JParams">
                        <span class="true"></span>
                    </div>
                    <div class="in-box">
                        <em class="ico pas"></em>
                        <input placeholder="<?php echo (L("_PC_LOGIN_ENTER_PASSWORD_")); ?>" type="password" class="text" name="password" id="password">
                    </div>
                    <!-- <div style="display:none" class="in-yan">
                        <input type="text" class="text" id="Jverify" name="verify_c" value="" placeholder="验证码">
                        <div class="yan"><img id="verifyImg" alt="验证码" src="<?php echo U('Login/verify_c');?>" title="点击刷新" onclick="this.src='<?php echo U('Login/verify_c');?>?t='+Date.parse(new Date());"></div>
                        <span id="JchangeVerify" class="txt" style="cursor: pointer" onclick="document.getElementById('verifyImg').src='<?php echo U('Login/verify_c');?>?t='+Date.parse(new Date());">换一张</span>
                    </div> -->
                    <div class="in-txt">
                        <label class="lab">
                            <input type="checkbox" name="rememberPassword" value="1" checked>  
                            <?php echo (L("_PC_LOGIN_LOG_IN_FOR_TWO_WEEK_")); ?>
                        </label>
                        <a href="<?php echo U('Login/forgetPassword');?>" class="link"><?php echo (L("_PC_LOGIN_FORGET_PASSWORD_")); ?>?</a>
                    </div>
                    <input type="submit" class="conment-login login-submit" id="Jsubmit" value="<?php echo (L("_PC_LOGIN_LOG_IN_")); ?>">
                    <br>
                    <a href="javascript:;" style="line-height: 22px;"><?php echo (L("_PC_LOGIN_ANY_PROBLEMS_CONTACT_")); echo C('serviceTel');?></a>
                </div>
            </div>
        </form>
    </div>
</div>
<!--  提示信息 -->
<div class="tips" id="tips">
    <span class="ico"></span>
    <span class="txt">键盘大写锁定已打开，请注意大小写</span> <em></em>
</div>
<script type="text/javascript" src="/Static/Public/Shop/js/keydown.js"></script>
<script type="text/javascript">
// var _isFalse = 0;
// 
//错误提示
function falltips(emlemt,txt){
    var l = $(emlemt).offset().left;
    var t = $(emlemt).offset().top;
    $('#tips').css({'top':t-44,"left":l});
    $('#tips').show();
    $('#tips').find('.txt').html(txt);
}
$(function() {
    //边框颜色
    $('.in-box .text').on({
        blur:function(event) {
            $(this).parent('.in-box').removeClass('blur-box');
            $(this).siblings('.pass-power').hide();
            $('#tips').hide();
        },
        focus:function(event) {
            $(this).parent('.in-box').addClass('blur-box');
            $(this).siblings('.pass-power').show();
            $('#tips').hide();
        }
    });

    $('.in-yan .text').on({
        blur:function(event) {
            $(this).removeClass('blur-box');
        },
        focus:function(event) {
            $(this).addClass('blur-box');
        }
    });

    $('#Jsubmit').click(function() {
        if ($('#JParams').val() == '') {
            falltips('#JParams','<?php echo (L("_PC_LOGIN_ENTER_CORRECT_LOG_IN_ID_")); ?>')
            return false;
        } else if ($('#password').val() == '') {
            falltips('#password','<?php echo (L("_PC_LOGIN_PASSWORD_NOT_MATCH_")); ?>')
            return false;
        }

        ajaxLogin();
        return false;
    });
});

function ajaxLogin() {
    $.ajax({
        url: "<?php echo U('Login/login');?>",
        type: 'POST',
        dataType: 'json',
        data: $('#Jform').serialize()
    })
    .done(function(returnData) { 
        if (returnData['status'] == '200000') {
            window.location.href = "<?php echo U('Agent/agentGoodsCategory');?>";
        } else if (returnData['status'] == '400025') {
            window.location.href = "<?php echo U('Agent/completeAgent');?>?user_id="+returnData['data']['userId'];
        } else {              
            falltips('#JParams',returnData.message);
        }
    }); 
}
</script>