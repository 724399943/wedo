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
			<h1 class="y-confirm-order-h1"><?php echo (L("_WAP_USER_WITHDRAW_")); ?></h1>
		</header>
		<div class="main">
			<div class="withdrawals">
				<div class="wdraw_top">
					<p><?php echo (L("_WAP_USER_WITHDRAWALAMOUNT_")); ?></p>
					<input type="text" placeholder="<?php echo (L("_WAP_USER_PLEASEENTER_")); ?>(<?php echo (L("_WAP_USER_MINIMUMWITHDRAWAL_")); echo C('minWithdrawMoney'); echo (L("_COMMON_GINGGIT_")); ?>，<?php echo (L("_WAP_USER_MAXIMUMWITHDRAWAL_")); echo ($maxWithdrawMoney); echo (L("_COMMON_GINGGIT_")); ?>)" v-model="dataJson.money">
				</div>
				<div class="p_cen_m">
					<ul class="sm_ul">
						<li class="smli">
							<a href="javascript:;" @click="onPayType">
								<span class="s1"><?php echo (L("_WAP_USER_PLEASESELECT_")); ?></span>
								<span class="s2"><?php echo (L("_WAP_USER_PAYMENTGATEWAY_")); ?></span>
								<em class="ei"></em>
							</a>
						</li>
						<li class="smli">
							<a href="javascript:;">
								<span class="s1"><?php echo (L("_WAP_USER_ACCOUNT_")); ?></span>
								<input type="text" class="txi" v-model="dataJson.account">
								<em class="ei"></em>
							</a>
						</li>
					</ul>
					<ul class="sm_ul">
						<li class="smli">
							<a href="javascript:;">
								<span class="s1"><?php echo (L("_WAP_USER_ACTUALNAME_")); ?></span>
								<input type="text" class="txi" v-model="dataJson.truename">
								<em class="ei"></em>
							</a>
						</li>
					</ul>
				</div>
			</div>
				<a href="javascript:;" class="widup" @click="goToVerify"><?php echo (L("_WAP_USER_SUBMIT_")); ?></a>
		</div>
		<!-- 弹窗 -->
		<div class="mask"></div>
		<div class="setPay_psw KeditPassword">
			<p><?php echo (L("_PC_USER_WITHDRAWAL_PASSWORD_")); ?></p>
			<ul>
				<li class="vertical-mi">{{set_password.substring(0,1)}}</li>
				<li class="vertical-mi">{{set_password.substring(1,2)}}</li>
				<li class="vertical-mi">{{set_password.substring(2,3)}}</li>
				<li class="vertical-mi">{{set_password.substring(3,4)}}</li>
				<li class="vertical-mi">{{set_password.substring(4,5)}}</li>
				<li class="vertical-mi">{{set_password.substring(5,6)}}</li>
				<div class="spinput">	
					<input type="password" maxlength="6" v-model="set_password" @input="editing">
				</div>
			</ul>
		</div>
		<div class="setPay_psw KverifyPassword">
			<p><?php echo (L("_PC_USER_ENTER_WITHDRAWAL_PASSWORD_")); ?></p>
			<ul>
				<li class="vertical-mi">{{password.substring(0,1)}}</li>
				<li class="vertical-mi">{{password.substring(1,2)}}</li>
				<li class="vertical-mi">{{password.substring(2,3)}}</li>
				<li class="vertical-mi">{{password.substring(3,4)}}</li>
				<li class="vertical-mi">{{password.substring(4,5)}}</li>
				<li class="vertical-mi">{{password.substring(5,6)}}</li>
				<div class="spinput">	
					<input type="password" maxlength="6" v-model="password" @input="changing">
				</div>
			</ul>
		</div>
		<div class="selpayType" id="selPay">
			<ul>
				<li @click="selpay(0)"><?php echo (L("_COMMON_WECHAT_")); ?></li>
				<li @click="selpay(1)"><?php echo (L("_COMMON_ALIPAY_")); ?></li>
				<li @click="selpay(2)"><?php echo (L("_COMMON_CREDIT_CARD_")); ?></li>
			</ul>
			<a href="javascript:;" @click="cancelPay"><?php echo (L("_COMMON_CANCEL_")); ?></a>
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
			set_password : '',
			password : '',
			withdraw_password : "<?php echo ($userInfo['withdraw_password']); ?>",
		},
		mounted(){
		   $('#Jloading').fadeOut();
		},
		methods : {
			goToVerify() {
				if ( !this.dataJson['money'] ) {
					automsgbox("<?php echo (L("module_user_withdraw_amount")); ?>");
					return;
				}
				if ( !this.dataJson['withdraw_type'] && this.dataJson['withdraw_type'] != 0 ) {
					automsgbox('<?php echo (L("module_user_withdraw_methods")); ?>');
					return;
				}
				if ( !this.dataJson['account'] ) {
					automsgbox('<?php echo (L("module_user_account_number")); ?>');
					return;
				}
				if ( !this.dataJson['truename'] ) {
					automsgbox('<?php echo (L("module_user_actual_name")); ?>');
					return;
				}
				this.showPasswordBox();
			},
			editing() {
				var password = $(event.target).val(),
					match = !!password.match(/^\d+$/);
				if ( match == false ) {
					this.set_password = '';
					automsgbox('密码只能是数字');
					return;
				}
				if ( password.length == 6 ) this.editWithdrawPassword();
			},
			changing() {
				var password = $(event.target).val(),
					match = !!password.match(/^\d+$/);
				if ( match == false ) {
					this.password = '';
					automsgbox('密码只能是数字');
					return;
				}
				if ( password.length == 6 ) this.verifyWithdrawPassword();
			},
			withdraw() {
				var that = this;
				$.ajax({
					url : "<?php echo U("User/withdraw");?>",
					type : "POST",
					dataType : "json",
					data : that.dataJson
				})
				.done(function(data){
					if (data.status == 200000) {
						automsgbox(data['message'], function(){
							window.location.href = window.location.href;
						});
					}else{
						automsgbox(data.message);
						that.password = '';
						that.hidePasswordBox();
					}
				})
			},
			editWithdrawPassword() {
				var that = this;
				if ( !that.set_password ) {
					automsgbox('<?php echo (L("_WAP_AGENT_NEWPASSWORD_")); ?>');
					return;
				}
				$.ajax({
					url: '<?php echo U('User/editWithdrawPassword');?>',
					type: 'POST',
					dataType: 'json',
					data: {password:that.set_password}
				})
				.done(function(returnData) {
					if ( returnData['status'] == '200000' ) {
						automsgbox(returnData['message'], function(){
							$('.KverifyPassword').show();
						});
					} else {
						automsgbox(returnData['message']);
					}
				});			
			},
			verifyWithdrawPassword() {
				var that = this;
				$.ajax({
					url: '<?php echo U('User/verifyWithdrawPassword');?>',
					type: 'POST',
					dataType: 'json',
					async: false,
					data: {password:that.password}
				})
				.done(function(returnData) {
					if ( returnData['status'] == '200000' ) {
						that.withdraw();
					} else {
						automsgbox(returnData['message']);
						that.password = '';
						that.hidePasswordBox();
					}
				});
			},
			showPasswordBox() {
				$('.mask').show();
				( !this.withdraw_password ) ? $('.KeditPassword').show() : $('.KverifyPassword').show();
			},
			hidePasswordBox() {
				$('.mask').hide();
				$('.setPay_psw').hide();
			},
			onPayType : function(){
				$("#selPay").slideDown(200);
				$(".mask").show();	
			},
			selpay : function(withdraw_type){
				var that = $(event.target),
					text = that.text();
				$('.s2').text(text);
				this.dataJson.withdraw_type = withdraw_type;
				that.siblings().removeClass('on');
				that.addClass('on');
				$("#selPay").slideUp(200);
				$(".mask").hide();
			},
			cancelPay : function(){
				$("#selPay").slideUp(200);
				$(".mask").hide();
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