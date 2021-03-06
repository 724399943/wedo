<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo (L("_PC_MESSAGE_CONTACT_USER_")); ?></title>
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
    
	<style type="text/css">
		.contentwrapper{padding-bottom:50px;}
		.chatWindow_box{display:-webkit-box;border-radius:5px;border:1px solid #e4e4e4;height:600px;}
		.chatWindow_box .lf{width:20%;}
		.chatWindow_box .lf .ctitle{line-height:50px;background:#967bdc;color:#fff;font-size:18px;border-top-left-radius:5px;text-align:center;}
		.chatWindow_box .lf ul{background:#f9f9f9;overflow-y:scroll;height:550px;}
		.chatWindow_box .lf ul li{overflow:hidden;position:relative;padding:6px 0;}
		.chatWindow_box .lf ul li.on{background:#dedede;}
		.chatWindow_box .lf ul li .headImgbox{width:45px;height:45px;position:relative;float:left;margin-left:5px;}
		.chatWindow_box .lf ul li .headImgbox:before{content:'';display:block;padding:50% 0;}
		.chatWindow_box .lf ul li .headImgbox img{width:100%;height:100%;position:absolute;top:0;left:0;border-radius:50%;}
		.chatWindow_box .lf ul li .name{display:block;color:#485b79;position:absolute;top:50%;left:55px;transform:translateY(-50%);-webkit-transform:translateY(-50%);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;width:70%;}
		.chatWindow_box .rt{width:80%;background:#f9f9f9;}
		.chatWindow_box .rt .talker{height:50px;line-height:50px;color:#485b79;font-size:18px;text-align:center;border-bottom:1px solid #e4e4e4;margin-top:-1px;}
		.em-widget-wrapper{position:relative;width:100%;height:550px;background:#f9f9f9;}
		.em-widgetBody-wrapper{position:absolute;top:0;bottom:140px !important;width:100%;border-bottom:1px solid #e4e4e4;-webkit-transition: margin .5s,top 0s;-moz-transition: margin .5s,top 0s;transition: margin .5s,top 0s;-webkit-overflow-scrolling: touch;overflow-y:scroll;}
		.em-widget-chat{padding-bottom:20px;}
		.em-widget-chat:after {content: "";display: block;clear: both;zoom: 1;overflow: hidden;}
		.em-widget-send-wrapper{position:absolute;height:140px;width:100%;bottom:0;background:#fff;-webkit-backface-visibility: hidden;}
		.toolbar{padding:7px 0 5px;position:relative;overflow:hidden;}
		.toolbar .em-bar-emoji{background:url(/Static/Public/Shop/images/emoty_ico.png) center center no-repeat;background-size:30px;width:30px;height:30px;margin-left:5px;}
		.toolbar .em-widget-img{background:url(/Static/Public/Shop/images/chat_upload_img.png) center center no-repeat;background-size:30px;width:30px;height:30px;margin-left:15px;}
		.em-widget-send-wrapper i{float:left;top:5px;left:5px;position:relative;color:#4d4d4d;}
		.upload-img-container{position:absolute;left:55px;width:30px;height:30px;top:12px;opacity:0;}
		.em-widget-send-wrapper .em-widget-textarea{width:100%;height:70px;border:none;padding:10px;box-sizing:border-box;--moz-box-sizing:border-box;resize:none;}
		.em-widget-send{position:absolute;right:10px;bottom:10px;width:100px;border-radius:4px;height:30px;text-align:center;line-height:30px;color:#fff;cursor:pointer;}
		.em-widget-send.bg-color{background:#d1c1f9;}
		.em-widget-send.bg-color:hover{background:#967bdc;}
		.em-bar-emoji-wrapper {position: fixed;z-index: 1;bottom: 144px;left: 5px;background-color: #fff;padding: 0 0 0 10px;border-radius: 4px;box-shadow: 0 4px 5px rgba(0,0,0,.1);-webkit-transform: translateZ(0);-moz-transform: translateZ(0);transform: translateZ(0);}
		.em-bar-emoji-wrapper.hide{display:none;}
		.em-widget-left, .em-widget-right {float: left;display: block;text-decoration: none;width: 98%;text-align: left;margin: 8px 0;}
		.em-widget-msg-wrapper {position: relative;}
		.em-widget-right .em-widget-msg-wrapper {float: right;margin-right: 22px;}
		.icon-corner-right {z-index: 1;position: absolute;top: 5px;right: -34px;width: 30px;height: 30px;color: #e3ecfc;text-shadow: 1px 0 0 #c3cbd9;}
		.em-widget-msg-container {display: inline-block;border-radius: 6px;max-width: 190px;padding: 10px;min-width: 20px;min-height: 20px;vertical-align: middle;text-align: left;font-size: 13px;border: 1px solid #e5e5e5;}
		.em-widget-right .em-widget-msg-container {background-color: #967bdc;color:#fff;float: right;border: 1px solid #967bdc;}
		.em-widget-msg-container img {border-radius: 2px;max-width: 190px;word-wrap: break-word;vertical-align: middle;}
		.em-widget-msg-container .emoji {width: 24px;height: 24px;}
		.icon-corner-left {z-index: 1;position: absolute;top: 5px;left: -33px;width: 30px;height: 30px;color: #fff;text-shadow: 1px 0 0 #c3cbd9;}
		.em-widget-left .em-widget-msg-container {background-color: #fff;float: left;border: 1px solid #c3cbd9;}
		.em-widget-left .em-widget-msg-wrapper {float: left;margin-left: 37px;}
		.icon-corner-left img, .icon-corner-right img {width: 100%;height: 100%;border-radius: 15px;}
		.em-widget-chat .em-widget-date, .em-widget-chat .em-widget-event {width: 100%;float: left;margin: 8px 0;text-align: center;color: #fff;font-size: 12px;}
		.em-widget-chat .em-widget-date>span, .em-widget-chat .em-widget-event>span {background-color: #c9ced3;border-radius: 4px;padding: 1px 3px;}
	</style>
	<script type="text/javascript" src="/Static/Public/ImApi/js/webim.config.js"></script>
    <script type="text/javascript" src="/Static/Public/ImApi/js/strophe-1.2.8.min.js"></script>
    <script type="text/javascript" src="/Static/Public/ImApi/js/websdk-1.4.10.js"></script>
    <script type="text/javascript" src="/Static/Public/Common/js/json2.js"></script>
	<script type="text/javascript" src="/Static/Public/Shop/js/chat.js"></script>

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
	    <h1 class="pagetitle"><?php echo (L("_PC_MESSAGE_CONTACT_USER_")); ?></h1>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<div class="chatWindow_box">
			<div class="lf">
				<h1 class="ctitle"><?php echo (L("_PC_MESSAGE_CONTACTS_")); ?></h1>
				<ul class="diyScroll" id="Jcontact">
					
				</ul>
			</div>
			<div class="rt">
				<div class="onechat">
					<h1 class="talker" id="Talker"></h1>
					<div id="em-kefu-webim-chat" class="em-widget-wrapper">
						<div id="em-widgetBody" class="em-widgetBody-wrapper diyScroll">
						</div>
						<div class="em-bar-emoji-wrapper e-face hide" style="position:absolute;width:400px;overflow-y:auto;">
				            <ul class=em-bar-emoji-container id=emotionUl>
				                <li></li>
				            </ul>
				        </div>
						<div id="em-widgetSend" class="em-widget-send-wrapper">
							<div class="toolbar">
								<i class="em-bar-emoji icon-face e-face fg-hover-color" title="表情" id="selectEmoji"></i>
								<i class="em-widget-img icon-picture fg-hover-color" title="图片"></i>
							</div>
							<!-- <input id="em-widget-img-input" type="file" class="upload-img-container" accept="image/*"> -->
							<input id="em-widget-img-input" type="file" class="upload-img-container">
							<textarea class="em-widget-textarea" spellcheck="false" placeholder="点击输入内容..." id="talkInputId"></textarea>
							<span class="em-widget-send bg-color" data-type="text" id="sendPrivateText">发送</span>
						</div>
					</div>
				</div>
			</div>
		</div>
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
        
<script type="text/html" id="contactlist_tpl">
<%for(var i=0; i<list.length; i++){%>
	<%var data = list[i];%>
	<li data-id="wedo<%=data['id']%>" data-nickname="<%=data['nickname']%>">
		<div class="headImgbox">
			<img src="<%=data['headimgurl']%>">
		</div>
		<span class="name"><%=data['nickname']%></span>
	</li>
<%}%>
</script>
<script type="text/html" id="userChat_tpl">
<%for(var i=list.length - 1; i>=0; i--){%>
	<%var data = list[i],from_id='wedo'+data['from_id'];%>
	<%if(fromId == from_id){%>
	<div class="em-widget-left">
		<div class="em-widget-msg-wrapper">
			<i class="icon-corner-left">
				<img src="<%=data['fromUser']['headimgurl']%>">
			</i>
			<%if( data['content']['type'] == '1' ){%>
				<div class="em-widget-msg-container em-widget-msg-txt"><%=data['content']['content']%></div>
			<%}else if ( data['content']['type'] == '2' ) {%>
				<div class="em-widget-msg-container em-widget-msg-img">
					<a href="javascript:;">
						<img class="em-widget-imgview" src="<%=data['content']['content']%>">
					</a>
				</div>
			<%}else if ( data['content']['type'] == '3' ) {%>
				<div class="em-widget-msg-container em-widget-msg-txt"><%=data['content']['content']%></div>
			<%}else{%>
				<div class="em-widget-msg-container em-widget-msg-txt">
					
				</div>
			<%}%>
		</div>
	</div>
	<%}else{%>
		<div class="em-widget-right">
			<div class="em-widget-msg-wrapper">
				<i class="icon-corner-right">
					<img src="<%=data['curUser']['headimgurl']%>">
				</i>
				<%if( data['content']['type'] == '1' ){%>
					<div class="em-widget-msg-container em-widget-msg-txt"><%=data['content']['content']%></div>
					<!-- <div class="emcaoTips">
						<div class="triangle-up"></div>
						<div class="operation">
							<span class="Del">删除</span> <span>|</span> <span class="Copy">复制</span>
						</div>
					</div> -->
				<%}else if ( data['content']['type'] == '2' ) {%>
					<div class="em-widget-msg-container em-widget-msg-img">
						<a href="javascript:;">
							<img class="em-widget-imgview" src="<%=data['content']['content']%>">
						</a>
					</div>
				<%}else if ( data['content']['type'] == '3' ) {%>
					<div class="em-widget-msg-container em-widget-msg-txt"><%=data['content']['content']%></div>
				<%}else{%>
					<div class="em-widget-msg-container em-widget-msg-txt">
						
					</div>
				<%}%>
			</div>
		</div>
	<%}%>
<%}%>
</script>
<script type="text/html" id="message_tpl">
<%for(var i=0; i<list.length; i++){%>
	<%var data = list[i];%>
	<div class="em-widget-chat" id="chat_wedo<%=data['id']%>" style="display: none;">
								
	</div>
<%}%>	
</script>
<script type="text/javascript">
	var bt = baidu.template,to,fromId;
	var page = 1;
	function addressList() {
		$.ajax({
			url: '<?php echo U('Chat/addressList');?>',
			type: 'POST',
			dataType: 'json',
			data: {}
		})
		.done(function(returnData) {
			if ( returnData['data']['list'].length > 0 )  {
				var html = bt('contactlist_tpl', returnData['data']);
				var html2 = bt('message_tpl', returnData['data']);
				$('#Jcontact').html(html);
				$('#em-widgetBody').html(html2);
			}
		});
	}
	addressList();

	// 环信通信云
    // if (_isLogin) {
        var username = "wedo<?php echo (session('userId')); ?>";
        var password = "wedo<?php echo (session('userId')); ?>";
        chatLogin(username, password);
    // }//登录
    var userInfo = <?php echo json_encode($_SESSION['userInfo']);?>;
    
    //创建本地聊天窗
    var systemName = "<?php echo C('systemName');?>";
    $('#Talker').text(systemName);

	$('#selectEmoji').click(function(){
        showEmotionDialog();
    });//表情

    $('#sendPrivateText').click(function(){
        $("#emotionUl").parent().addClass('hide');
        sendPrivateText(userInfo, to);
    })//发送消息

    $('#em-widget-img-input').on('change',function(){
        sendPrivateImg(userInfo, to);
    })//发送图片

    // 单聊贴图发送
    document.addEventListener('paste', function (e) {
    	var user = {};
    	user['id'] = userInfo['id'];
	    user['nickname'] = userInfo['nickname'];
	    user['headimgurl'] = userInfo['headimgurl'];

        if (e.clipboardData && e.clipboardData.types) {
            if (e.clipboardData.items.length > 0) {
                if (/^image\/\w+$/.test(e.clipboardData.items[0].type)) {
                    var blob = e.clipboardData.items[0].getAsFile();
                    var url = window.URL.createObjectURL(blob);
                    var id = conn.getUniqueId();             // 生成本地消息id
                    var msg = new WebIM.message('img', id);  // 创建图片消息
                    msg.set({
                        apiUrl: WebIM.config.apiURL,
                        file: {data: blob, url: url},
                        to: to,                      // 接收消息对象
                        roomType: false,
                        chatType: 'singleChat',
                        action : 'action',                     //用户自定义，cmd消息必填
        				ext :user,    //用户自扩展的消息内容（群聊用法相同）
                        onFileUploadError: function (error) {
                            console.log('Error');
                        },
                        onFileUploadComplete: function (data) {
                            console.log('Complete');
                        },
                        success: function (id) {
                            var _url = url;
                            var html = "<div class='em-widget-right'><div class='em-widget-msg-wrapper'><i class='icon-corner-right'><img src="+user.headimgurl+"></i><div class='em-widget-msg-container em-widget-msg-img'><a href='javascript:;''><img class='em-widget-imgview' src='"+_url+"'></a></div></div></div>";
                            $('.em-widget-chat').append(html);
                            console.log('Success');
                        }
                    });
                    conn.send(msg.body);
                }
            }
        }
    });

   	// 选择联系人
	$("#Jcontact").on("click",'li',function(){
		to = $(this).data('id');
		fromId = $(this).data('id');
		$("#Jcontact").find('li').removeClass('on');
		$(this).addClass('on');
		$('#Talker').html($(this).data('nickname'));
		$('#em-widgetBody .em-widget-chat').hide();
		$('#chat_'+fromId).show();
		loadUserChat();
	})

	function loadUserChat() {
		to_id = fromId.replace('wedo', '');
		$.ajax({
			url: '<?php echo U('Chat/userChat');?>',
			type: 'POST',
			dataType: 'json',
			data: {page:page, to_id:to_id}
		})
		.done(function(returnData) {
			if ( returnData['data']['list'].length > 0 ) {
				returnData['data']['fromId'] = fromId;
				var html = bt('userChat_tpl', returnData['data']);
				$('#chat_'+fromId).html(html);
			} else {
				
			}
		});
	}
</script>

    </body>
</html>