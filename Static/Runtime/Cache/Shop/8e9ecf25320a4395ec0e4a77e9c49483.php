<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo C('systemName');?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="Keywords" content="" />
	<meta name="Description" content="" />
	<meta name="viewport" content="width = 320,initial-scale=1,user-scalable=no" />
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<meta content="telephone=no" name="format-detection" />
	<!-- css -->
	<link href="/Static/Public/Wechat/css/base.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="/Static/Public/Wechat/css/wedoStyle.css">
	<link rel="stylesheet" type="text/css" href="/Static/Public/Wechat/css/swiper.css">
	
</head>

<body>

	<div class="content" id="content">
		<header class="head">
			<span class="back"></span>
			<h1 class="y-confirm-order-h1"><?php echo (L("_WAP_ORDER_CONTACT_MERCHANT_")); ?></h1>
			<a href="tel:12345678"><span class="call"></span></a>
		</header>
		<div class="main">
			<div class="imchat_wrap">    
			    <div id=em-kefu-webim-chat class="em-widget-wrapper">
			        <div id=em-widgetBody class=em-widgetBody-wrapper>			            
			            <div class=em-widget-chat>
			                
			            </div>
			        </div>			        
			        <div id=em-widgetSend class=em-widget-send-wrapper>
			            <div class=toolbar>
			           		<i class="em-add-open icon-face e-face fg-hover-color" title=功能 id="openFun"></i>
			           		<i class="em-address-ye icon-face e-face fg-hover-color" data-type="text" title=发送 id="sendPrivateText"></i>			       
			                <div contenteditable="true" class=em-widget-div spellcheck=false id="talkInputId"></div>
			            </div>
			            <div class="open-upload-box" id="OuploadImg">
			            	<div class="o_up_main">
			            		<div class="imgbox">
			            			<img src="/Static/Public/Wechat/images/chat_upload_img.png">
			            			<input id=em-widget-img-input type=file class=upload-img-container accept=image/* > 
			            		</div>
			            		<p>图片</p>
			            	</div>
			            </div>
			        </div>			        			    
			    </div>			   
			</div>
		</div>
	</div>






     <div class="loading" id="Jloading"><img src="/Static/Public/Wechat/images/loading.gif"></div>
	<!-- 提示信息 -->
	<div class="mengban">
		<!-- 判断提示只有确定 -->
		<div class="msg-main-box2 JmsgBox-confirm">
			<div class="detail-wrap">
				<p class="detail-txt"></p>
			</div>
			<div class="btn">
				<a href="javascript:;" class="tips-btn1 JsureBtn">确定</a>
			</div>

		</div>

		<!--    判断提示 -->
		<div class="msg-main-box2 JmsgBox2">
			<div class="detail-wrap">
				<p class="detail-txt"></p>
			</div>
			<div class="btn">
				<a href="javascript:;" class="tips-btn1 JsureBtn">确定</a>
				<a href="javascript:;" class="tips-btn1 JcancelBtn">取消</a>
			</div>

		</div>

		<!-- 自动消失 -->
		<div class="automsg-main-box JmsgBox1" style="display: none;">
			<div class="tit"><?php echo (L("_COMMON_NOTICE_TIPS_")); ?></div>
			<p class="detail-txt">加入购物车成功</p>
		</div>
	</div>


<!-- 正式版本vue -->
<!-- <script type="text/javascript" src="/Static/Public/Wechat/js/vue.min.js"></script> -->
<!-- 开发版本 -->
<script type="text/javascript" src="/Static/Public/Wechat/js/vue.js"></script>
<script type="text/javascript" src="/Static/Public/Wechat/js/common.js"></script>
<script type="text/javascript" src="/Static/Public/Wechat/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript">
	//判断是安卓还是IOS
	var ua_phone = navigator.userAgent.toLowerCase();
	var UA_phoneType = '';	
	if (/iphone|ipad|ipod/.test(ua_phone)) {
		//msgbox("iphone");	
		UA_phoneType = 0;	
	} else if (/android/.test(ua_phone)) {
		UA_phoneType = 1;
	}

	/**              
	 * 时间戳转换日期              
	 * @param <int> unixTime    待时间戳(秒)            
	 */
	Vue.filter('time',function(value, type="yyyy-MM-dd hh:mm:ss") {
		var newDate = new Date();
		newDate.setTime(value * 1000);
		Date.prototype.format = function(format) {
			var date = {
			    "M+": this.getMonth() + 1,
			    "d+": this.getDate(),
			    "h+": this.getHours(),
			    "m+": this.getMinutes(),
			    "s+": this.getSeconds(),
			    "q+": Math.floor((this.getMonth() + 3) / 3),
			    "S+": this.getMilliseconds()
			};
			if (/(y+)/i.test(format)) {
			    format = format.replace(RegExp.$1, (this.getFullYear() + '').substr(4 - RegExp.$1.length));
			}
			for (var k in date) {
			    if (new RegExp("(" + k + ")").test(format)) {
			           format = format.replace(RegExp.$1, RegExp.$1.length == 1
			                  ? date[k] : ("00" + date[k]).substr(("" + date[k]).length));
			    }
			}
			return format;
		}
		return newDate.format(type);
	})
</script>

<script type="text/html" id="userChat_tpl">
<%for(var i=list.length - 1; i>=0; i--){%>
	<%var data = list[i];%>
	<%if(fromId == data['from_id']){%>
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
					<div class="emcaoTips">
						<div class="triangle-up"></div>
						<div class="operation">
							<span class="Del">删除</span> <span>|</span> <span class="Copy">复制</span>
						</div>
					</div>
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
<script type="text/javascript" src="/Static/Public/ImApi/js/webim.config.js"></script>
<script type="text/javascript" src="/Static/Public/ImApi/js/strophe-1.2.8.min.js"></script>
<script type="text/javascript" src="/Static/Public/ImApi/js/websdk-1.4.10.js"></script>
<script type="text/javascript" src="/Static/Public/Common/js/json2.js"></script>
<script type="text/javascript" src="/Static/Public/Wechat/js/chat.js"></script>
<script type="text/javascript" src="/Static/Public/Shop/js/baiduTemplate.js"></script>
<script type="text/javascript">
	var vm = new Vue({
		el : "#content",
		data : {},
		created(){
			$('#Jloading').fadeOut();
		}
	})
	var to = "wedo<?php echo ($_GET['id']); ?>";

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
    var talker = "<li><div class='headImgbox'><img></div><span class='name'>"+systemName+"</span></li>";
	$("#Jcontact").append(talker);

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

    // 功能
    $("#openFun").bind('click',function(e){
    	$("#OuploadImg").slideDown(200);
    	stopPropagation(e);
    })

    function stopPropagation(e) { 
		if (e.stopPropagation){ 
			e.stopPropagation(); 
		}else{ 
			e.cancelBubble = true;
		} 
	} 

	$(document).bind('click',function(){ 
		$("#OuploadImg").slideUp(200);
		$('.emcaoTips').fadeOut(150);
	})

	// 复制删除消息
	var timer;
	$('#em-widgetBody').on('touchstart','.em-widget-msg-wrapper',function(){
		var el = $(this);
		timer = setTimeout(function(){
			el.find('.emcaoTips').fadeIn(150);
		},1000)
	})
	$('#em-widgetBody').on('touchend','.em-widget-msg-wrapper',function(){
		clearTimeout(timer);
	})
	// 删除
	$("#em-widgetBody").on('click','.Del',function(){
		$(this).parents('.em-widget-right').remove();
	})
	//复制
	function Copy(str){
	    var save = function(e){
	        e.clipboardData.setData('text/plain', str);
	        e.preventDefault();
	    }
	    document.addEventListener('copy', save);
	    document.execCommand('copy');
	    document.removeEventListener('copy',save);
	    alert('复制成功！');
	}
	$("#em-widgetBody").on('click','.Copy',function(){
		Copy($(this).parents('.emcaoTips').siblings('.em-widget-msg-txt').text());
	})

	var page = 1;
	var fromId = "<?php echo ($_GET['id']); ?>";
	var bt = baidu.template;
	function loadUserChat() {
		$.ajax({
			url: '<?php echo U('Chat/userChat');?>',
			type: 'POST',
			dataType: 'json',
			data: {page:page, to_id:fromId}
		})
		.done(function(returnData) {
			var html = '';
			if ( returnData['data']['list'].length > 0 ) {
				returnData['data']['fromId'] = fromId;
				html = bt('userChat_tpl', returnData['data']);
			}
			$('.em-widget-chat').html(html);
		});
	}
	loadUserChat();
	$('.y-confirm-order-h1').click(function(){
		var userInfo2 = []
		sendPrivateText(userInfo2, to);
	})
	//重置本地信息的total
	function resetTotal(fromId){
		fromId = 'wedo' + fromId;
		var messageList =  localStorage['messageList'];
		if ( messageList ) {
			var messageList = JSON.parse(messageList);
			for(var i=0; i<messageList.length; i++){
				if( messageList[i].id == fromId ){
					messageList[i].total = 0;
					break;
				}
			}
			localStorage['messageList'] = JSON.stringify(messageList);
		}
	}
	resetTotal();
</script>


	<script type="text/javascript">
		/*有确认按钮*/
		function msgbox(txt, callback) {
			var mengban = $('.mengban');
			var tipBox2 = $('.JmsgBox-confirm');
			$('.JmsgBox-confirm .detail-txt').html(txt);
			mengban.show();
			tipBox2.show();
			$('.JmsgBox-confirm .JsureBtn').click(function() {
				mengban.hide();
				tipBox2.hide();
				if (callback) {
					callback();
				}
			});
		}

		/*有取消和确认按钮*/
		function msgbox2(txt, callback) {
			var mengban = $('.mengban');
			var tipBox2 = $('.JmsgBox2');
			$('.JmsgBox2 .detail-txt').html(txt);
			mengban.show();
			tipBox2.show();
			var ctr = 1;
			$('.JmsgBox2 .JsureBtn').click(function() {
				mengban.hide();
				tipBox2.hide();
				if (callback && ctr == 1) {
					ctr = 0;
					callback();
				}
			});
			$('.JcancelBtn').click(function() {
				mengban.hide();
				tipBox2.hide();
				ctr = 0;
			});
		}

		/*自动消失*/
		function automsgbox(txt, callback) {
			var mengban = $('.mengban');
			var tipBox1 = $('.automsg-main-box');
			$('.automsg-main-box .detail-txt').html(txt);
			mengban.show();
			tipBox1.show();
			var t = setTimeout(function(){
				mengban.hide();
				tipBox1.hide();
				if (callback) {
					callback();
				}
			},2000);
		}

		function isScroll(bottomCall){
			var startX = 0, startY = 0;
		    function touchSatrtFunc(evt) {
			      try
			      {

			          var touch = evt.touches[0]; //获取第一个触点  
			          var x = Number(touch.clientX); //页面触点X坐标  
			          var y = Number(touch.clientY); //页面触点Y坐标  
			          //记录触点初始位置  
			          startX = x;
			          startY = y;

			      } catch (e) {
			          alert( e.message);
			      }
		    }
	    	//touchstart事件  
	        document.body.addEventListener('touchstart', touchSatrtFunc, false);
	        document.body.addEventListener('touchmove',scrlllfunction,false);
	        function scrlllfunction (ev){
		        var _point = ev.touches[0];
		         // window滚动
		        var _top = document.body.scrollTop;
		         // 什么时候到底部
		        var bottomAdr = document.body.scrollHeight - window.innerHeight;
		          //判断是否滚到底部加载更多
		          if(_top >= bottomAdr-10 && _point.clientY < startY){
		              if(bottomCall){
		                bottomCall();
		              }
		          }
		          // 到达顶端
		          if (_top === 0) {
		              // 阻止向下滑动
		              if (_point.clientY > startY) {
		                  ev.preventDefault();
		              } else {
		                  // 阻止冒泡
		                  // 正常执行
		                  ev.stopPropagation();
		              }
		          } else if (_top == bottomAdr) {
		              // 到达底部
		              // 阻止向上滑动
		              if (_point.clientY < startY) {
		                  ev.preventDefault();
		              } else {
		                  // 阻止冒泡
		                  // 正常执行
		                  ev.stopPropagation();
		              }
		          } else if (_top > 0 && _top < bottomAdr) {
		              ev.stopPropagation();
		          } else {
		              ev.preventDefault();
		          }
	        }
		}

		var is_interface = '<?php echo session("isInterfase");?>';
		$('.back').click(function() {
			console.log(111);
			if ( is_interface ) {
				window.location.href="mitchell://back";
			} else {
				history.back();
			}
		});
	</script>

</body>
</html>