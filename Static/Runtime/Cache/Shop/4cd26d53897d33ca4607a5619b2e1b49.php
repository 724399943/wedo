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
	
	<link rel="stylesheet" href="/Static/Public/Admin/js/kindeditor/themes/default/default.css" />
	<style type="text/css">
		body,.bcont{position:absolute;top:0;left:0;right:0;bottom:0;overflow:auto;}
		.deleteBtn{display: inline-block; text-align: center; background: #fff; color: #967bdc; line-height: 20px; border: 1px solid #967bdc; border-radius: 3px; padding: 0 2%; font-size: 12px;} 
	</style>

</head>

<body>

	<div class="basic_wrap" id="content">
		<div class="conent bcont">
			<header class="head">
				<span class="back"></span>
				<h1 class="y-confirm-order-h1"><?php echo (L("_WAP_GOODS_EDITCOMMODITY_")); ?></h1>
			</header>
			<div class="main">
				<div class="add_g_top">
					<input type="hidden" v-model="dataJson['id']">
					<div class="add_title">
						<input type="text" placeholder="<?php echo (L("_WAP_GOODS_ENTERATITLE_")); ?>" v-model="dataJson['goods_name']">
					</div>
					<div class="add_title">
						<input type="text" placeholder="<?php echo (L("_WAP_GOODS_PRODUCTSUBTITLE_")); ?>" v-model="dataJson['introduction']">
					</div>
					<div class="add_g_de">
						<div class="g_imgm">
							<p><?php echo (L("_WAP_GOODS_PRODUCTPHOTO_")); ?></p>
							<ul>
								<li v-for="(item, index) in goodsImageData">
									<div class="imgbox">
										<img :src="item">
										<em></em>
									</div>
								</li>
								<li>
									<div class="upload_input">
										<input type="file" name="upload_input" id="upload_input"/>
									</div>
								</li>
							</ul>
						</div>
						<div class="g_imgm">
							<p><?php echo (L("_WAP_GOODS_DESCRIPTION_")); ?></p>
							<!-- <textarea placeholder="<?php echo (L("_WAP_GOODS_IMPORTDESCRIPTION_")); ?>" v-model="dataJson['description']"></textarea> -->
							<div contenteditable="true" v-html="dataJson['description']" id="description"></div>
						</div>
					</div>
				</div>
				<div class="add_g_classfiy">
					<div class="a_g_c" @click="showGoodsCategory">
						<a href="javscript:;"><?php echo (L("_WAP_GOODS_PLATFORMCATEGORY_")); ?></a>
						<input type="text" readonly="readonly" placeholder="<?php echo (L("_WAP_GOODS_PLEASESELECT_")); ?>" v-model="categoryName">
						<em></em>
					</div>
					<div class="a_g_c" @click="showAgentGoodsCategory">
						<a href="javscript:;"><?php echo (L("_WAP_GOODS_MERCHANTCATEGORY_")); ?></a>
						<input type="text" readonly="readonly" placeholder="<?php echo (L("_WAP_GOODS_PLEASESELECT_")); ?>" v-model="agentCategoryName">
						<em></em>
					</div>
				</div>
				<div class="add_g_emc" v-if="attrData.length > 0">
					<p><?php echo (L("_WAP_GOODS_GOODSATTR_")); ?></p>
					<div class="select_emc" v-for="(item, index) in attrData">
						<b class="attr_name">{{item['attr_name']}}</b>
						<div class="sel_add_gattr">
							<div class="s_e_box" v-bind:class="{on:selectedAttrStr.indexOf(value['id']) != '-1'}" v-for="(value, key) in item['attrValue']">
								<input type="hidden" name="attr" :value="value['attr_name_id']">
								<input type="hidden" name="attrname" :value="item['attr_name']">
								<input type="hidden" name="value" :value="value['id']">
								<span>{{value['attr_value']}}</span>
								<div class="check_box">
									<input type="checkbox" :id="'Jattr_' + value['id']" :value="value['id']" v-model="selectedAttr" @change="selGoodsAttr">
									<label :for="'Jattr_' + value['id']"></label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="add_g_mid" id="JeditSKU" style="display:none;"></div>
				<div class="add_g_emc">
					<p><?php echo (L("_WAP_GOODS_DELIVERYMETHOD_")); ?></p>
					<div class="select_emc">
						<div class="s_e_box">
							<span><?php echo (L("_WAP_GOODS_BYDELIVERY_")); ?></span>
							<div class="check_box">
								<input type="checkbox" id="cb1" value="0" v-model="expressType">
								<label for="cb1"></label>
							</div>
						</div>
						<div class="s_e_box">
							<span><?php echo (L("_WAP_GOODS_COLLECTFROMMERCHANT_")); ?></span>
							<div class="check_box">
								<input type="checkbox" id="cb2" value="1" v-model="expressType">
								<label for="cb2"></label>
							</div>
						</div>
					</div>
				</div>
				<div class="add_g_keyword">
					<p><?php echo (L("_WAP_GOODS_SETKEYWORDS_")); ?></p>
					<input type="text" placeholder="<?php echo (L("_WAP_GOODS_KEYWORDSTIPS_")); ?>" v-model="dataJson['keyword']">
				</div>
				<a href="javascript:;" class="add_g_btn" @click="editGoods"><?php echo (L("_WAP_GOODS_SAVE_")); ?></a>
			</div>
		</div>
		<div class="content basic" id="goodsCategory">
			<header class="head" style="position:absolute;">
				<span class="backed" @click="closeGoodsCategory('back')"></span>
				<h1 class="y-confirm-order-h1"><?php echo (L("_WAP_SUPPLEMENT_CHOOSECATEGORY_")); ?></h1>
				<a href="javascript:;" class="mail" @click="closeGoodsCategory('finish')"><?php echo (L("_WAP_SUPPLEMENT_COMPLETE_")); ?></a>
			</header>
			<div class="selClassfiy">  
				<p><?php echo (L("_WAP_GOODS_SELECTACLASSFIY_")); ?>。</p>
				<ul>
					<li v-for="(item,index) in goodsCategoryData">
						<div class="sclont">
							<span>{{item.category_name}}</span>
							<div class="check_box">
								<input :id="'goodsCategory_'+item.id" type="checkbox" :value="item['id']" v-model="categoryId" @change="selectGoodsCategory(item.category_name)">
								<label :for="'goodsCategory_'+item.id"></label>
							</div>
						</div>
					</li>
				</ul>
			</div>
		</div>
		<div class="content basic" id="agentGoodsCategory">
			<header class="head" style="position:absolute;">
				<span class="backed" @click="closeAgentGoodsCategory('back')"></span>
				<h1 class="y-confirm-order-h1"><?php echo (L("_WAP_SUPPLEMENT_CHOOSECATEGORY_")); ?></h1>
				<a href="javascript:;" class="mail" @click="closeAgentGoodsCategory('finish')"><?php echo (L("_WAP_SUPPLEMENT_COMPLETE_")); ?></a>
			</header>
			<div class="selClassfiy">  
				<p><?php echo (L("_WAP_GOODS_STOREACLASSFIY_")); ?>。</p>
				<ul>
					<li v-for="(item,index) in agentGoodsCategoryData">
						<div class="sclont">
							<span>{{item.category_name}}</span>
							<div class="check_box">
								<input :id="'agentGoodsCategory_'+item['id']" type="checkbox" :value="item['id']" v-model="agentCategoryId" @change="selectAgentGoodsCategory(item.category_name)">
								<label :for="'agentGoodsCategory_'+item['id']"></label>
							</div>
						</div>
					</li>
				</ul>
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

<script type="text/javascript" src="/Static/Public/Common/js/ajaxfileupload.js"></script>
<script type="text/javascript">
	var vm = new Vue({
		el : "#content",
		data : {
			agentId : "<?php echo (session('agentId')); ?>",
			dataJson : {},
			expressType : [],
			goodsCategoryData : [],
			agentGoodsCategoryData : [],
			categoryId : [],
			agentCategoryId : [],
			categoryName : '',
			agentCategoryName : '',
			goodsImageData : [],
			goodsAttr : [],
			attrData : [],
			skuData : {},
			selectedAttr : [],
			selectedAttrStr : '',
		},
		created(){
			this.loadGoodsInfo();
			this.loadGoodsCategory();
			this.loadAgentGoodsCategory();
		},
		mounted(){
		   $('#Jloading').fadeOut();
		},
		methods : {
			editGoods : function() {
				var that = this;
				if ( that.expressType.length == 0 ) {
					automsgbox('<?php echo (L("_WAP_GOODS_SELECTDISTRIBUTION_")); ?>');
					return;
				} else {
					that.dataJson['express_type'] = that.expressType.join(",");
				}
				if ( that.categoryId.length == 0 ) {
					automsgbox('<?php echo (L("_WAP_GOODS_SELECTACLASSFIY_")); ?>');
					return;
				} else {
					that.dataJson['category_id'] = that.categoryId[0];
				}
				if ( that.agentCategoryId.length == 0 ) {
					automsgbox('<?php echo (L("_WAP_GOODS_STOREACLASSFIY_")); ?>');
					return;
				} else {
					that.dataJson['agent_category_id'] = that.agentCategoryId[0];
				}
				that.dataJson['SKUattr[]'] = [];
				that.dataJson['SKUprice[]'] = [];
				that.dataJson['SKUnumber[]'] = [];
				$('input[name="SKUattr[]"]').each(function(){
					that.dataJson['SKUattr[]'].push($(this).val());
				});
				$('input[name="SKUprice[]"]').each(function(index){
					var thatValue = $(this).val();
					if ( !thatValue ) {
						automsgbox('<?php echo (L("_WAP_GOODS_GOODSPRICE_")); ?>');
					} else {
						that.dataJson['SKUprice[]'].push($(this).val());
					}
					return;
				});
				$('input[name="SKUnumber[]"]').each(function(index){
					var thatValue = $(this).val();
					if ( !thatValue ) {
						automsgbox('<?php echo (L("_WAP_GOODS_GOODSINVENTORY_")); ?>');
					} else {
						that.dataJson['SKUnumber[]'].push($(this).val());
					}
					return;
				});
				that.dataJson['description'] = $('#description').html();
				that.dataJson['images[]'] = that.goodsImageData;
				$.ajax({
					url: '<?php echo U('Goods/editGoods');?>',
					type: 'POST',
					dataType: 'json',
					data: that.dataJson
				})
				.done(function(returnData) {
					if( returnData['status'] == '200000' ){
						automsgbox('<?php echo (L("_WAP_GOODS_EDITSUCCESS_")); ?>', function(){
							window.location.href = "<?php echo U('Agent/agentGoods');?>";
						});
					} else if ( returnData['status'] == '200001' ) {
						window.location.href = "<?php echo U('GoodsForEdit/payForEdit');?>?order_sn="+ returnData['data']['order_sn'];
					} else {
						automsgbox(returnData['message']);
					}
				});
			},
			loadGoodsInfo() {
				var that = this;
				$.ajax({
					url: '<?php echo U('Goods/goodsInfo');?>',
					type: 'POST',
					dataType: 'json',
					data: {id:"<?php echo ($_GET['id']); ?>"}
				})
				.done(function(returnData) {
					if ( returnData['status'] == '200000' ) {
						var data = returnData['data']['list'];
						that.dataJson = data['goodsInfo'];
						that.dataJson['description'] = data['goodsDesc'];
						that.goodsImageData = data['goodsImages'];
						that.expressType = data['goodsInfo']['express_type'].split(',');
						that.categoryId.push(data['goodsInfo']['category_id']);
						that.agentCategoryId.push(data['goodsInfo']['agent_category_id'])
						// that.goodsCategoryData = returnData['data']['list'];
						if(that.categoryId[0].indexOf($("#goodsCategory_"+that.categoryId[0]).val()) != -1){
							that.categoryName = $("#goodsCategory_"+that.categoryId[0]).parent().prev().text();
						}
						if(that.agentCategoryId[0].indexOf($("#agentGoodsCategory_"+that.agentCategoryId[0]).val()) != -1){
							that.agentCategoryName = $("#agentGoodsCategory_"+that.agentCategoryId[0]).parent().prev().text();
						}
						that.attrData = data['attrData'];
						that.skuData = eval('(' + data['relevanceData'] + ')');
						for (var i = 0; i < that.skuData['relevance_attr'].length; i ++) {
							var thatAttr = that.skuData['relevance_attr'][i];
							that.selectedAttr.push(thatAttr);
							that.selectedAttrStr += thatAttr + '-';
						}
						that.$nextTick(function(){
							that.initializeAttr();
						})
					}
				});
			},
			loadGoodsCategory() {
				var that = this;
				$.ajax({
					url: '<?php echo U('Category/goodsCategory');?>',
					type: 'POST',
					dataType: 'json',
					data: {}
				})
				.done(function(returnData) {
					if ( returnData['status'] == '200000' ) {
						that.goodsCategoryData = returnData['data']['list'];
					}
				});
			},
			loadAgentGoodsCategory() {
				var that = this;
				$.ajax({
					url: '<?php echo U('Category/agentGoodsCategory');?>',
					type: 'POST',
					dataType: 'json',
					data: {id:that.agentId}
				})
				.done(function(returnData) {
					if ( returnData['status'] == '200000' ) {
						that.agentGoodsCategoryData = returnData['data']['list'];
					}
				});
			},
			loadCategoryAttr(category_id) {
				if ( category_id ) {
					var that = this;
					$.ajax({
						url: '<?php echo U('Goods/categoryAttr');?>',
						type: 'POST',
						dataType: 'json',
						data: {category_id:category_id}
					})
					.done(function(returnData) {
						if ( returnData['status'] == '200000' ) {
							that.attrData = returnData['data']['list'];
							if ( that.attrData.length == 0 ) {
								$('#JcombineList').remove();
							} else {
								that.$nextTick(function(){
									that.createCombination();
								})
							}
						}
					});
				}
			},
			showGoodsCategory() {
				$("#goodsCategory").addClass("on");
			},
			showAgentGoodsCategory() {
				$("#agentGoodsCategory").addClass("on");
			},
			selectGoodsCategory(category_name) {
				this.categoryName = category_name;
				if(this.categoryId.length > 1){
					this.categoryId.splice(0,1);
				}
			},
			selectAgentGoodsCategory(category_name) {
				this.agentCategoryName = category_name;
				if(this.agentCategoryId.length > 1){
					this.agentCategoryId.splice(0,1);
				}
			},
			closeGoodsCategory(type) {
				var that = this;
				$("#goodsCategory").removeClass("on");
			},
			closeAgentGoodsCategory(type) {
				var that = this;
				$("#agentGoodsCategory").removeClass("on");
				if ( type == 'finish' && this.agentCategoryId[0] ) {
					this.loadCategoryAttr(this.agentCategoryId[0]);
				}
			},
			selGoodsAttr : function(){
				if ($(event.target)[0].checked) {
					$(event.target).parent().parent('.s_e_box').addClass('on');
				} else {
					$(event.target).parent().parent('.s_e_box').removeClass('on');
				}
				this.createCombination();
			},
			initializeAttr() {
				this.createCombination();
				for (var j in this.skuData['relevance_id']) {
					var skuItem = this.skuData['relevance_id'][j];
					console.log(skuItem);
					$('#JskuPrice_' + skuItem['attr']).val(skuItem['goods_price']);
					$('#JskuNumber_' + skuItem['attr']).val(skuItem['goods_number']);
				}
			},
			combine(arr) {
				if(arr.length > 1) {
			        var len1 = arr[0].length, len2 = arr[1].length, newArr = arr.slice(0), temp = [];
			        for(var i = 0; i < len1; i ++) {
			            for(var j = 0; j < len2; j ++) {
			                temp.push(arr[0][i] + '**' + arr[1][j])
			            }
			        }
			        newArr.splice(0, 2, temp);
			        return arguments.callee(newArr)
			    }
			    return arr[0];
			},
			createCombination : function(){
				var attrData = {};
				$('#JcombineList').remove();
				$('.s_e_box.on').each(function(index) {
					var attr = $(this).find('input[name="attr"]').val();
					var value = $(this).find('input[name="value"]').val();
					var value_name = $(this).find('span').text();
					var attr_name = $(this).find('input[name="attrname"]').val();
					
					if (!attrData[attr]) {
						attrData[attr] = {};
					}
					attrData[attr]['attr_name'] = attr_name;
					attrData[attr]['attr_list'] = attrData[attr]['attr_list'] ? attrData[attr]['attr_list'] : {};
					attrData[attr]['attr_list'][value] = value_name;
				});
				var attrStr = [];
				var attrList = [];
				var allAttr = [];
				var field = document.createElement('div');
				field.id = 'JcombineList';
				for(var i in attrData) {
					if (attrData.hasOwnProperty(i)) {
						attrList.push(i);
					}

					var len = allAttr.length
					allAttr[len] = [];
					for (var j in attrData[i].attr_list) {
						if (attrData[i].attr_list.hasOwnProperty(j)) {
							allAttr[len].push(attrData[i]['attr_list'][j] + '-' + i + '-' + j);
						}
					}
				}
				var combineResult = this.combine(allAttr);
				for (var j = 0; j < combineResult.length; j ++) {
					attrStr.push('<div class="add_edit_g"><ul>');
					var tmp_list = combineResult[j].split('**'),
						multi_attr = ',',
						sku_id = '';
					for (var k = 0; k < tmp_list.length; k ++) {
						var tmp_attr = tmp_list[k].split('-');
						multi_attr += tmp_attr[2] + ',';
						sku_id += tmp_attr[2] + '-';
						attrStr.push('<li> <div class="amid_input"> <span>' + attrData[tmp_attr[1]]['attr_name'] + '</span> <input type="text" value="' + tmp_attr[0] + '" readonly> </div> </li>');
					}
					sku_id = sku_id.substring(0, sku_id.length - 1);
					attrStr.push('<li> <div class="amid_input"> <span><?php echo (L("_WAP_GOODS_PRICE_")); ?></span> <input type="text" placeholder="<?php echo (L("_WAP_GOODS_GOODSPRICE_")); ?>" name="SKUprice[]" id="JskuPrice_' + sku_id + '"> </div> </li>');
					attrStr.push('<li> <div class="amid_input"> <span><?php echo (L("_WAP_AGENT_INSTOCK_")); ?></span> <input type="text" placeholder="<?php echo (L("_WAP_GOODS_GOODSINVENTORY_")); ?>" name="SKUnumber[]" id="JskuNumber_' + sku_id + '"> </div> </li>');
					attrStr.push('<li> <div class="amid_input"> <span><?php echo (L("_COMMON_OPERATE_")); ?></span> <em class="deleteBtn"><?php echo (L("_COMMON_DELETE_")); ?></em> </div> </li>');
					attrStr.push('</ul>');
					attrStr.push('<input type="hidden" name="SKUattr[]" value="' + multi_attr + '" />');
					attrStr.push('</div>');
				}

				field.innerHTML = attrStr.join('');
				var JeditSKU = document.getElementById('JeditSKU');
				JeditSKU.appendChild(field);
				JeditSKU.style.display = 'block';
			}
		}
	})

	// 删除属性
	$('#JeditSKU').on('click', '.deleteBtn', function(){
		$(this).parents('.add_edit_g').remove();
	});

	// 上传图片
	$(document).on('change', '#upload_input', function() {
	    $.ajaxFileUpload({
	        url: "<?php echo U('Goods/photoUpload');?>",
	        secureuri: false,
	        fileElementId: 'upload_input',
	        dataType: 'json',
	        success: function (data, status) {
	            if(data.error != '') {
	                automsgbox(data.error);
	            } else {
	                vm.goodsImageData.push(data.url);
	            }
	        },error: function (data, status, e) {
	            automsgbox(e);
	        }
	    });
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