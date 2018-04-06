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
			<h1 class="y-confirm-order-h1"><?php echo (L("_WAP_FORGET_TITTLE_")); ?></h1>
		</header>
		<div class="forget_psw">
			<form>
				<ul>
					<li>
						<div class="fpinput fp">
							<span><?php echo (L("_WAP_LOGIN_ID_")); ?></span>
							<input type="text" name="" placeholder="<?php echo (L("_WAP_FORGET_ENTERNUMOREMAIL_")); ?>" id="Account" v-model="dataJson.account">
						</div>
					</li>
					<li>
						<div class="fpinput fp">
							<span><?php echo (L("_WAP_FORGET_NEWPASSWORD_")); ?></span>
							<input type="password" name="" placeholder="<?php echo (L("_WAP_REGISTER_LIMIT_")); ?>" v-model="dataJson.password">
						</div>
					</li>
					<li>
						<div class="fpinput">
							<input type="text" name="" placeholder="<?php echo (L("_WAP_REGISTER_VERIFY_")); ?>" class="codet" v-model="dataJson.verify">
							<a href="javascript:;" class="fpcode_btn" v-if="noget" @click="getCode"><?php echo (L("_WAP_REGISTER_GETVERIFY_")); ?></a>
							<a href="javascript:;" class="fpcode_btn grey" v-else><?php echo (L("_WAP_REGISTER_REGAINVERIFY_")); ?>({{settimes}})</a>
						</div>
					</li>
				</ul>
			</form>
			<div class="fpcao" id="Jfpcao">
				<p><?php echo (L("_WAP_FORGET_TIPS_")); ?></p>
				<a href="javascript:;" class="reset_psw" @click="commitFun"><?php echo (L("_WAP_FORGET_RESETPASSWORD_")); ?></a>
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
				dataJson : {},
				noget : 1,
				settimes : 60,
				account : '',
			},
			mounted(){
			   $('#Jloading').fadeOut();
			},
			methods : {
				commitFun : function(){
					var that = this;
					that.account = $('#Account').val();
					$.ajax({
						url : "<?php echo U("Login/resetPassword");?>",
						type : "POST",
						dataType : "json",
						data : {
							account : that.account,
							verify : that.dataJson.verify,
							password : that.dataJson.password,
						}
					})
					.done(function(data){
						if (data.status == 200000) {
                            automsgbox(data.message,function() {
                                window.location.href = "<?php echo U('Login/login');?>";
                            },true);
                        } else {
                            automsgbox(data.message);
                        }
					})
				},
				getCode : function(){
					var that = this;
					that.account = $('#Account').val();
					if ( !that.account ) {
						automsgbox('<?php echo (L("_WAP_FORGET_ENTERNUMOREMAIL_")); ?>');
						return;
					}
					$.ajax({
						url : "<?php echo U("Login/getVerifyCode");?>",
						type : "POST",
						dataType : "json",
						data : {
							account : that.account,
							type : 2,
						}
					})
					.done(function(returnData) {
                       if (returnData.status == 200000) {
                            var thats = $('.getCode_btn');
                            that.settime(thats);
                       } else {
                            automsgbox(returnData['message']);
                       }
                   })
				},settime : function (_this) {
                    var that = this;
                    if (vm.settimes == 0) { 
                        _this.html("<?php echo (L("_WAP_REGISTER_GETVERIFY_")); ?>"); 
                        vm.noget = 1;
                        vm.settimes = 60; 
                        return;
                    } else { 
                        _this.html("<?php echo (L("_WAP_REGISTER_REGAINVERIFY_")); ?>(" + vm.settimes + ")"); 
                        vm.noget = 0;
                        vm.settimes--;
                    } 
                    setTimeout(function() { that.settime(_this) } ,1000) 
                }
			}
		})
		$("input").on("focus",function(event){
			$("#Jfpcao").css({"position":"relative","marginTop":"70px"});
			event.stopPropagation();
		})
		$("input").on("blur",function(){
			$("#Jfpcao").css({"position":"fixed"});
		})
		$("document").on("touchstart",function(){
			$("#Jfpcao").css({"position":"fixed"});	
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