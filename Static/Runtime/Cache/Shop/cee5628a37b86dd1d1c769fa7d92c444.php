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
	
	<link rel="stylesheet" type="text/css" href="css/swiper.css">

</head>

<body>

	<div class="content" id="content">
		<div class="goods_detail_wrap">
			<div class="goods_img_wrap swiper-container" id="Goods_img_cont">
				<div class="goods_img_cont swiper-wrapper">
					<div class="goods_img_box swiper-slide" v-for="(item,index) in goodsImages"><img :src="item"></div>
				</div>
				<div class="slide_num">{{slideNum}}/{{goodsImages.length}}</div>
				<span class="back"></span>
			</div>
			<div class="goods_msg">
				<p class="name db-overflow">
					{{goodsInfo.goods_name}}
					<em v-if="goodsInfo['is_auth'] == '1'">平台认证</em>
				</p>
				<p class="msgtt">{{goodsInfo.introduction}}</p>
				<div class="g_msg_tt">
					<span>RM{{goodsInfo.goods_price}}</span>
					<p>热销{{goodsInfo.sale_number}}件, {{goodsInfo.browsing_number}}人浏览过</p>
				</div>
				<div class="select_g_t">
					<div class="s_g_t" @click="editGoodsInfo">
						<span>选择</span>
						<input type="text" name="" disabled="disabled" value="绿色">
						<em></em>
					</div>
				</div>
			</div>
			<div class="goods_detail">
				<div class="g_de_t">
					<em class="lf"></em>
					<span>商品详情</span>
					<em class="rt"></em>
				</div>
				<div class="gs_d" v-html="Detail"></div>
			</div>
			<div class="goods_assess">
				<div class="tt">商品评价({{commentCount}})</div>
				<div class="g_assess_cont" v-if="commentCount == 0">
					<p>暂无商品评价</p>
				</div>
				<div class="g_assess_cont" v-else>
					<div class="eva_list_box">
						<div class="user_eva" v-for="(item,index) in commentList">
							<div class="eva_top">
								<div class="eva_user">
									<img :src="item.headimgurl">
									<span>{{item.nickname}}</span>
								</div>
								<div class="eva_star_cont">
									<div class="eva_star_box" :class="{on:item.star>=1}"></div>
									<div class="eva_star_box" :class="{on:item.star>=2}"></div>
									<div class="eva_star_box" :class="{on:item.star>=3}"></div>
									<div class="eva_star_box" :class="{on:item.star>=4}"></div>
									<div class="eva_star_box" :class="{on:item.star>=5}"></div>
								</div>
							</div>
							<p>{{item.contain}}</p>
							<div class="eva_goods_cont">
								<span>{{item.add_time | time(1)}}</span>
								<span>{{item.attrList}}</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<a :href="'<?php echo U('GoodsComment/goodsComment');?>?goods_id='+goods_id" class="seemore_eva">查看更多评论</a>
			<!-- 编辑规格 -->
			<div class="select_goods_box" id="Jedit">
				<div class="s_g_top">
					<div class="s_g_img">
						<img :src="goodsInfo.goods_image">
					</div>
					<p class="s_g_name">{{goodsInfo.goods_name}}</p>
					<span class="s_price">RM{{goodsInfo.goods_price}}</span>
					<em class="close">x</em>
				</div>
				<div class="s_g_check_box" v-for="(item,index) in attrNameInfo">
					<span>{{item.attr_name}}</span>
					<ul class="s_g_c_ul">
						<li :class="{on:ind == 0}" v-for="(it,ind) in item.attrValue">{{it.attr_value}}</li>
					</ul>
				</div>
			</div>
			<div class="eva_btnwrap">
				<a href="javascript:;" class="eva_edi">编辑</a>
				<a href="javascript:;" class="shelf" v-if="goodsInfo['is_on_sale'] == '0'">上架</a>
				<a href="javascript:;" class="shelf" v-else>下架</a>
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

	<script type="text/javascript" src="/Static/Public/Wechat/js/swiper.min.js"></script>
	<script type="text/javascript">
		var vm = new Vue({
			el : "#content",
			data : {
				goodsInfo : [],
				goods_id : <?php echo ($_GET["goods_id"]); ?>,
				goodsImages : [],
				slideNum : 1,
				Detail : "",
				commentCount : '',
				commentList : [],
				attrNameInfo : [],
				longitude : 113.37763,
				latitude : 23.13275,
				is_on_sale : 1
			},
			created(){
				this.getGoodsInfo();
			},
			mounted(){
			   $('#Jloading').fadeOut();
			},
			methods : {
				getGoodsInfo : function(){
					var that = this;
					$.ajax({
						url : "<?php echo U("Goods/goodsDetail");?>",
						type : "POST",
						dataType : "json",
						data : {
							goods_id : that.goods_id,
							longitude : that.longitude,
							latitude : that.latitude
						}
					})
					.done(function(data){
						that.goodsInfo = data.data.goodsInfo;
						that.goodsImages = data.data.goodsImages;
						that.Detail = data.data.goodsDesc;
						that.commentCount = data.data.commentCount;
						that.commentList = data.data.commentList;
						that.$nextTick(function(){
			            var mySwiper = new Swiper('#Goods_img_cont', {
			  					autoplayDisableOnInteraction : false, 
			  					  onSlideChangeEnd: function(swiper){
			       					 that.slideNum = swiper.activeIndex+1;
			      				}
			  				  })
			            })
					})

					$.ajax({
						url : "<?php echo U("Goods/getSkuAttr");?>",
						type : "POST",
						dataType : "json",
						data : {}
					})
					.done(function(data){
						that.attrNameInfo = data.data.attrNameInfo;
					})
				},
				setGoodsOnSale : function(){
					var that = this;
					$.ajax({
						url : "<?php echo U("Goods/setOnSale");?>",
						type : "POST",
						dataType : "json",
						data : {
							goods_id : that.goods_id,
							is_on_sale : that.is_on_sale
						}
					})
					.done(function(data){
						if(data.status == 200000){
							automsgbox(data.message);
						}else{
							automsgbox(data.message);
						}
					})
				},
				editGoodsInfo : function(){
					$("#Jedit").slideDown();
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