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
			<h1 class="y-confirm-order-h1">购买评价</h1>
		</header>
		<div class="main">
			<div class="goods_assess">
				<div class="eva_list_box"  v-for="(item,index) in commentList">
					<div class="user_eva">
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
						<div class="eva_goodsimg">
							<div class="imgbox" v-for="(image, thatIndex) in item.images">
								<img :src="image" @click="showBigImage(image)">
							</div>
						</div>
						<div class="eva_goods_cont">
							<span>{{item.add_time|time("yyyy-MM-dd")}}</span>
							<span>{{item.add_time|time("hh:mm:ss")}}</span>
							<span>{{item.attr_list}}</span>
							<em @click="showReplyBox(item['id'], item['reply_contain'])"><?php echo (L("_WAP_AGENT_REPLY_")); ?></em>
						</div>
					</div>
					<div v-if="!!item.reply_contain" class="business_reply">
						<p><span><?php echo (L("_WAP_AGENT_MERCHANT_RESPOND_")); ?>：</span>{{item.reply_contain}}</p>
					</div>
				</div>
			</div>
		</div>
			<!-- 弹窗 -->
			<div class="mask" @click="hideBigImage"></div>
			<div class="bigevaimg">
				<div class="msevaimg">
					<img src="" id="JshowBigImage">
				</div>
			</div>
			<div class="evasim">
				<div class="add_eva">
					<input type="text" id="JreplyContain" placeholder="<?php echo (L("_WAP_AGENT_CONTENT_")); ?>">
					<div class="btn_wrap">
						<a href="javascript:;" @click="replyComment"><?php echo (L("_COMMON_CONFIRM_")); ?></a>
						<a href="javascript:;" @click="hideReplyBox"><?php echo (L("_COMMON_CANCEL_")); ?></a>
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
			commentList : [],
			page : 1,
			goscroll : 1,
			id : '',
		},
		created(){
			this.loadComment();
			this.loadmore();
		},
		mounted(){
		   $('#Jloading').fadeOut();
		},
		methods : {
			loadComment : function() {
				var that = this;
				console.log(that.page);
				$.ajax({
					url: '<?php echo U('Agent/agentGoodsComment');?>',
					type: 'POST',
					dataType: 'json',
					data: {
						page : that.page
					}
				})
				.done(function(returnData) {
					if( returnData['data']['list'].length > 0 ){
						if( that.commentList.length == 0 ){
							that.commentList = returnData['data']['list'];
						}else{
							that.commentList = that.commentList.concat(returnData['data']['list']);
						}
						vm.goscroll = 1;
					}else{
						vm.goscroll = 0;
					}
				});
			},
			loadmore(){
				document.addEventListener("scroll",function(){
					var scrollTop = window.pageYOffset 
						|| document.documentElement.scrollTop 
						|| document.body.scrollTop 
						|| 0;
					if(scrollTop + window.innerHeight >= document.body.clientHeight){
						if(vm.goscroll){
							vm.page++;
							vm.goscroll = 0;
							vm.loadComment();
						}
					}
				})
			},
			showBigImage(image) {
				$('.mask').fadeIn();
				$('.bigevaimg').fadeIn();
				$('#JshowBigImage').attr('src', image);
			},
			hideBigImage(){
				$('.mask').fadeOut();
				$('.bigevaimg').fadeOut();
			},
			showReplyBox(id, reply_contain) {
				if ( !reply_contain ) {
					this.id = id;
					$('.mask').fadeIn();
					$('.evasim').fadeIn();
				} else {
					automsgbox('您已回复该评论');
				}
			},
			hideReplyBox() {
				$('.mask').fadeOut();
				$('.evasim').fadeOut();
			},
			replyComment() {
				var reply_contain = $('#JreplyContain').val();
				if ( !reply_contain ) {
					automsgbox('请输入回复内容');
					return;
				}
				$.ajax({
					url: '<?php echo U('Agent/replyComment');?>',
					type: 'POST',
					dataType: 'json',
					data: {
						id : this.id,
						reply_contain : reply_contain
					}
				})
				.done(function(returnData) {
					if ( returnData['status'] == '200000' ) {
						automsgbox('回复成功', function(){
							window.location.href = window.location.href;
						});
					} else {
						automsgbox(returnData['message'])
					}
				});
			}
		}
	})

	$(document).click(function(e){ 
		e = window.event || e; 
		obj = $(e.srcElement || e.target);
	 	if ($(obj).is(".mask")) { 
	 		$('.mask').fadeOut();
			$('.bigevaimg').fadeOut();
			$('.evasim').fadeOut();
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