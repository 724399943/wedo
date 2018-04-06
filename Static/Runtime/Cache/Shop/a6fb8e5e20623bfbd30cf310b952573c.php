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
	
	<style type="text/css">
	.dot{width:20px;height:20px;text-align:center;color:#fff;border-radius:50%;position:absolute;top:-5px;right:-5px;background:#ff3838;font-size:12px;transform:scale(0.75);-webkit-transform:scale(0.75);}

	</style>

</head>

<body>

	<div class="content" id="content">
		<header class="head">
			<span class="back"></span>
			<h1 class="y-confirm-order-h1"><?php echo (L("_COMMON_FOOTER_NOTIFICATION_")); ?></h1>
			<a href="<?php echo U('Chat/addressList');?>" class="mail"><?php echo (L("_PC_MESSAGE_CONTACTS_")); ?></a>
		</header>
		<div class="main">
			<div class="news_wrap">
				<div class="system_news" @click="jumpToSystem">
					<div class="imgbox">
						<img src="/Static/Public/Wechat/images/system_ico.png">
						<em class="dot" v-if="systemCount > 0">{{systemCount}}</em>
					</div>
					<div class="s_new_m">
						<p><?php echo (L("_PC_MESSAGE_SYSTEM_")); ?></p>
						<div class="nmt"><span>{{message['title']}}</span><em v-if="message.add_time != null">{{message['add_time']|time("yyyy-MM-dd")}}</em></div>
					</div>
				</div>
				<div class="chat_news">
					<ul>
						<li v-for="(item, index) in historyList">
							<a @click="jumpToConsultion(item['id'])">
							<!-- <a :href="'<?php echo U('Message/consultation');?>?id='+item['id']"> -->
								<div class="cnews">
									<div class="imgbox">
										<img :src="item['headimgurl']">
										<em class="dot" v-if="item.count>0">{{item.count}}</em>
									</div>
									<div class="s_new_m">
										<p class="stt"><span>{{item['nickname']}}</span><em>{{item['add_time']|time("yyyy-MM-dd")}}</em></p>
										<p class="nmt" v-if="item['content'] && item['content']['type'] == '1'">{{item['content']['content']}}</p>
										<p class="nmt" v-else-if="item['content'] && item['content']['type'] == '2'">[图片]</p>
									</div>
								</div>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>



	<!-- 底部固定菜单 -->
<footer class="y-ycar-foot">
	<a href="<?php echo U('Agent/goodsCenter');?>" <?php if(CONTROLLER_NAME == 'Agent' AND ACTION_NAME == 'goodsCenter'): ?>class="on"<?php endif; ?>>
		<em class="icon"><i></i><?php echo (L("_COMMON_FOOTER_PRODUCTS_")); ?></em>
	</a>
	<a href="<?php echo U('Order/orderList');?>" <?php if(CONTROLLER_NAME == 'Order'): ?>class="on"<?php endif; ?>>
		<em class="icon"><i></i><?php echo (L("_COMMON_FOOTER_ORDER_")); ?></em>
	</a>
	<a href="<?php echo U('Message/index');?>" <?php if(CONTROLLER_NAME == 'Message'): ?>class="on"<?php endif; ?>>
		<em class="icon"><i></i><?php echo (L("_COMMON_FOOTER_NOTIFICATION_")); ?></em>
		<p class="reddot">1</p>
	</a>
	<a href="<?php echo U('Agent/agentCenter');?>" <?php if(CONTROLLER_NAME == 'Agent' AND ACTION_NAME == 'agentCenter'): ?>class="on"<?php endif; ?>>
		<em class="icon"><i></i><?php echo (L("_COMMON_FOOTER_MYPROFILE_")); ?></em>
	</a>
</footer>



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

<script type="text/javascript" src="/Static/Public/ImApi/js/webim.config.js"></script>
<script type="text/javascript" src="/Static/Public/ImApi/js/strophe-1.2.8.min.js"></script>
<script type="text/javascript" src="/Static/Public/ImApi/js/websdk-1.4.10.js"></script>
<script type="text/javascript" src="/Static/Public/Common/js/json2.js"></script>
<script type="text/javascript" src="/Static/Public/Wechat/js/chat.js"></script>
<script type="text/javascript">
	var vm = new Vue({
		el : "#content",
		data : {
			// messageData2 : window.messageInfo,
			systemCount : 0,
			message : {},
			historyList : [],
			page : 1,
			goscroll : 1,
			nothing : 1,
			count : 0,
		},
		created(){
			this.loadMessage();
			window.initMessage = this.initMessage;
			this.loadUserChat();
			this.loadMore();
			this.getSystemMsgCount();
		},
		watch : {
			// 'messageData2' : 'initMessage',
		},
		mounted(){
		   $('#Jloading').fadeOut();
		},
		methods : {
			loadMessage : function() {
				var that = this;
				$.ajax({
					url: '<?php echo U('Message/index');?>',
					type: 'POST',
					dataType: 'json',
					data: {}
				})
				.done(function(returnData) {
					if( returnData['status'] == '200000' ){
						that.message = returnData['data']['system'];
						that.count = parseInt(returnData['data']['count']);
						var username = "wedo<?php echo (session('userId')); ?>";
						var password = "wedo<?php echo (session('userId')); ?>";
						chatLogin(username, password);
						that.$nextTick(function(){
							this.initMessage();
						});
					}
				});
			},
			getSystemMsgCount(){
				var that = this;
				$.ajax({
					url: '<?php echo U('Message/systemMessage');?>',
					type: 'POST',
					dataType: 'json',
					data: {
						page : that.page,
						status : '1'
					}
				})
				.done(function(returnData) {
					var list = returnData['data']['list'];
					for(var i=0; i<list.length; i++){
						if( list[i].is_read === 0)
							++that.systemCount;
					}
				});
			},
			loadUserChat() {
				var that = this;
				$.ajax({
					url: '<?php echo U('Chat/chatHistory');?>',
					type: 'POST',
					dataType: 'json',
					data: {page:that.page}
				})
				.done(function(returnData) {
					if(returnData['data']['list'].length>0){
			        	if(that.historyList.length == 0){
			        		that.historyList = returnData['data']['list'];
			        	}else{
			        		that.historyList = that.historyList.concat(returnData['data']['list']);
			        	}
		        		vm.goscroll = 1;
		        		vm.nothing = 0;
		        	}else{
		        		vm.goscroll = 0;
		        		if( that.historyList.length == 0 ){
							vm.nothing = 1;
						}
		        	}
				});
			},
			loadMore(){
		        document.addEventListener('scroll', function() {	
					var scrollTop = window.pageYOffset 
						|| document.documentElement.scrollTop 
						|| document.body.scrollTop 
						|| 0;
					if(scrollTop + window.innerHeight >= document.body.clientHeight){
						if(vm.goscroll){
							vm.page++;
							vm.goscroll=0;
							vm.loadUserChat();
						}
					}
		        })
		    },
			jumpToSystem() {
				window.location.href = '<?php echo U('Message/systemMessage');?>';
			},
			//处理信息显示
			initMessage(){
				var messageList = localStorage.messageList;
				if ( messageList ) {
					var messageList = JSON.parse(messageList);
					/****处理显示列表信息*****/
					for(var i=0; i<this.historyList.length; i++){
						for(var j=0; j<messageList.length; j++){
							var list_from = 'wedo' + this.historyList[i].id,
								messageData = messageList[j];
							if( list_from == messageData.id ){
								this.historyList[i].content = {type:messageData['type'],content:messageData['content']};
								this.historyList[i].count = messageData.total;
								this.historyList[i].add_time = messageData.time / 1000;
								this.count += parseInt(messageData.total);
								break;
							}
						}
					}
				}
				this.labelCount();
			},
			//重置本地信息的total
			resetTotal(fromId){
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
				this.initMessage();
			},
			jumpToConsultion(id){
				 // <a :href="'<?php echo U('Message/consultation');?>?id='+item['id']"> 
				 this.resetTotal(id);
				 window.location.href = '<?php echo U('Message/consultation');?>?id=' + id;
			},
			labelCount(){
				if ( this.count > 0 ) {
					$('.reddot').text(this.count);
				} else {
					$('.reddot').remove();
				}
			}
		}
	})
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