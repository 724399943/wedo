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
			<h1 class="y-confirm-order-h1"><?php echo (L("_WAP_AGENT_MERCHANDISE_")); ?></h1>
			<a href="<?php echo U('PointOrder/pointOrder');?>" class="mail"><?php echo (L("_WAP_POINTORDER_HISTORY_")); ?></a>
		</header>
		<div class="main">
			<div class="integral_goods">
				<div class="order_nav">
					<div class="o_nav">
						<span class="on" @click="ontab(0)"><?php echo (L("_WAP_AGENT_NOTLIST_")); ?></span>
						<em></em>
					</div>
					<div class="o_nav">
						<span @click="ontab(3)"><?php echo (L("_WAP_AGENT_TOBELIST_")); ?></span>
						<em style="display:none;"></em>
					</div>
				</div>
				<div class="isgoods_main" v-if="nothing == 0 && type == 0">
					<!-- 未申请 -->
					<div class="is_hgoods">
						<div class="sel_goodsm">
							<ul>
								<li v-for="(item,index) in goodsList">
									<div class="s_goods_wrap">
										<div class="s_cbox">
											<input type="checkbox" name="" :id="item.goods_id" @change="selfun">
											<label :for="item.goods_id"></label>
										</div>
									</div>
									<a :href="'/Goods/goodsDetail?goods_id='+item.goods_id">
										<div class="imgbox">
											<img :src="item.goods_image">
										</div>
										<div class="se_ggg sexiao">
											<h1>{{item.goods_name}}</h1>
											<p class="db-overflow">{{item.introduction}}</p>
											<span>RM{{item.goods_price}}</span>
										</div>
									</a>
								</li>
							</ul>
						</div>
						<a href="javascript:;" class="widup" @click="goodsToPoint"><?php echo (L("_WAP_AGENT_APPLYFORMERCHANDISE_")); ?></a>
					</div>
				</div>
				<!-- 没有内容 -->
				<div class="no_igoods" v-else-if="nothing == 1 && type == 0">
					<p><?php echo (L("_WAP_AGENT_ZEROPRODUCTAPPLIED_")); ?></p>
				</div>
				<div class="isgoods_main" v-if="nothing == 0 && type == 3">
					<!-- 已申请 -->
					<div class="is_hgoods">
						<div class="sel_goodsm">
							<ul>
								<li v-for="(item,index) in goodsList">
									<div class="is_hhh">
										<a :href="'/Goods/goodsDetail?goods_id='+item.goods_id">
											<div class="imgbox">
												<img :src="item.goods_image">
											</div>
											<div class="se_ggg">
												<h1>{{item.goods_name}}</h1>
												<p class="db-overflow">{{item.introduction}}</p>
												<span v-if="item.status == 1">{{item.goods_price}}积分</span>
											</div>
										</a>
									</div>
									<span class="tips" v-if="item.status == 0">申请中</span>
									<span class="tips" v-else-if="item.status == 1">已通过</span>
									<span class="tips" v-else>未通过</span>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<!-- 没有内容 -->
				<div class="no_igoods" v-else-if="nothing == 1 && type == 3">
					<p><?php echo (L("_WAP_AGENT_ZEROPRODUCT_")); ?></p>
				</div>
			</div>
			<!-- 提示 -->
			<div class="istips" id="closeTips" @click="closetips">
				<div class="is_box">
					<p class="tit"><?php echo (L("_WAP_AGENT_NOTICE_")); ?></p>
					<p class="tp"><?php echo (L("_WAP_AGENT_NOTICECONT_")); ?></p>
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

<script type="text/javascript">
	var vm = new Vue({
		el : "#content",
		data : {
			type : 0, // 0：未申请，1：审核中，2：已审核，3：1,2共存
			goodsList : [],
			page : 1,
			goscroll:1,
			goods_id : '',
			nothing : 0
		},
		created(){
			this.getGoods();
			this.loadmore();
		},
		mounted(){
		   $('#Jloading').fadeOut();
		},
		methods : {
			getGoods : function(){
				var that = this;
				$.ajax({
					url: "<?php echo U("Agent/myPointGoods");?>",
					type: "POST",
					dataType: "json",
					data: {
						type : that.type
					}
				})
				.done(function(data) {
					if(data.data.list.length > 0){
						if(that.goodsList.length == 0){
							that.goodsList = data.data.list;
						}else{
							that.goodsList = that.goodsList.concat(data.data.list);
						}
						that.goscroll = 1;
						that.nothing = 0;
					}else{
						that.goscroll = 0;
						if(that.goodsList.length == 0){
							that.nothing = 1;
						}
					}
				})
			},
			goodsToPoint : function(){
				var that = this;
				$.ajax({
					url : "<?php echo U("Agent/goodsToPoint");?>",
					type : "POST",
					dataType : "json",
					data : {
						goods_id : that.goods_id
					}
				})
				.done(function(data){
					if(data.status == 200000){
						automsgbox('<?php echo (L("_WAP_AGENT_GOODS_TO_POINT_")); ?>', function(){
							window.location.href = window.location.href;
						});
					}else{
						automsgbox(data.message);
					}
				})
			},
			selfun : function(){
				var that = this;
				that.goods_id = $(event.target).attr('id');
			},
			ontab : function(index){
				var that = this;
				$(".o_nav").find('span').removeClass("on");
				$(".o_nav").find("em").fadeOut();
				$(event.target).addClass('on');
				$(event.target).siblings('em').show();
				that.goodsList = [];
				that.type = index;
				that.getGoods();
			},
			closetips : function(){
				$(event.target).fadeOut();
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
							that.page ++;
							that.goscroll = 0;
							that.getGoods();
						}
					}
				})
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