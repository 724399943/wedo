<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo (L("_PC_MESSAGE_SYSTEM_")); ?></title>
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
			<li class="<?php if(in_array(CONTROLLER_NAME, array('Message')) && in_array(ACTION_NAME, array('index', 'messageDetail'))): ?>on<?php endif; ?>">
				<a href="<?php echo U('Message/index');?>"><?php echo (L("_PC_MESSAGE_SYSTEM_")); ?></a>
			</li>
			<li class="<?php if(in_array(CONTROLLER_NAME, array('MessageCheck')) && in_array(ACTION_NAME, array('issuedMessage', 'issuingDetail'))): ?>on<?php endif; ?>">
				<a href="<?php echo U('MessageCheck/issuedMessage');?>"><?php echo (L("_PC_MESSAGE_MY_POST_")); ?></a>
			</li>
			<li class="<?php if(in_array(CONTROLLER_NAME, array('Message')) && in_array(ACTION_NAME, array('consultation'))): ?>on<?php endif; ?>">
				<a href="<?php echo U('Message/consultation');?>"><?php echo (L("_PC_MESSAGE_CONTACT_USER_")); ?></a>
			</li>
			<li class="<?php if(in_array(CONTROLLER_NAME, array('MessageCheck')) && in_array(ACTION_NAME, array('issuingMessage', 'payForIssuing'))): ?>on<?php endif; ?>">
				<a href="<?php echo U('MessageCheck/issuingMessage');?>"><?php echo (L("_PC_MESSAGE_PUBLISH_")); ?></a>
			</li>
		</ul>
	</li>
</ul>

                <a class="togglemenu"></a>
                <br /><br />
            </div>
            <div class="centercontent">
                
	 <div class="pageheader">
	    <h1 class="pagetitle"><?php echo (L("_PC_MESSAGE_SYSTEM_")); ?></h1>
	    <span class="pagedesc"></span>
	</div>
	<div id="contentwrapper" class="contentwrapper">
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="template"></table>
        <div class="page-box" id="pageBox"></div>
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
        
<script id="message_tpl" type="text/html">
<tr>
	<th><?php echo (L("_COMMON_NO_")); ?></th>
	<th><?php echo (L("_PC_MESSAGE_TITLE_")); ?></th>
	<th><?php echo (L("_PC_MESSAGE_MAIN_CONTENT_")); ?></th>
	<th><?php echo (L("_PC_MESSAGE_PUBLISH_TIME_")); ?></th>
	<th><?php echo (L("_COMMON_STATUS_")); ?></th>
	<th width="10%"><?php echo (L("_COMMON_OPERATE_")); ?></th>
</tr>
<%for(var i = 0; i < list.length; i ++){%>
	<%
		var data = list[i],
			date = new Date(data['add_time'] * 1000);
	%>
	<tr>
		<td><%=data['id']%></td>
		<td><%=data['title']%></td>
		<td><%=data['content']%></td>
		<td><%=date.pattern('yyyy-MM-dd HH:mm:ss')%></td>
		<td>
			<%if( data['is_read'] == '1' ) {%>
				已查看
			<%}else{%>
				未读
			<%}%>
		</td>
		<td class="center">
			<a class="stdbtn btn_lime" href="<%=jumpUrl%>?id=<%=data['id']%>"><%=view%></a>
		</td>
	</tr>
<%}%>
</script>
<script type="text/javascript">
	/*使用模板引擎*/
    var bt = baidu.template;
    function loadData(page) {
    	popupWin.show('<?php echo (L("_COMMON_LOADING_")); ?>');
    	$.ajax({
			url: '<?php echo U('Message/systemMessage');?>',
			type: 'POST',
			dataType: 'json',
			data: {
				page : page,
				status : '1',
				type : '1'
			}
		})
		.done(function(returnData) {
			if ( returnData['data']['list'].length ) {
				returnData['data']['view'] = "<?php echo (L("_COMMON_VIEW_")); ?>";
				returnData['data']['jumpUrl'] = "<?php echo U('Message/messageDetail');?>";
				var html = bt('message_tpl', returnData['data']);
				$('#template').html(html);
				createPageTags(returnData['data']['page'], returnData['data']['count'], 0);
				listenPageEvent(loadData);
			} else {
				alert('<?php echo (L("_COMMON_NO_DATA_")); ?>');
			}
			popupWin.hide();
		});
    }
    loadData(1);
</script>

    </body>
</html>