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
			<h1 class="y-confirm-order-h1"><?php echo (L("_WAP_AGENT_SUBTOTAL_")); ?>{{total}}元</h1>
		</header>
		<div class="main">
			<div class="settle_details">
				<div class="order_nav">
					<div class="o_nav on">
						<span class="on" id="Jcalendar"><?php echo (L("_WAP_AGENT_ALLDATES_")); ?></span>
						<input id="Jcalendar" type="text" name="" value="" v-model="start_time">
						<input id="birthday" type="hidden" name="birthday" value="2007-11-08">
						<em></em>
					</div>
					<div class="o_nav">
						<span @click="change"><?php echo (L("_WAP_AGENT_ALLSTATUS_")); ?></span>
						<i></i>
						<ul class="onav_ul" id="orderul">
							<li @click="selSate(0)"><?php echo (L("_WAP_AGENT_HASBEENPAID_")); ?></li>
							<li @click="selSate(1)"><?php echo (L("_WAP_AGENT_DUEPAYMENT_")); ?></li>
						</ul>
					</div>
				</div>
				<div class="set_d_list">
					<ul>
						<li v-for="(item,index) in list">
							<div class="lf">
								<p class="tt">{{item.event}}</p>
								<p class="time">{{item.add_time | time(0)}}</p>
							</div>
							<span v-if="item.type == 1"><?php echo (L("_WAP_AGENT_HASBEENPAID_")); ?></span>
							<span v-else>待结算</span>
						</li>
					</ul>
				</div>
			</div>
			<div class="navmask"></div>
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

<?php if($_COOKIE['think_language'] == 'zh-cn'): ?><script type="text/javascript" src="/Static/Public/Wechat/js/LCalendar.js"></script>
<?php else: ?>
	<script type="text/javascript" src="/Static/Public/Wechat/js/LCalendar_en.js"></script><?php endif; ?>
<script type="text/javascript">
	var vm = new Vue({
		el : "#content",
		data : {
			page :1,
			goscroll :1,
			list : [],
			total : '',
			start_time : '',
			obol : true,
			type : 1
		},
		created (){
			this.getSettlementLog();
		},	
		mounted(){
		   $('#Jloading').fadeOut();
		},
		methods : {
			getSettlementLog : function(){
				var that = this;
				$.ajax({
					url : "<?php echo U("Agent/settlementLog");?>",
					type : "POST",
					dataType : "json",
					data : {
						page : that.page,
						type : that.type
					}
				})
				.done(function(data){
					if(data.data.list.length>0){
						that.total = data.data.total;
			        	if(that.list.length == 0){
			        		that.list = data.data.list;
			        	}else{
			        		that.list = that.list.concat(data.data.list);
			        	}
		        		vm.goscroll = 1;
		        	}else{
		        		vm.goscroll = 0;
		        	}
				})
			},
			change : function(){
				var that = this;
				$('#orderul').slideToggle(300);
				if(that.obol){
					$(".navmask").fadeIn();
					that.obol = false;		
				}else{
					$(".navmask").fadeOut();
					that.obol = true;
				}
			},
			selSate : function(index){
				var that = this;
				$(event.target).siblings().removeClass('on');
				$(event.target).addClass('on');
				$("#orderul").slideUp(300);
				$(".navmask").fadeOut();
				that.obol = true;
				that.type = index;
				that.list = [];
				vm.getSettlementLog();
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
							vm.getGoods();
						}
					}
		        })
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
            vm.list = [];
            $.ajax({
				url: '<?php echo U('Agent/settlementLog');?>',
				type: 'POST',
				dataType: 'json',
				data: {
					date : vm.start_time,
					page : vm.page
				}
			})
			.done(function(data) {
				if(data.data.list.length>0){
		        	if(that.list.length == 0){
		        		that.list = data.data.list;
		        	}else{
		        		that.list = that.list.concat(data.data.list);
		        	}
	        		vm.goscroll = 1;
	        	}else{
	        		vm.goscroll = 0;
	        	}
			});
			vm.loadMore();
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