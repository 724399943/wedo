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

	<div class="content enrollbg" id="content">
		<header class="head">
			<span class="back"></span>
			<h1 class="y-confirm-order-h1"><?php echo (L("_WAP_REGISTER_BUSINESS_REGISTER_")); ?></h1>
		</header>
		<div class="enroll_wrap">
			<!-- <div class="nav" id="navTab">
				<span class="lr on" @click="ontab(0)"><?php echo (L("_WAP_REGISTER_BYPHONE_")); ?></span>
				<span class="sr" @click="ontab(1)"><?php echo (L("_WAP_REGISTER_BYEMAIL_")); ?></span>
			</div> -->
			<!-- <div class="enr_input" v-if="tab == 0">
				<form>
					<ul>
						<li class="line">
							<div class="einput">
								<span><?php echo (L("_WAP_REGISTER_PHONENUMBER_")); ?></span>
								<input type="text" name="" value="+86" placeholder="<?php echo (L("_COMMON_PLEASE_ENTER_")); echo (L("_WAP_REGISTER_PHONENUMBER_")); ?>" id="Account" v-model="dataJson.phone">
							</div>
						</li>
						<li class="line">
							<div class="einput">
								<span><?php echo (L("_WAP_REGISTER_PASSWORD_")); ?></span>
								<input type="password" name="" placeholder="<?php echo (L("_WAP_REGISTER_LIMIT_")); ?>" v-model="dataJson.password">
							</div>
						</li>
						<li class="line">
							<div class="einput">
								<input type="text" name="" placeholder="<?php echo (L("_WAP_REGISTER_VERIFY_")); ?>" class="code" v-model="dataJson.verify">
								<a href="javascript:;" class="getCode_btn" v-if="noget" @click="getCode"><?php echo (L("_WAP_REGISTER_GETVERIFY_")); ?></a>
								<a href="javascript:;" class="getCode_btn grey" v-else><?php echo (L("_COMMON_ALL_REGAINVERIFY_")); ?>{{settimes}}</a>
							</div>
						</li>
						<li>
							<div class="einput">
								<span class="res"><?php echo (L("_WAP_REGISTER_REFERRER_")); ?></span>
								<input type="text" name="" class="rin" v-model="dataJson.referrer">
							</div>
						</li>
						<li>
							<div class="einput">
								<span class="res"><?php echo (L("_WAP_REGISTER_AGREE_")); ?></span>
								<a href="javascript:;" class="eag"><?php echo (L("_WAP_REGISTER_AGREEMENT_")); ?></a>
							</div>
						</li>
					</ul>
				</form>
				<p class="ccust"><?php echo (L("_WAP_LOGIN_LOGINTIPS_")); echo C('telphone');?></p>
				<a href="javascript:;" class="register_btn" @click="commitFun"><?php echo (L("_WAP_REGISTER_REGISTER_")); ?></a>
			</div> -->
			<div class="enr_input">
				<form>
					<ul>
						<li class="line">
							<div class="einput ei">
								<span class="espan"><?php echo (L("_WAP_REGISTER_EMAIL_")); ?></span>
								<input type="text" name="" placeholder="<?php echo (L("_COMMON_PLEASE_ENTER_")); echo (L("_WAP_REGISTER_EMAILADDRESS_")); ?>" id="Account" v-model="dataJson.email">
							</div>
						</li>
						<li class="line">
							<div class="einput ei">
								<span class="espan"><?php echo (L("_WAP_REGISTER_PASSWORD_")); ?></span>
								<input type="password" name="" placeholder="<?php echo (L("_WAP_REGISTER_LIMIT_")); ?>" v-model="dataJson.password">
							</div>
						</li>
						<li class="line">
							<div class="einput">
								<input type="text" name="" placeholder="<?php echo (L("_WAP_REGISTER_VERIFY_")); ?>" class="code" v-model="dataJson.verify">
								<a href="javascript:;" class="getCode_btn" v-if="noget" @click="getCode"><?php echo (L("_WAP_REGISTER_GETVERIFY_")); ?></a>
								<a href="javascript:;" class="getCode_btn grey" v-else><?php echo (L("_WAP_REGISTER_REGAINVERIFY_")); ?>{{settimes}}</a>
							</div>
						</li>
						<li class="line">
							<div class="einput phone">
								<span class="espan"><?php echo (L("_WAP_REGISTER_PHONENUMBER_")); ?></span>
								<input type="text" name="" value="+86" placeholder="<?php echo (L("_COMMON_PLEASE_ENTER_")); echo (L("_WAP_REGISTER_PHONENUMBER_")); ?>" v-model="dataJson.phone">
							</div>
						</li>
						<li>
							<div class="einput re">
								<span class="espan res"><?php echo (L("_WAP_REGISTER_REFERRER_")); ?></span>
								<input type="text" name="" v-model="dataJson.referrer">
							</div>
						</li>
						<li>
							<div class="einput">
								<span class="res"><?php echo (L("_WAP_REGISTER_AGREE_")); ?></span>
								<a href="javascript:;" class="eag"><?php echo (L("_WAP_REGISTER_AGREEMENT_")); ?></a>
							</div>
						</li>
					</ul>
				</form>
				<p class="ccust"><?php echo (L("_WAP_LOGIN_LOGINTIPS_")); echo C('serviceTel');?></p>
				<a href="javascript:;" class="register_btn" @click="commitFun"><?php echo (L("_WAP_REGISTER_REGISTER_")); ?></a>
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
				dataJson : {type:0},
				tab : 0,
				settimes : 60,
				noget : 1,
				account : '',
			},
			mounted(){
			   $('#Jloading').fadeOut();
			},
			methods : {
				commitFun : function(){
					var  that = this;
					$.ajax({
						url : "<?php echo U("Login/register");?>",
						type : "POST",
						dataType : "json",
						data : {
							phone : that.dataJson.phone,
							password : that.dataJson.password,
							verify :  that.dataJson.verify,
							email : that.dataJson.email,
							referrer : that.dataJson.referrer,
							status : 1
						}
					})
					.done(function(returnData){
						if (returnData['status'] == 200000) {
                            automsgbox('<?php echo (L("_WAP_REGISTER_REGISTERSUCCESS_")); ?>', function() {
                                window.location.href = "<?php echo U('Agent/completeAgent');?>?user_id=" + returnData['data']['userId'];
                            },true);
                        } else {
                            automsgbox(returnData.message);
                        }
					})
				},
				ontab : function(index){
					var that = this;
					$('#navTab').find('span').eq(index).addClass('on');
					$('#navTab').find('span').eq(index).siblings('span').removeClass('on');
					that.tab = index;
				},
				getCode : function(){
					var that = this;
					that.account = $('#Account').val();
					$.ajax({
						url : "<?php echo U("Login/getVerifyCode");?>",
						type : "POST",
						dataType : "json",
						data : {
							account : that.account,
							type : 0,
						}
					})
					.done(function(returnData) {
                       if (returnData.status == 200000) {
                       		automsgbox(returnData['data']['code']);
                            var thats = $('.getCode_btn');
                            that.settime(thats);
                       } else {
                            automsgbox(returnData.message);
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