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
		<div class="personal_center">
			<div class="p_cen_top">
				<div class="headbox">
					<div class="h_img">
						<a href="<?php echo U('User/userInfo');?>">
							<img :src="user['headimgurl']">
						</a>
					</div>
					<a href="<?php echo U('User/userInfo');?>">
						<p>{{user['nickname']}}</p>
					</a>
				</div>
				<em class="qrcode" @click="showAllQrcode"></em>
				<div class="selQrcode">
					<p @click="showShopQrcode"><?php echo (L("_COMMON_STORE_QR_CODE_")); ?></p>
					<p @click="showMoneyQrcode"><?php echo (L("_COMMON_QR_CODE_FOR_COLLECTION_")); ?></p>
				</div>
			</div>
			<div class="p_cen_m">
				<ul>
					<li class="poli">
						<em class="cico ico1"></em>
						<a href="<?php echo U('Agent/settlementManagement');?>" style="overflow:hidden;display:inline;">
							<span class="s1"><?php echo (L("_WAP_AGENT_SETTLEMENTMANAGEMENT_")); ?></span>
						</a>
						<a href="<?php echo U('Agent/settlementManagement');?>" style="overflow:hidden;float:right;">
							<span class="s2 red">{{user['money']}}</span>
						</a>
						<em class="ei"></em>
					</li>
					<li class="poli">
						<em class="cico ico2"></em>
						<a href="<?php echo U('Point/index');?>" style="overflow:hidden;display:inline;">
							<span class="s1"><?php echo (L("_WAP_AGENT_MYPOINTS_")); ?></span>
						</a>
						<a href="<?php echo U('Point/pointGoods');?>" style="overflow:hidden;float:right;">
							<span class="s2"><?php echo (L("_WAP_AGENT_MALL_")); ?></span>
						</a>
						<em class="ei"></em>
					</li>
					<li>
						<a class="ca" href="<?php echo U('GoodsCheck/goodsToAuth');?>">
							<em class="cico ico3"></em>
							<span class="s1"><?php echo (L("_WAP_AGENT_VERIFIED_")); ?></span>
							<em class="ei"></em>
						</a>
					</li>
					<li>
						<a class="ca" href="<?php echo U('GoodsCheck/goodsToTop');?>">
							<em class="cico ico4"></em>
							<span class="s1"><?php echo (L("_WAP_AGENT_BUMPTOTOP_")); ?></span>
							<em class="ei"></em>
						</a>
					</li>
					<li>
						<a class="ca" href="<?php echo U('Bidding/biddingIndexGoods');?>">
							<em class="cico ico5"></em>
							<span class="s1"><?php echo (L("_WAP_AGENT_INDEXBIDDING_")); ?></span>
							<em class="ei"></em>
						</a>
					</li>
					<li>
						<a class="ca" href="<?php echo U('Bidding/biddingFavorableGoods');?>">
							<em class="cico ico6"></em>
							<span class="s1"><?php echo (L("_WAP_AGENT_PROMOTIONALBIDDING_")); ?></span>
							<em class="ei"></em>
						</a>
					</li>
					<li>
						<a class="ca" href="<?php echo U('Bidding/biddingRecord', array('bidding_type'=> '2', 'tab'=> '2'));?>">
							<em class="cico ico7"></em>
							<span class="s1"><?php echo (L("_WAP_AGENT_MERCHANTBIDDING_")); ?></span>
							<em class="ei"></em>
						</a>
					</li>
					<li>
						<a class="ca" href="<?php echo U('Bidding/biddingBanner');?>">
							<em class="cico ico8"></em>
							<span class="s1"><?php echo (L("_WAP_AGENT_ADVERTISINGBIDDING_")); ?></span>
							<em class="ei"></em>
						</a>
					</li>
					<li class="set">
						<a class="ca" href="<?php echo U('Agent/setting');?>">
							<em class="cico ico9"></em>
							<span class="s1"><?php echo (L("_WAP_AGENT_SETTING_")); ?></span>
							<em class="ei"></em>
						</a>
					</li>
				</ul>
			</div>
		</div>
		<!-- 弹窗 -->
		<div class="mask"></div>
		<div class="mqrcode">
			<span @click="processQrcode(0)"></span>
			<div class="qrimg">
				<div class="imgbox">
					<img :src="qrcode">
				</div>
			</div>
			<p>{{user['agent_name']}}</p>
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

<script type="text/javascript">
	var vm = new Vue({
		el : "#content",
		data : {
			user : {},
			qrcodeShow : 0,
			qrcode : '',
			agentQRcode : '',
			agentReceiptQRcode : '',
			historyList : [],
			count : 0,
		},
		created(){
			this.loadUser();
			this.loadMessage();
			window.initMessage = this.initMessage;
		},
		mounted(){
			$('#Jloading').fadeOut();
		},
		methods : {
			loadUser : function() {
				var that = this;
				$.ajax({
					url: '<?php echo U('Agent/agentCenter');?>',
					type: 'POST',
					dataType: 'json',
					data: {}
				})
				.done(function(returnData) {
					if( returnData['status'] == '200000' ){
						that.user = returnData['data']['list'];
						that.agentQRcode = returnData['data']['agentQRcode'];
						that.agentReceiptQRcode = returnData['data']['agentReceiptQRcode'];
						that.$nextTick(function(){
							this.initMessage();
						});
					}
				});
			},
			showAllQrcode() {
				if ( this.qrcodeShow == 0 ) {
					this.showSelectQrcode(1);
					this.qrcodeShow = 1;
				} else {
					this.showSelectQrcode(0);
					this.qrcodeShow = 0;
				}
			},
			showSelectQrcode(type) {
				( type == 1 ) ? $('.selQrcode').slideDown(200) : $('.selQrcode').slideUp(200);
			},
			showShopQrcode() {
				$(event.target).addClass('on');
				$(event.target).siblings().removeClass('on');
				this.qrcode = this.agentQRcode;
				this.showSelectQrcode(0);
				this.processQrcode(1);
			},
			showMoneyQrcode() {
				$(event.target).addClass('on');
				$(event.target).siblings().removeClass('on');
				this.qrcode = this.agentReceiptQRcode;
				this.showSelectQrcode(0);
				this.processQrcode(1);
			},
			processQrcode(type) {
				if ( type == 1 ) {
					$('.mask').fadeIn();
					$('.mqrcode').fadeIn();
				} else {
					$('.mask').fadeOut();
					$('.mqrcode').fadeOut();
					this.qrcodeShow = 0;
				}
			},
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
						that.count = parseInt(returnData['data']['count']);
						that.$nextTick(function(){
							that.initMessage();
						});
					}
				});
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
			labelCount(){
				if ( this.count > 0 ) {
					$('.reddot').text(this.count);
				} else {
					$('.reddot').remove();
				}
			}
		}
	});

	$(document).click(function(e){ 
		e = window.event || e; 
		obj = $(e.srcElement || e.target);
	 	if ($(obj).is(".mask")) { 
	 	   	vm.processQrcode(0);
	  	}
	});
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