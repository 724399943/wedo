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
            <img src="/Static/Public/Shop/images/okshop_logo.png" width="134" alt="">
        </a>
    </div>
    <div class="box clearfix">
        <img src="/Static/Public/Shop/images/loginpic.jpg" alt="" class="pic">
        <div class="login-input-b">
            <div class="login-data login-sigin-data">
                <div class="tit">
                    <?php echo (L("_PC_LOGIN_SIGN_UP_WEDO_MERCHANT_")); ?>
                    <span class="fl"><?php echo (L("_PC_LOGIN_ALREADY_AS_A_MEMBER_")); ?><a href="<?php echo U('Login/login');?>"><?php echo (L("_PC_LOGIN_TO_LOG_IN_")); ?></a></span>
                </div>
                <form id="Jform">
                    <div class="in-box">
                        <em class="ico ph"></em>
                        <input placeholder="<?php echo (L("_PC_LOGIN_ENTER_YOUR_MOBILE_NUMBER_")); ?>" type="text" class="text" name="phone" id="Jphone">
                        <span class="true"></span>
                    </div>
                    <div class="in-box">
                        <em class="ico pas"></em>
                        <input id="password" name="password" placeholder="<?php echo (L("_PC_LOGIN_ENTER_PASSWORD_")); ?>" type="password" class="text">
                        <div class="pass-power">
                            <?php echo (L("_PC_LOGIN_SAFETY_LOAD_")); ?>
                            <span class="r Jone"><?php echo (L("_PC_LOGIN_SAFETY_WARK_")); ?></span>
                            <span class='m Jtwo'><?php echo (L("_PC_LOGIN_SAFETY_MIDDLE_")); ?></span>
                            <span class='s Jthree'><?php echo (L("_PC_LOGIN_SAFETY_STRONG_")); ?></span>
                        </div>
                    </div>
                    <div class="in-box">
                        <em class="ico pas"></em>
                        <input placeholder="<?php echo (L("_PC_LOGIN_CONFIRM_PASSWORD_")); ?>" type="password" class="text" name="repassword" id="repassword">
                    </div>
                    <!-- <div  class="in-yan" style="display: none">
                        <span class="tVerify" style="margin-left: 40px; float: left;"></span>
                        <div class="yan"><input style="height: 37px" type="text" class="text" id="Tverify" name="verify_c" value="" placeholder="验证码"><img id="verifyImg" alt="验证码" src="<?php echo U('Login/verify_c');?>" title="点击刷新" onclick="this.src='<?php echo U('Login/verify_c');?>?t='+Date.parse(new Date());">
                        </div>
                        <span id="JchangeVerify" class="txt" style="display:none;cursor: pointer" onclick="document.getElementById('verifyImg').src='<?php echo U('Login/verify_c');?>?t='+Date.parse(new Date());">换一张</span>
                    </div> -->
                    <div class="in-yan" style="display: block">
                        <span class="adreess" style="margin-left: 40px; float: left;"></span>
                        <input type="text" class="text" name="verify" id="JverifyCode" value=""  placeholder="<?php echo (L("_PC_LOGIN_ENTER_PHONE_CODE_")); ?>">
                        <a href="javascript:;"  class="btn" id="Jverify" style="border-radius: 25px;"><?php echo (L("_PC_LOGIN_GET_VERIFICATION_CODE_")); ?></a>
                        <em class="key"></em>
                    </div>
                    <div class="in-box">
                        <!-- <em class="ico ph"></em> -->
                        <input placeholder="<?php echo (L("_PC_LOGIN_REFERRALS_")); ?>" type="text" class="text" name="referrer">
                        <span class="true"></span>
                    </div>
                    <div class="in-txt">
                        <label class="lab">
                            <input type="checkbox" id="Jprotocol" name="" checked value="">         
                            <?php echo (L("_PC_LOGIN_HAVE_READ_AND_AGREE_")); ?>
                        </label>
                        <a class="xy Jxy" href="javascript:;"><?php echo (L("_PC_LOGIN_SERVICE_AGREEMENT_")); ?></a>
                    </div>
                    <input type="hidden" name="status" value="1">
                    <input type="hidden" name="type" value="0" />
                    <input type="hidden" name="sendType" id="JSendType" value="" />
                    <a href="javascript:;" id="Jsbumit" class="conment-login"><?php echo (L("_PC_LOGIN_REGISTER_")); ?></a>
                    <a href="javascript:;" style="line-height: 22px;"><?php echo (L("_PC_LOGIN_REGISTER_ANY_PROBLEM_")); echo C('serviceTel');?></a>
                </form>
            </div>
        </div>
    </div>
</div>
<!--  提示信息 -->
<div class="tips" id="tips">
    <span class="ico"></span>
    <span class="txt">键盘大写锁定已打开，请注意大小写</span> <em></em>
</div>
<!-- okshop协议 -->
<div class="baiyang-agreement">
    <div class="ag-main Jbaiyang-main">
        <div class="a-box">
            <div class="tit"><?php echo (L("_PC_LOGIN_USER_AGREEMEND_")); ?></div>
            <div class="data">
                <p class="p-tit">okshop用户注册协议</p>
                <p>感谢您对okshop商城的信任，在注册之前请仔细阅读《okshop商城服务协议》，用户必须完全同意所有服务条款，并遵守相关法律法规，否则不可以在okshop商城注册。</p>
                <p class="p-tit">一、服务协议条款的确认和接纳</p>
               <p>本站的所有电子服务协议和运作权归okshop商城所有。您必须完全同意所有服务协议条款并完成注册程序，才能成为本站的正式用户。您确认：本服务协议条款是处理双方权利义务的契约，除非违反国家强制性法律，否则始终有效。在提交订单的同时，您也同时承认了您拥有购买这些产品的权利和行为能力，并且您将对您在订单中提供的所有信息的真实性负责。如果您在18周岁以下，您只能在父母或监护人的监护参与下才能使用本站。okshop商城保留在中华人民共和国大陆地区施行之法律允许的范围内独自决定拒绝服务、关闭用户账户、清除或编辑内容或取消订单的权利。</p>
               <p class="p-tit">二、用户注册条款</p>
               <p>1. 提供真实准确的个人资料。</p>
               <p>2. okshop商城拥有通过邮件、短信、电话、邮寄等形式，向在本站注册、购物用户、收货人发送订单信息、促销活动等告知信息的权利。</p>
               <p>3. 严格遵守以下业务，否则okshop商城有权利注销用户注册账号，并且用户需对自己的言论和行为承担法律责任</p>
               <p>(1)不得传输或发表煽动抗拒、破坏宪法和法律、行政法规实施的言论，煽动颠覆国家政权，推翻社会主义制度的言论，煽动分裂国家、破坏国家统一的言论，煽动民族仇恨、民族歧视、破坏民族团结的言论；</p>
               <p>(2)从中国大陆向境外传输资料信息时必须符合中国有关法律法规；</p>
               <p>(3)不得利用本网站从事洗钱、窃取商业秘密、窃取个人信息等违法犯罪活动；</p>
               <p>(4)不得干扰本网站的正常运转，不得侵入本网站及国家计算机信息系统；</p>
               <p>(5)不得传输或发表任何违法犯罪的、骚扰性的、中伤他人的、辱骂性的、恐吓性的、伤害性的、庸俗的、淫秽的、不文明的等信息资料；</p>
               <p>(6)不得传输或发表损害国家社会公共利益和涉及国家安全的信息资料或言论；</p>
               <p>(7)不得教唆他人从事本条所禁止的行为；</p>
               <p>(8)不得利用在本网站注册的账户进行牟利性经营活动（如批发、炒货、转卖）；</p>
               <p>(9)不得发布任何侵犯他人著作权、商标权等知识产权及其他合法权利的内容。</p>
               <p>4. okshop商城承诺：不公开用户的姓名、地址和地址邮箱，予以严格保护用户的隐私权</p>
               <p class="p-tit">三、商品信息</p>
               <p>1、本站的商品信息随时会发生变动，本站对此不做特别通知。本站会尽最大的努力保持商品的真实准确信息，但是由于商品信息量过于庞大，且受互联网技术发展水平等因素的限制，网页显示的信息可能会有一定的滞后性或差错，对此情形请用户充分知悉并予以理解。如用户发现商品信息错误的或有疑问的，请用户在第一时间告诉本站，并不要提交订单。</p>
               <p>2、由于促销活动引起的价格变化，本站将不会对之前售出订单中的相同商品补差价</p>
               <p class="p-tit">四、订单、送货以及费用</p>
               <p>1.请仔细填写收货人的真实姓名、详细地址及联系电话，如因以下情况造成的包裹延迟或无法发送的情况，okshop商城将不承担责任：</p>
               <p>（1）客户提供错误信息和不详细的地址；</p>
               <p>（2）货物送达无人签收，由此造成的重复配送所产生的费用及相关的后果；</p>
               <p>（3）不可抗力，例如：自然灾害、交通戒严、突发战争等。</p>
               <p>2. 由于市场变化及各种以合理商业努力难以控制的因素的影响，本站无法保证您提交的订单信息中希望购买的商品都会有货；如您拟购买的商品，发生缺货，您有权取消订单。</p>
               <p class="p-tit">五、用户账号、密码和安全性</p>
               <p>用户注册成功后，可以更改密码。用户将对自己的用户名和密码负全责。用户原则上不可以将用户名和密码公布给其他人。如果用户发现网站的信息发生外泄，请立即通知okshop商城。</p>
               <p class="p-tit">六、所有权及知识产权</p>
               <p>1. 用户一旦接受本协议，即表明该用户主动将其在任何时间段在本站发表的任何形式的信息内容（包括但不限于客户评价、客户咨询、各类话题文章等信息内容）的财产性权利等任何可转让的权利，如著作权财产权（包括并不限于：复制权、发行权、出租权、展览权、表演权、放映权、广播权、信息网络传播权、摄制权、改编权、翻译权、汇编权以及应当由著作权人享有的其他可转让权利），全部独家且不可撤销地转让给okshop商城所有，用户同意okshop商城有权就任何主体侵权而单独提起诉讼。</p>
               <p>2. 本协议已经构成《中华人民共和国著作权法》第二十五条（条文<?php echo (L("_COMMON_NO_")); ?>依照2011年版著作权法确定）及相关法律规定的著作财产权等权利转让书面协议，其效力及于用户在okshop商城上发布的任何受著作权法保护的作品内容，无论该等内容形成于本协议订立前还是本协议订立后。</p>
               <p>3. 用户同意并已充分了解本协议的条款，承诺不将已发表于本站的信息，以任何形式发布或授权其它主体以任何方式使用（包括但不限于在各类网站、媒体上使用）。</p>
               <p>4. okshop商城是本站的制作者,拥有此网站内容及资源的著作权等合法权利,受国家法律保护,有权不时地对本协议及本站的内容进行修改，并在本站张贴，无须另行通知用户。在法律允许的最大限度范围内，okshop商城对本协议及本站内容拥有解释权。</p>
               <p>5. 除法律另有强制性规定外，未经okshop商城明确的特别书面许可,任何单位或个人不得以任何方式非法地全部或部分复制、转载、引用、链接、抓取或以其他方式使用本站的信息内容，否则，okshop商城有权追究其法律责任。</p>
               <p>6. 本站所刊登的资料信息（诸如文字、图表、标识、按钮图标、图像、声音文件片段、数字下载、数据编辑和软件），均是okshop商城或其内容提供者的财产，受中国和国际版权法的保护。本站上所有内容的汇编是okshop商城的排他财产，受中国和国际版权法的保护。本站上所有软件都是okshop商城或其关联公司或其软件供应商的财产，受中国和国际版权法的保护。</p>
               <p class="p-tit">七、责任限制及不承诺担保</p>
               <p>1. 除非另有明确的书面说明,本站及其所包含的或以其它方式通过本站提供给您的全部信息、内容、材料、产品（包括软件）和服务，均是在"按现状"和"按现有"的基础上提供的。</p>
               <p>2. 除非另有明确的书面说明,okshop商城不对本站的运营及其包含在本网站上的信息、内容、材料、产品（包括软件）或服务作任何形式的、明示或默示的声明或担保（根据中华人民共和国法律另有规定的以外）。</p>
               <p>3. okshop商城不担保本站所包含的或以其它方式通过本站提供给您的全部信息、内容、材料、产品（包括软件）和服务、其服务器或从本站发出的电子信件、信息没有病毒或其他有害成分。</p>
               <p>4. 如因不可抗力或其它本站无法控制的原因使本站销售系统崩溃或无法正常使用导致网上交易无法完成或丢失有关的信息、记录等，okshop商城会合理地尽力协助处理善后事宜。</p>
               <p class="p-tit">八、法律适用、管辖、协议更新与用户阅读义务</p>
               <p>1、本协议之效力、解释、变更、执行与争议解决均适用中华人民共和国大陆地区法律，如无相关法律规定的，则应参照通用国际商业惯例和/或行业惯例。如发生本服务协议或网站规则与中华人民共和国法律相抵触，则这些内容将完全按法律规定重新解释，而其它条款则依旧保持对用户的约束力。</p>
               <p>2、若用户和okshop商城就服务协议的订立和履行等事宜产生争议的，用户和okshop商城均一致同意将相关争议提交okshop商城所在地（广州市）相应级别的法院管辖。</p>
               <p>3、根据国家法律法规变化及本站运营需要，okshop商城有权对本协议条款不时地进行修改，修改后的协议一旦被张贴在本站上即生效，并代替原来的协议。用户可随时登录查阅最新协议；用户有义务不时关注并阅读最新版的服务协议及网站公告。如用户不同意更新后的协议，可以且应立即停止接受本站站依据本协议提供的服务；如用户继续使用本站提供的服务的，即视为同意更新后的协议。</p>
            </div>
            <a href="javascript:;" class="com-btn-ture"><?php echo (L("_PC_LOGIN_I_AGREE_")); ?></a>
        </div>
    </div>
</div>
<script type="text/javascript" src="/Static/Public/Shop/js/passwork.js"></script>
<script type="text/javascript" src="/Static/Public/Shop/js/keydown.js"></script>
<script type="text/javascript">
    var getVerifySign = true;

    //错误提示
    function falltips(emlemt, txt) {
        var l = $(emlemt).offset().left;
        var t = $(emlemt).offset().top;
        $('#tips').css({'top':t-44,"left":l-40});
        $('#tips').show();
        $('#tips').find('.txt').html(txt);
    }

    $('.Jxy').click(function(){
       $('.baiyang-agreement').show(); 
    });

    $('.com-btn-ture').click(function(){
        $('.baiyang-agreement').hide(); 
    });

    $('.baiyang-agreement').click(function(){
        var obj  = $(event.target);
        var leng = $('.Jbaiyang-main').find(obj).length;
        if(leng<1){
           $('.baiyang-agreement').hide(); 
        }
    });

    $(function() {
        /*密码强度*/    
        $.passcon("#password", ".Jone", ".Jtwo", ".Jthree"); 

        /*显示获取验证码*/
        $('#repassword').keyup(function(){
            if( $(this).val() == $('#password').val() ){
                $('.in-yan').show();
            }
        });

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

        /*ajax判断邮箱-手机是否存在*/
        $('#Jphone').on('change',function() {
            if ($('#Jphone').val() == '') {
                falltips('#Jphone','<?php echo (L("_PC_LOGIN_ENTER_YOUR_MOBILE_NUMBER_")); ?>');
                return false;
            } else if (!isTelephone($('#Jphone').val()) && !isEmail($('#Jphone').val()) ) {
                falltips('#Jphone','<?php echo (L("_PC_LOGIN_ENTER_CORRECT_PHONE_")); ?>');
                return false;
            }

            $('#Jphone').siblings('input').remove();
            if (isTelephone($('#Jphone').val())) {
                $('.true').show();
                type = 'phone';
                $('#Jphone').after('<input type="hidden" name="phone" value="'+ $('#Jphone').val() +'" />');
            } else {
                type = 'email';
                $('.true').show();
                $('#Jphone').after('<input type="hidden" name="email" value="'+ $('#Jphone').val() +'" />');
            }

            $.ajax({
                url: "<?php echo U('Login/register');?>",
                type: 'POST',
                dataType: 'json',
                data: {
                    'phone' : $('#Jphone').val(),
                    'type'  : type
                }
            })
            .done(function(data) {
                if (data.error == 1) {
                    $('.true').hide();
                    falltips('#Jphone',data.msg);

                } 
            });
        });
        
        /*获取验证码*/   
        $('#Jverify').click(function() {
            if ($('#Jphone').val() == '') {
                falltips('#Jphone','<?php echo (L("_PC_LOGIN_ENTER_YOUR_MOBILE_NUMBER_")); ?>');
                return;
            } else if (!isTelephone($('#Jphone').val()) && !isEmail($('#Jphone').val()) ) {
                falltips('#Jphone','<?php echo (L("_PC_LOGIN_ENTER_CORRECT_PHONE_")); ?>');
                return;
            } else if ($('#password').val() == '') {
                falltips('#password','<?php echo (L("_PC_LOGIN_ENTER_YOUR_PASSWORD_")); ?>');
                return;
            } else if ($('#password').val().length < 6 || $('#password').val().length >20) {
                falltips('#password','<?php echo (L("_PC_LOGIN_ENTER_NEW_PASSWORD_")); ?>');
                return;
            } else if ($('#password').val() != $('#repassword').val()) {
                falltips('#password','<?php echo (L("_PC_LOGIN_PASSWORD_DID_NOT_MATCH_")); ?>');
                return;
            }

            if (getVerifySign) {
                getVerifySign = false;

                // if (isTelephone($('#Jphone').val())) {
                //     sendType    = 'sendTemplateSMS';
                //     type        = 'phone';
                // } else {
                //    sendType    = 'sendMail';
                //     type        = 'email';
                // }

                $.ajax({
                    url: "<?php echo U('Login/getVerifyCode');?>",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'account': $('#Jphone').val(),
                        'type'          : 0,
                    }
                })
                .done(function(returnData) {
                    if ( returnData['status'] == '200000' ) {
                        alert(returnData['data']['code']);
                        // $('#JSendType').val(sendType);
                        $('#Jverify').text(60);
                        timer();
                    } else {
                        falltips('#Jphone',returnData['message']);
                        getVerifySign = true;                        
                    }
                }); 
            }
        });

        $('#Jsbumit').click(function() {
            if ($('#Jphone').val() == '') {
                falltips('#Jphone','<?php echo (L("_PC_LOGIN_ENTER_YOUR_MOBILE_NUMBER_")); ?>');
                return;
            } else if (!isTelephone($('#Jphone').val()) && !isEmail($('#Jphone').val()) ) {
                falltips('#Jphone','<?php echo (L("_PC_LOGIN_ENTER_YOUR_MOBILE_NUMBER_")); ?>');
                return;
            } else if ($('#password').val() == '') {
                falltips('#password','<?php echo (L("_PC_LOGIN_ENTER_YOUR_PASSWORD_")); ?>');
                return;
            } else if ($('#password').val().length < 6 || $('#password').val().length >20) {
                falltips('#password','<?php echo (L("_PC_LOGIN_ENTER_NEW_PASSWORD_")); ?>');
                return;
            } else if ($('#password').val() != $('#repassword').val()) {
                falltips('#repassword','<?php echo (L("_PC_LOGIN_PASSWORD_DID_NOT_MATCH_")); ?>');
                return;
            } else if ($('#JverifyCode').val() == '') {
                falltips('.adreess','<?php echo (L("_PC_LOGIN_ENTER_VERIFICATION_CODE_")); ?>');
                return;
            } else if (!$('#Jprotocol')[0].checked) {
                msgbox('<?php echo (L("_PC_LOGIN_HAVE_READ_AND_AGREE_")); echo (L("_PC_LOGIN_SERVICE_AGREEMENT_")); ?>');
                return;
            }

            $.ajax({
                url: "<?php echo U('Login/register');?>",
                type: 'POST',
                dataType: 'json',
                data: $('#Jform').serialize()
            })
            .done(function(returnData) {
                if (returnData['status'] == '200000') {
                    window.location.href = "<?php echo U('Agent/completeAgent');?>?user_id=" + returnData['data']['userId'];
                }else {
                    falltips('.adreess', returnData['message']);
                }
            });
        });
    });

    function timer() {
        var value = Number($('#Jverify').text()); 
        if (value > 1) {
            document.getElementById("Jverify").innerHTML = value-1;
            //$('#Jverify').text(value - 1);
        } else {
            document.getElementById("Jverify").innerHTML = '<?php echo (L("_WAP_REGISTER_REGAINVERIFY_")); ?>';
            //$('#Jverify').text('重新获取');
            getVerifySign = true;
            return false;
        }
        window.setTimeout("timer()", 1000); 
    }

    function isTelephone (telephone) {
        var telReg = !!telephone.match(/^(0|86|17951)?(13[0-9]|15[012356789]|17[6780]|18[0-9]|14[57])[0-9]{8}$/);
        //如果手机号码不能通过验证
        return telReg;
    }

    /*判断邮箱格式*/
    function isEmail (email) {
        var emailReg = !!email.match(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/);
        //如果邮箱不能通过验证
        return emailReg;
    }
</script>