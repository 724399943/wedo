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
    <script src="/Static/Public/Wechat/js/common.js" type="text/javascript"></script>
    <script src="/Static/Public/Shop/js/baiduTemplate.js" type="text/javascript"></script>
    <script src="/Static/Public/Wechat/js/jquery-1.8.3.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="/Static/Public/Admin/css/style.default.css" type="text/css" />
    <link rel="shortcut icon" href="/Static/Public/xcrozz.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/Static/Public/Admin/css/plugins/uniform.tp.css" />
    <link rel="stylesheet" href="/Static/Public/Admin/css/pop.css" type="text/css" />
    
</head>
    <body class="withvernav">
        <div class="bodywrapper">
            <div class="topheader">
                <div class="left" style="color:#fff">
                    <ul>
                        <li><?php echo (L("_PC_SETTING_WELCOME_BACK_")); ?> <?php echo ($_SESSION['userInfo']['nickname']); ?></li>
                    </ul>
                </div>
            </div>
            
            <div class="header">
                <ul class="headermenu">
	<li class="<?php if(in_array(CONTROLLER_NAME, array('Agent', 'Goods')) && in_array(ACTION_NAME, array('agentGoodsCategory', 'agentGoods', 'agentGoodsComment', 'addGoods', 'editGoods', 'goodsDetail'))): ?>current<?php endif; ?>">
		<a href="<?php echo U('Agent/agentGoodsCategory');?>">
			<span class="icon icon-message"></span>
			<span class="tet"><?php echo (L("_PC_TOP_MENU_GOODS_MANAGER_")); ?></span>
		</a>
		<em></em>
	</li>

	<li class="<?php if(in_array(CONTROLLER_NAME, array('Order'))): ?>current<?php endif; ?>">
		<a href="<?php echo U('Order/orderList');?>">
			<span class="icon icon-orders"></span>
			<span class="tet"><?php echo (L("_PC_TOP_MENU_ORDER_MANAGER_")); ?></span>
		</a>
		<em></em>
	</li>

	<li class="<?php if(in_array(CONTROLLER_NAME, array('Message', 'MessageCheck'))): ?>current<?php endif; ?>">
		<a href="<?php echo U('Message/index');?>">
			<span class="icon icon-pencil"></span>
			<span class="tet"><?php echo (L("_PC_TOP_MENU_MESSAGE_MANAGER_")); ?></span>
		</a>
		<em></em>
	</li>

	<li class="<?php if(in_array(CONTROLLER_NAME, array('Point', 'Agent', 'PointOrder', 'Collect')) && in_array(ACTION_NAME, array('pointGoods', 'pointLog', 'myPointGoods', 'goodsDetail', 'orderDetail', 'pointOrder', 'pointInfo', 'pointOrderDetail', 'collectGoods'))): ?>current<?php endif; ?>">
		<a href="<?php echo U('Point/pointGoods');?>">
			<span class="icon icon-point"></span>
			<span class="tet"><?php echo (L("_PC_TOP_MENU_MY_POINT_")); ?></span>
		</a>
		<em></em>
	</li>

	<li class="<?php if(in_array(CONTROLLER_NAME, array('Bidding', 'Agent', 'User', 'GoodsCheck')) && in_array(ACTION_NAME, array('biddingBanner', 'settlementLog', 'withdraw', 'withdrawSuccess', 'goodsToAuth', 'goodsToTop', 'biddingRecord', 'biddingBanner', 'biddingIndexGoods', 'biddingFavorableGoods', 'biddingAgent', 'platformBiddingRecord', 'toBiddingIndexGoods', 'toBiddingFavorableGoods', 'toBiddingAgent', 'toBiddingBanner', 'payForBidding', 'basicData', 'editBasicData'))): ?>current<?php endif; ?>">
		<a href="<?php echo U('Agent/basicData');?>">
			<span class="icon icon-admin"></span>
			<span class="tet"><?php echo (L("_PC_TOP_MENU_UESR_CENTER_")); ?></span>
		</a>
		<em></em>
	</li>
	
	<li class="<?php if(in_array(CONTROLLER_NAME, array('Agent', 'User', 'Article')) && in_array(ACTION_NAME, array('chooseLanguage', 'changePassword', 'userFeedback', 'feedbackSuccess', 'articleDetail'))): ?>current<?php endif; ?>">
		<a href="<?php echo U('Agent/changePassword');?>">
			<span class="icon icon-system"></span>
			<span class="tet"><?php echo (L("_PC_TOP_MENU_SETTING_")); ?></span>
		</a>
		<em></em>
	</li>

	<li>
		<a href="<?php echo U('Login/logout');?>">
			<span class="icon icon-exit"></span>
			<span class="tet"><?php echo (L("_PC_TOP_MENU_LOGOUT_")); ?></span>
		</a>
		<em></em>
	</li>
</ul>
            </div>
            <div class="main-date-lr">
              <div class="vernav2 iconmenu">
                
	<ul>
	<li class="current">
		<ul class="Jcon-ctr">
			<li class="<?php if(in_array(CONTROLLER_NAME, array('Agent')) && in_array(ACTION_NAME, array('changePassword'))): ?>on<?php endif; ?>">
				<a href="<?php echo U('Agent/changePassword');?>"><?php echo (L("_PC_SETTING_CHANGE_PASSWORD_")); ?></a>
			</li>
			<li class="<?php if(in_array(CONTROLLER_NAME, array('User')) && in_array(ACTION_NAME, array('chooseLanguage'))): ?>on<?php endif; ?>">
				<a href="<?php echo U('User/chooseLanguage');?>"><?php echo (L("_PC_SETTING_CHOOSE_LANGUAGE_")); ?></a>
			</li>
			<li class="<?php if(in_array(CONTROLLER_NAME, array('User')) && in_array(ACTION_NAME, array('userFeedback', 'feedbackSuccess'))): ?>on<?php endif; ?>">
				<a href="<?php echo U('User/userFeedback');?>"><?php echo (L("_PC_SETTING_CUSTOMER_FEEDBACK_")); ?></a>
			</li>
			<li class="<?php if(CONTROLLER_NAME == 'Article' && $_GET['sign'] == 'agreement'): ?>on<?php endif; ?>">
				<a href="<?php echo U('Article/articleDetail', array('sign'=> 'agreement'));?>"><?php echo (L("_PC_SETTING_USER_AGREEMENT_")); ?></a>
			</li>
			<li class="<?php if(CONTROLLER_NAME == 'Article' && $_GET['sign'] == 'aboutUs'): ?>on<?php endif; ?>">
				<a href="<?php echo U('Article/articleDetail', array('sign'=> 'aboutUs'));?>"><?php echo (L("_PC_SETTING_ABOUT_US_")); ?></a>
			</li>
		</ul>
	</li>
</ul>

                <a class="togglemenu"></a>
                <br /><br />
            </div>
            <div class="centercontent">
                
<div class="pageheader">
    <h1 class="pagetitle">
    	<?php echo (L("_PC_SETTING_CHANGE_PASSWORD_")); ?>
    </h1>
    <span class="pagedesc"></span>
</div>

<div id="contentwrapper" class="contentwrapper">
	<form class="stdform stdform2" id="JsubmitForm">
		<div class="line-dete" >
			<label><?php echo (L("_PC_SETTING_CURRENT_PASSWORD_")); ?>：</label>
	        <span class="field">
	        	<input type="password" class="smallinput" name="password">
	        </span>
		</div>
		<div class="line-dete" >
			<label><?php echo (L("_PC_SETTING_NEW_PASSWORD_")); ?>：</label>
	        <span class="field">
	        	<input type="password" class="smallinput" name="new_password">
	        </span>
		</div>
		<div class="line-dete" >
			<label><?php echo (L("_PC_SETTING_CONFIRM_PASSWORD_")); ?>：</label>
	        <span class="field">
	        	<input type="password" class="smallinput" name="new_repassword">
	        </span>
		</div>
		<div class="line-dete">
			<label></label>
	        <span class="field">
	        	<input type="button" class="stdbtn Jcommit" value="<?php echo (L("_COMMON_EDIT_")); ?>" style="margin-left:10px" />
	        </span>
		</div>
	</form>
</div>

            </div>
            </div>
        </div>
        
        <script src="/Static/Public/Admin/js/pop.js" type="text/javascript"></script>
        <script type="text/javascript">
            function msgBox(title, content, time) {
                var _title = title ? title : '提示';
                var _time = time ? time : 1500;

                popwin(_title, content);
                setTimeout(function() {
                    window.location.href = window.location.href;
                }, _time);
            }
             /*
             * loading弹窗
             * 打开loading:  popupWin.show(msg)
             * 关闭loading:  popupWin.hide()
             */
            var popupWin = (function() {
                var popup =  $('<div class="popup"></div>').hide(),
                    content = $('<div class="popup-content"></div>'),
                    text = $('<span class="content-inner"></span>');

                $('body').append(popup.append(content.append(text)));

                return {
                    show: function(msg) {
                        text.html(msg);
                        popup.show();
                    },
                    hide: function() {
                        popup.hide();
                    }
                }
            })();

            function maskFade(type) {
                (type == '1') ? $(".mask").fadeIn() : $(".mask").fadeOut();
                (type == '1') ? $(".replay_m").fadeIn() : $(".replay_m").fadeOut();
            }

            function messageBox(title, placeholder, callback, needTextarea) {
                if ( needTextarea ) {
                    $('#Ktitle').next().remove();
                    $('#Ktitle').after(placeholder);
                } else {
                    $('#Kcontent').attr('placeholder', placeholder);
                }
                maskFade(1);
                $('#Ktitle').text(title);
                var ctr = 1;
                $('.rbtn .Ksure').click(function() {
                    maskFade(0);
                    if (callback && ctr == 1) {
                        ctr = 0;
                        callback();
                    }
                });
                $('.Kcancel').click(function() {
                    maskFade(0);
                    ctr = 0;
                });
                $(".mask").click(function(){
                    maskFade(0);
                });
            }
        </script>
        
<script type="text/javascript">
$(function(){
	$('.Jcommit').click(function(){
		$.ajax({
			url: "<?php echo U('Agent/changePassword');?>",
			type: 'POST',
			dataType: 'json',
			data: $('#JsubmitForm').serialize()
		})
		.done(function(data) {
			if (data.status == '200000') {
				alert('<?php echo (L("_COMMON_SUCCESS_")); ?>');
				window.location.href = "<?php echo U('Login/logout');?>";
			}else{
				alert(data.message);
			}
		})
	})	  
 })

</script>

    </body>
</html>