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
	
	<link rel="stylesheet" type="text/css" href="/Static/Public/Wechat/css/LCalendar.css">

</head>

<body>

	<div class="content" id="content">
		<header class="head">
			<span class="back"></span>
			<h1 class="y-confirm-order-h1" @click="change"><span><?php echo (L("_WAP_ORDER_ALLORDERS_")); ?></span><b></b></h1>
			<ul class="all_orderul" id="orderul">
				<li @click="selomsg(1)"><?php echo (L("_PC_POINT_TO_BE_DELIVERED_")); ?></li>
				<li @click="selomsg(2)"><?php echo (L("_PC_POINT_TO_BE_RECEIVED_")); ?></li>
				<li @click="selomsg(3)"><?php echo (L("_PC_POINT_TO_BE_REVIEW_")); ?></li>
				<li @click="selomsg(4)"><?php echo (L("_PC_POINT_IS_FINISHED_")); ?></li>
				<li @click="selomsg(5)"><?php echo (L("_PC_POINT_IS_CANCELED_")); ?></li>
			</ul>
		</header>
		<div class="main">
			<div class="order_main">
				<div class="order_nav">
					<div class="o_nav on" @click="seldeta">
						<span class="on" id="Jcalendar"><?php echo (L("_WAP_ORDER_ALLDATES_")); ?></span>
						<input id="Jcalendar" type="text" name="" value="" v-model="start_time">
						<input id="birthday" type="hidden" name="birthday" value="2007-11-08">
						<em></em>
					</div>
					<div class="o_nav">
						<span @click="selship"><?php echo (L("_WAP_ORDER_DELIVERYMETHODS_")); ?></span>
						<i></i>
						<ul class="onav_ul" id="shipul">
							<li @click="seldeship(0)"><?php echo (L("_COMMON_BY_DELIVERY_")); ?></li>
							<li @click="seldeship(1)"><?php echo (L("_COMMON_COLLECT_FROM_MERCHANT_")); ?></li>
						</ul>
					</div>
				</div>
				<div class="my_order_wrap">
			        <ul class="yy-myorder-list" id="Jtab-orderList">
				        <li class="clearfix" v-for="(item, index) in orderList" @click="seeOrder(item.order_sn)">
				        	<div class="order-tophead">
								<span class="ott-t"><?php echo (L("_WAP_ORDER_NUMBER_")); ?>:</span>
								<span class="ott-sn">{{item['order_sn']}}</span>
								<em class="ot-tips" v-if="item['status'] == '5'"><?php echo (L("_PC_POINT_IS_CANCELED_")); ?></em>
								<em class="ot-tips" v-else-if="item['status'] == '1'"><?php echo (L("_PC_POINT_TO_BE_DELIVERED_")); ?></em>
								<em class="ot-tips" v-else-if="item['status'] == '2'"><?php echo (L("_PC_POINT_TO_BE_RECEIVED_")); ?></em>
								<em class="ot-tips" v-else-if="item['status'] == '3'"><?php echo (L("_PC_POINT_TO_BE_REVIEW_")); ?></em>
								<em class="ot-tips" v-else-if="item['status'] == '4'"><?php echo (L("_PC_POINT_IS_FINISHED_")); ?></em>
							</div>
				        	<div class="y-order-pro">
					        	<a href="javascript:;" class="y-kein" v-for="(goods, goodsIndex) in item['goodsList']">
					        		<div class="y-imgbox">
							        	<div class="y-img">
								        	<div class="ab">
								        		<img :src="goods['goods_image']">
								        	</div>
							        	</div>
					        		</div>       		
						        	<div class="y-jnjcn">
							        	<div class="y_name_t">
							        		<p class="y-njcn">{{goods['goods_name']}}</p>
							        		<em>RM{{goods['unit_price']}}</em>
							        	</div>
							        	<div class="y-ws5d">
								        	<span>{{goods['attr_list']}}</span>
								        	<span class="num">x{{goods['goods_number']}}</span>
								        </div>
						        	</div>
					        	</a>
				        	</div>
				        	<p class="tt-count">					        		
				        		<?php echo (L("_WAP_ORDER_ATOTALOF_")); ?>{{item['goods_number']}}<?php echo (L("_WAP_ORDER_GOODS_")); ?> <?php echo (L("_WAP_ORDER_SUBTOTAL_")); ?>
					        	<em>RM{{item['total']}}</em>
				        	</p>
				        	<div class="btbox clearfix" v-if="item['status'] == '1'">
				        		<a href="javascript:;" class="c2" @click="delivery(item['order_sn'])"><?php echo (L("_WAP_ORDER_DELIVERED_")); ?></a>
				        	</div>
				        </li>
			        </ul>
		    	</div> 
	    	</div>
	    	<!-- 弹窗 -->
			<div class="omask"></div>
			<div class="navmask"></div>
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

<?php if($_COOKIE['think_language'] == 'zh-cn'): ?><script type="text/javascript" src="/Static/Public/Wechat/js/LCalendar.js"></script>
<?php else: ?>
	<script type="text/javascript" src="/Static/Public/Wechat/js/LCalendar_en.js"></script><?php endif; ?>
<script type="text/javascript">
	var vm = new Vue({
		el : "#content",
		data : {
			orderList : [],
			page : 1,
			goscroll : 1,
			order_type : 'all',
			obol : true,
			sbol : true,
			otype : 1,
			start_time : '',
			express_type : -1,
			historyList : [],
			count : 0,
		},
		created(){
			this.loadOrder();
			this.loadmore();
			this.loadMessage();
			window.initMessage = this.initMessage;
		},
		mounted(){
		   $('#Jloading').fadeOut();
		},
		methods : {
			loadOrder : function() {
				var that = this;
				$.ajax({
					url: '<?php echo U('Order/orderList');?>',
					type: 'POST',
					dataType: 'json',
					data: {
						order_type : that.order_type,
						user_type : 'saler',
						express_type : that.express_type,
						page : that.page
					}
				})
				.done(function(returnData) {
					if( returnData['data']['list'].length > 0 ){
						if( that.orderList.length == 0 ){
							that.orderList = returnData['data']['list'];
						}else{
							that.orderList = that.orderList.concat(returnData['data']['list']);
						}
						that.goscroll = 1;
					}else{
						that.goscroll = 0;
					}
				});
			},
			loadmore(){
				var that = this;
				document.addEventListener("scroll",function(){
					var scrollTop = window.pageYOffset 
						|| document.documentElement.scrollTop 
						|| document.body.scrollTop 
						|| 0;
					if(scrollTop + window.innerHeight >= document.body.clientHeight){
						if(that.goscroll){
							that.page++;
							that.goscroll = 0;
							that.loadOrder();
						}
					}
				})
			},
			delivery(order_sn) {
				$.ajax({
					url: '<?php echo U('Order/delivery');?>',
					type: 'POST',
					dataType: 'json',
					data: {order_sn: order_sn}
				})
				.done(function(returnData) {
					if ( returnData['status'] == '200000' ) {
						automsgbox('发货成功', function(){
							window.location.href = window.location.href;
						});
					} else {
						automsgbox(returnData['message']);
					}
				});
			},
			change : function(){
				var that = this;
				$('#orderul').slideToggle(200);
				$('#shipul').fadeOut();
				$('.navmask').fadeOut();
				vm.sbol = true;
				if(that.obol){
					$('.omask').show();
					that.obol = false;
				}else{
					$('.omask').fadeOut();
					that.obol = true;
				}
			},
			selomsg : function(index){
				var that = this;
				$(event.target).addClass('on');
				$(event.target).siblings().removeClass('on');
				$('#orderul').fadeOut();
				$('.omask').fadeOut();
				that.obol = true;
				that.otype = index;
				that.orderList = [];
				switch (that.otype) {
					case 1 : 
						that.order_type = 'toBeShipped';
						that.loadOrder();
						break;
					case 2 : 
						that.order_type = 'toBeReceived';
						that.loadOrder();
						break;
					case 3 : 
						that.order_type = 'toBeComment';
						that.loadOrder();
						break;
					case 4 :
						that.order_type = 'isFinish';
						that.loadOrder();
						break;
					case 5 : 
						that.order_type = 'isOutDate';
						that.loadOrder();
						break;
				}
			},
			selship : function(){
				var that = this;
				$('#shipul').slideToggle(200);
				if(that.sbol){
					$('.navmask').fadeIn();
					that.sbol = false;
				}else{
					$('.navmask').fadeOut();
					that.sbol = true;
				}
			},
			seldeta : function(){
				$('#shipul').fadeOut();
				$('.navmask').fadeOut();
				vm.sbol = true;
			},
			seldeship : function(index){
				var that = this;
				that.express_type = index;
				$(event.target).addClass('on');
				$(event.target).siblings().removeClass('on');
				that.orderList = [];
				that.loadOrder();
				$('#shipul').fadeOut();
				$('.navmask').fadeOut();
				vm.sbol = true;
			},
			seeOrder : function(index){
				window.location.href = "<?php echo U('Order/orderDetail');?>?order_sn="+index;
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
	})
	//初始化选择日期
	var calendar = new LCalendar();
    calendar.init({
        'trigger': '#Jcalendar', //标签id
        'type': 'date', //date 调出日期选择 datetime 调出日期时间选择 time 调出时间选择 ym 调出年月选择,
        'minDate': '2000-1-1', //最小日期
        'maxDate': new Date().getFullYear() + '-' + (new Date().getMonth() + 5) + '-' + new Date().getDate(), //最大日期,
        'callback': function() {
        	var birthday = $('#Jcalendar').val();
        	$('#birthday').val(birthday);
            var time = birthday.split('-');
            time = time.join('/');
            vm.start_time = time;
            vm.orderList = [];
            $.ajax({
				url: '<?php echo U('Order/orderList');?>',
				type: 'POST',
				dataType: 'json',
				data: {
					order_type : vm.order_type,
					date : vm.start_time,
					user_type : 'saler',
					page : vm.page
				}
			})
			.done(function(returnData) {
				if( returnData['data']['list'].length > 0 ){
					if( vm.orderList.length == 0 ){
						vm.orderList = returnData['data']['list'];
					}else{
						vm.orderList = vm.orderList.concat(returnData['data']['list']);
					}
					vm.goscroll = 1;
				}else{
					vm.goscroll = 0;
				}
			});
			vm.loadmore();
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