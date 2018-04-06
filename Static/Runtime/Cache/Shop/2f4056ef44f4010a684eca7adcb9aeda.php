<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo (L("_PC_ORDER_LIST_")); ?></title>
    <meta name="renderer" content="webkit">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit">
    <link rel="shortcut icon" href="/Static/Public/xcrozz.ico" type="image/x-icon">
    <link href="/Static/Public/Shop/css/base.css" rel="stylesheet">
    <link href="/Static/Public/Shop/css/style.css" rel="stylesheet">
    <script src="/Static/Public/Wechat/js/common.js" type="text/javascript"></script>
    <script src="/Static/Public/Shop/js/baiduTemplate.js" type="text/javascript"></script>
    <script src="/Static/Public/Wechat/js/jquery-1.8.3.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="/Static/Public/Admin/css/style.default.css" type="text/css" />
    <link rel="shortcut icon" href="/Static/Public/xcrozz.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/Static/Public/Admin/css/plugins/uniform.tp.css" />
    <link rel="stylesheet" href="/Static/Public/Admin/css/pop.css" type="text/css" />
    
	<link rel="stylesheet" href="/Static/Public/Admin/css/jquery.datetimepicker.css" type="text/css" />
	<link rel="stylesheet" href="/Static/Public/Shop/css/messagebox.css" type="text/css" />
	<style type="text/css">
		.stdtable th, .stdtable td{text-align:center;}
		.txtEdit{display:inline-block;width:35px;height:35px;background:url(/Static/Public/Shop/images/edit_ico.png) center center no-repeat;background-size:25px;vertical-align:middle;cursor:pointer;}
		.txtEdit.on{background:url(/Static/Public/Shop/images/finish_ico.png) center center no-repeat;background-size:25px;}
		.intxt.on{border:1px solid #ccc;box-shadow: inset 0 1px 3px #ddd;-webkit-box-shadow: inset 0 1px 3px #ddd;-moz-box-shadow: inset 0 1px 3px #ddd;}
	</style>

</head>
    <body class="withvernav">
        <div class="bodywrapper">
            <div class="topheader">
                <div class="left" style="color:#fff">
                    <ul>
                        <li><?php echo (L("_PC_SETTING_WELCOME_BACK_")); ?> <?php echo ($_SESSION['userInfo']['nickname']); ?></li>
                    </ul>
                </div>
            </div>
            
            <div class="header">
                <ul class="headermenu">
	<li class="<?php if(in_array(CONTROLLER_NAME, array('Agent', 'Goods')) && in_array(ACTION_NAME, array('agentGoodsCategory', 'agentGoods', 'agentGoodsComment', 'addGoods', 'editGoods', 'goodsDetail'))): ?>current<?php endif; ?>">
		<a href="<?php echo U('Agent/agentGoodsCategory');?>">
			<span class="icon icon-message"></span>
			<span class="tet"><?php echo (L("_PC_TOP_MENU_GOODS_MANAGER_")); ?></span>
		</a>
		<em></em>
	</li>

	<li class="<?php if(in_array(CONTROLLER_NAME, array('Order'))): ?>current<?php endif; ?>">
		<a href="<?php echo U('Order/orderList');?>">
			<span class="icon icon-orders"></span>
			<span class="tet"><?php echo (L("_PC_TOP_MENU_ORDER_MANAGER_")); ?></span>
		</a>
		<em></em>
	</li>

	<li class="<?php if(in_array(CONTROLLER_NAME, array('Message', 'MessageCheck'))): ?>current<?php endif; ?>">
		<a href="<?php echo U('Message/index');?>">
			<span class="icon icon-pencil"></span>
			<span class="tet"><?php echo (L("_PC_TOP_MENU_MESSAGE_MANAGER_")); ?></span>
		</a>
		<em></em>
	</li>

	<li class="<?php if(in_array(CONTROLLER_NAME, array('Point', 'Agent', 'PointOrder', 'Collect')) && in_array(ACTION_NAME, array('pointGoods', 'pointLog', 'myPointGoods', 'goodsDetail', 'orderDetail', 'pointOrder', 'pointInfo', 'pointOrderDetail', 'collectGoods'))): ?>current<?php endif; ?>">
		<a href="<?php echo U('Point/pointGoods');?>">
			<span class="icon icon-point"></span>
			<span class="tet"><?php echo (L("_PC_TOP_MENU_MY_POINT_")); ?></span>
		</a>
		<em></em>
	</li>

	<li class="<?php if(in_array(CONTROLLER_NAME, array('Bidding', 'Agent', 'User', 'GoodsCheck')) && in_array(ACTION_NAME, array('biddingBanner', 'settlementLog', 'withdraw', 'withdrawSuccess', 'goodsToAuth', 'goodsToTop', 'biddingRecord', 'biddingBanner', 'biddingIndexGoods', 'biddingFavorableGoods', 'biddingAgent', 'platformBiddingRecord', 'toBiddingIndexGoods', 'toBiddingFavorableGoods', 'toBiddingAgent', 'toBiddingBanner', 'payForBidding', 'basicData', 'editBasicData'))): ?>current<?php endif; ?>">
		<a href="<?php echo U('Agent/basicData');?>">
			<span class="icon icon-admin"></span>
			<span class="tet"><?php echo (L("_PC_TOP_MENU_UESR_CENTER_")); ?></span>
		</a>
		<em></em>
	</li>
	
	<li class="<?php if(in_array(CONTROLLER_NAME, array('Agent', 'User', 'Article')) && in_array(ACTION_NAME, array('chooseLanguage', 'changePassword', 'userFeedback', 'feedbackSuccess', 'articleDetail'))): ?>current<?php endif; ?>">
		<a href="<?php echo U('Agent/changePassword');?>">
			<span class="icon icon-system"></span>
			<span class="tet"><?php echo (L("_PC_TOP_MENU_SETTING_")); ?></span>
		</a>
		<em></em>
	</li>

	<li>
		<a href="<?php echo U('Login/logout');?>">
			<span class="icon icon-exit"></span>
			<span class="tet"><?php echo (L("_PC_TOP_MENU_LOGOUT_")); ?></span>
		</a>
		<em></em>
	</li>
</ul>
            </div>
            <div class="main-date-lr">
              <div class="vernav2 iconmenu">
                
	<ul>
	<li class="current">
		<ul class="Jcon-ctr">
			<li class="<?php if(in_array(CONTROLLER_NAME, array('Order')) && in_array(ACTION_NAME, array('orderList', 'orderDetail'))): ?>on<?php endif; ?>">
				<a href="<?php echo U('Order/orderList');?>"><?php echo (L("_PC_ORDER_LIST_")); ?></a>
			</li>
		</ul>
	</li>
</ul>

                <a class="togglemenu"></a>
                <br /><br />
            </div>
            <div class="centercontent">
                
	 <div class="pageheader">
	    <h1 class="pagetitle"><?php echo (L("_PC_ORDER_LIST_")); ?></h1>
	    <span class="pagedesc"></span>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form class="order-list">
			<p>
				<label><?php echo (L("_COMMON_ORDER_NUMBER_")); ?>：</label>
				<input type="text" name="order_sn" value="<?php echo ($return['order_sn']); ?>" placeholder="<?php echo (L("_COMMON_PLEASE_SEARCH_")); echo (L("_COMMON_ORDER_NUMBER_")); ?>">
				&nbsp;&nbsp;
				<label><?php echo (L("_COMMON_ORDER_STATUS_")); ?>：</label>
				<select name="order_type">
					<option value="all" <?php if($return['order_type'] == 'all'): ?>selected<?php endif; ?>><?php echo (L("_COMMON_ALL_")); ?></option>
					<option value="toBePaid" <?php if($return['order_type'] == 'toBePaid'): ?>selected<?php endif; ?>><?php echo (L("_PC_POINT_PENDING_PAYMENT_")); ?></option>
					<option value="toBeShipped" <?php if($return['order_type'] == 'toBeShipped'): ?>selected<?php endif; ?>><?php echo (L("_PC_POINT_TO_BE_DELIVERED_")); ?></option>
					<option value="toBeReceived" <?php if($return['order_type'] == 'toBeReceived'): ?>selected<?php endif; ?>><?php echo (L("_PC_POINT_TO_BE_RECEIVED_")); ?></option>
					<option value="toBeComment" <?php if($return['order_type'] == 'toBeComment'): ?>selected<?php endif; ?>><?php echo (L("_PC_POINT_TO_BE_REVIEW_")); ?></option>
				</select>
				&nbsp;&nbsp;
				<label><?php echo (L("_COMMON_DELIVERY_METHOD_")); ?>：</label>
				<select name="express_type">
					<option value="-1"><?php echo (L("_COMMON_ALL_")); ?></option>
					<option value="0" <?php if($return['express_type'] == '0'): ?>selected<?php endif; ?>><?php echo (L("_COMMON_BY_DELIVERY_")); ?></option>
					<option value="1" <?php if($return['express_type'] == '1'): ?>selected<?php endif; ?>><?php echo (L("_COMMON_COLLECT_FROM_MERCHANT_")); ?></option>
				</select>
				&nbsp;&nbsp;
				<label><?php echo (L("_COMMON_ORDER_TIME_")); ?>：</label>
				<input type="text" name="" id="staDatartTime" date-time="<?php echo ($return['startTime']); ?>">
				<input type="hidden" name="startTime" id="startTime" value="<?php echo ($return['startTime']); ?>" >
				-
				<input type="text" name="" id="endDataTime" date-time="<?php echo ($return['endTime']); ?>">
				<input type="hidden" name="endTime" id="endTime" value="<?php echo ($return['endTime']); ?>">
				&nbsp;&nbsp;
				<input type="submit" value="<?php echo (L("_COMMON_FILTER_")); ?>">
			</p>
		</form>
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="template"></table>
        <div class="page-box" id="pageBox"></div>
        <div class="mask"></div>
	    <div class="replay_m">
	        <p id="Ktitle"></p>
	        <textarea placeholder="" id="Kcontent"></textarea>
	        <div class="rbtn">
	            <span class="Kcancel" style="margin-right:15px;"><?php echo (L("_COMMON_BUTTON_NO_")); ?></span>
	            <span class="Ksure"><?php echo (L("_COMMON_BUTTON_YES_")); ?></span>
	        </div>
	    </div>
	</div>

            </div>
            </div>
        </div>
        
        <script src="/Static/Public/Admin/js/pop.js" type="text/javascript"></script>
        <script type="text/javascript">
            function msgBox(title, content, time) {
                var _title = title ? title : '提示';
                var _time = time ? time : 1500;

                popwin(_title, content);
                setTimeout(function() {
                    window.location.href = window.location.href;
                }, _time);
            }
             /*
             * loading弹窗
             * 打开loading:  popupWin.show(msg)
             * 关闭loading:  popupWin.hide()
             */
            var popupWin = (function() {
                var popup =  $('<div class="popup"></div>').hide(),
                    content = $('<div class="popup-content"></div>'),
                    text = $('<span class="content-inner"></span>');

                $('body').append(popup.append(content.append(text)));

                return {
                    show: function(msg) {
                        text.html(msg);
                        popup.show();
                    },
                    hide: function() {
                        popup.hide();
                    }
                }
            })();

            function maskFade(type) {
                (type == '1') ? $(".mask").fadeIn() : $(".mask").fadeOut();
                (type == '1') ? $(".replay_m").fadeIn() : $(".replay_m").fadeOut();
            }

            function messageBox(title, placeholder, callback, needTextarea) {
                if ( needTextarea ) {
                    $('#Ktitle').next().remove();
                    $('#Ktitle').after(placeholder);
                } else {
                    $('#Kcontent').attr('placeholder', placeholder);
                }
                maskFade(1);
                $('#Ktitle').text(title);
                var ctr = 1;
                $('.rbtn .Ksure').click(function() {
                    maskFade(0);
                    if (callback && ctr == 1) {
                        ctr = 0;
                        callback();
                    }
                });
                $('.Kcancel').click(function() {
                    maskFade(0);
                    ctr = 0;
                });
                $(".mask").click(function(){
                    maskFade(0);
                });
            }
        </script>
        
<script type="text/javascript" src="/Static/Public/Admin/js/jquery.datetimepicker.js"></script>
<script type="text/javascript" src="/Static/Public/Admin/js/moment.min.js"></script>
<script id="orderList_tpl" type="text/html">
<tr>
	<!-- <th><?php echo (L("_COMMON_NO_")); ?></th> -->
	<th><?php echo (L("_COMMON_ORDER_NUMBER_")); ?></th>
	<th><?php echo (L("_COMMON_PRODUCTS_NAME_")); ?></th>
	<th><?php echo (L("_PC_ORDER_PRODUCT_MODEL_")); ?></th>
	<th><?php echo (L("_COMMON_QUANTITY_")); ?></th>
	<th><?php echo (L("_COMMON_PRICE_PER_UNIT_")); ?></th>
	<th><?php echo (L("_COMMON_SUBTOTAL_")); ?></th>
	<th><?php echo (L("_COMMON_ORDER_TIME_")); ?></th>
	<th><?php echo (L("_COMMON_UESR_NAME_")); ?></th>
	<th><%=delivery%></th>
	<th><?php echo (L("_COMMON_ORDER_STATUS_")); ?></th>
	<th><?php echo (L("_PC_ORDER_REMARKS_")); ?></th>
	<th width="10%"><?php echo (L("_COMMON_OPERATE_")); ?></th>
</tr>
<%for(var i = 0; i < list.length; i ++){%>
	<%
		var data = list[i],
			date = new Date(data['add_time'] * 1000),
			goodsList = data['goodsList'],
			rowspan = goodsList.length;
		for ( var j = 0; j < rowspan; j ++ ) {
			var goodsData = goodsList[j];
	%>
		<tr>
			<!-- <td><%=data['id']%></td> -->
			<%if(j==0){%>
				<td rowspan="<%=rowspan%>"><%=data['order_sn']%></td>
			<%}%>
			<td><%=goodsData['goods_name']%></td>
			<td><%=goodsData['attr_list']%></td>
			<td>*<%=goodsData['goods_number']%></td>
			<td><%=goodsData['unit_price']%></td>
			<%if(j==0){%>
				<td rowspan="<%=rowspan%>"><%=goodsData['price']%></td>
				<td rowspan="<%=rowspan%>"><%=date.pattern('yyyy-MM-dd HH:mm:ss')%></td>
				<td rowspan="<%=rowspan%>"><%=data['nickname']%></td>
				<td rowspan="<%=rowspan%>">
					<%if( data['express_type'] == '1' ) {%>
						<%=collectFromMerchant%>
					<%}else{%>
						<%=byDelivery%>
					<%}%>
				</td>
				<td rowspan="<%=rowspan%>">
					<%switch( data['status'] ) {
						case '0' :%>
							<?php echo (L("_PC_POINT_PENDING_PAYMENT_")); ?>
						<%break;
						case '1' :%>
							<?php echo (L("_PC_POINT_TO_BE_DELIVERED_")); ?>
						<%break;
						case '2' :%>
							<?php echo (L("_PC_POINT_TO_BE_RECEIVED_")); ?>
						<%break;
						case '3' :%>
							<?php echo (L("_PC_POINT_TO_BE_REVIEW_")); ?>
						<%break;
						case '4' :%>
							<?php echo (L("_PC_POINT_IS_FINISHED_")); ?>
						<%break;
						case '5' :%>
							<?php echo (L("_PC_POINT_IS_CANCELED_")); ?>
						<%break;%>
					<%}%>
				</td>
				<td rowspan="<%=rowspan%>">
					<input type="text" id="Korder_<%=data['order_sn']%>" value="<%=data['remark']%>" readonly="readonly" class="intxt" style="border:none;box-shadow:none;-webkit-box-shadow:none;-moz-box-shadow:none;text-align:center;">
					<em class="txtEdit Kremark" data-sn="<%=data['order_sn']%>"></em>
				</td>
				<td rowspan="<%=rowspan%>" class="center">
					<%if ( data['status'] == '1' ) {%>
						<a class="stdbtn btn_lime Kdelivery" data-sn="<%=data['order_sn']%>" href="javascript:;"><%=toDelivery%></a>
						<!-- <a class="stdbtn btn_lime Kcancel" data-sn="<%=data['order_sn']%>" href="javascript:;"><%=toCancel%></a> -->
					<%}%>
					<a class="stdbtn btn_lime" href="<%=jumpUrl%>?order_sn=<%=data['order_sn']%>"><?php echo (L("_COMMON_ORDER_DETAILS_")); ?></a>
				</td>
			<%}%>
		</tr>
	<%}%>
<%}%>
</script>
<script type="text/javascript">
	/*使用模板引擎*/
    var bt = baidu.template,
    	order_sn = "<?php echo ($return['order_sn']); ?>",
    	express_type = "<?php echo ($return['express_type']); ?>",
    	order_type = "<?php echo ($return['order_type']); ?>",
    	startTime = "<?php echo ($return['startTime']); ?>",
    	endTime = "<?php echo ($return['endTime']); ?>",order_sn,remark;
    var editbol = true;//控制修改还是完成状态
    function loadData(page) {
    	popupWin.show('<?php echo (L("_COMMON_LOADING_")); ?>');
    	$.ajax({
			url: '<?php echo U('Order/orderList');?>',
			type: 'POST',
			dataType: 'json',
			data: {
				page : page,
				type : 1,
				user_type : 'saler',
				order_sn : order_sn,
				order_type : order_type,
				express_type : express_type,
				startTime : startTime,
				endTime : endTime,
			}
		})
		.done(function(returnData) {
			if ( returnData['data']['list'].length ) {
				returnData['data']['toDelivery'] = "<?php echo (L("_WAP_ORDER_DELIVERED_")); ?>";
				returnData['data']['toCancel'] = "<?php echo (L("_WAP_ORDER_CANCEL_ORDER_")); ?>";
				returnData['data']['byDelivery'] = "<?php echo (L("_COMMON_BY_DELIVERY_")); ?>";
				returnData['data']['collectFromMerchant'] = "<?php echo (L("_COMMON_COLLECT_FROM_MERCHANT_")); ?>";
				returnData['data']['delivery'] = "<?php echo (L("_COMMON_DELIVERY_METHOD_")); ?>";
				returnData['data']['jumpUrl'] = "<?php echo U('Order/orderDetail');?>";
				var html = bt('orderList_tpl', returnData['data']);
				$('#template').html(html);
				createPageTags(returnData['data']['page'], returnData['data']['count'], 0);
				listenPageEvent(loadData);
			} else {
				alert('<?php echo (L("_COMMON_NO_DATA_")); ?>');
			}
			popupWin.hide();
		});
    }
    loadData(1);

    // 发货
    $('#template').on('click', '.Kdelivery', function(){
    	var title = $(this).text(),
    		order_sn = $(this).data('sn')
    	messageBox(title, '<p><?php echo (L("_COMMON_TO_DELIVERY_")); ?></p>', function(){
	    	$.ajax({
				url: '<?php echo U('Order/delivery');?>',
				type: 'POST',
				dataType: 'json',
				data: {order_sn: order_sn}
			})
			.done(function(returnData) {
				if ( returnData['status'] == '200000' ) {
					alert(returnData['message']);
					window.location.href = window.location.href;
				} else {
					alert(returnData['message']);
				}
			});
    	},1);
    });

    function saveRemark() {
    	$.ajax({
			url: '<?php echo U('Order/saveRemark');?>',
			type: 'POST',
			dataType: 'json',
			data: {order_sn:order_sn,remark:remark}
		})
		.done(function(returnData) {
			if ( returnData['status'] == '200000' ) {
				alert(returnData['message']);
				window.location.href = window.location.href;
			} else {
				alert(returnData['message']);
			}
		});
    }

    // 编辑备注
    $("#template").on('click','.Kremark',function(e){
    	if(editbol){
    		//进入编辑
	    	$(this).addClass("on");
	    	$(this).siblings("input").css({"border":"1px solid #ccc","box-shadow":"inset 0 1px 3px #ddd","-webkit-box-shadow":"inset 0 1px 3px #ddd","-moz-box-shadow":"inset 0 1px 3px #ddd"});
	    	$(this).siblings("input").attr("readonly",false);
	    	editbol = false;
    	}else{
    		//完成编辑
    		order_sn = $(this).data('sn');
    		remark = $('#Korder_' + order_sn).val();
    		saveRemark();
    		$(this).removeClass('on');
    		$(this).siblings("input").css({"border":"none","box-shadow":"none","-webkit-box-shadow":"none","-moz-box-shadow":"none"});
    		$(this).siblings("input").attr("readonly",true);
    		editbol = true;
    	}
    })

    // 取消订单
    $('#template').on('click', '.Kcancel', function(){
    	var title = $(this).text(),
    		order_sn = $(this).data('sn')
    	messageBox(title, '<p><?php echo (L("_COMMON_TO_CANCEL_")); ?></p>', function(){
	    	$.ajax({
				url: '<?php echo U('Order/merchantCancel');?>',
				type: 'POST',
				dataType: 'json',
				data: {order_sn: order_sn}
			})
			.done(function(returnData) {
				if ( returnData['status'] == '200000' ) {
					alert(returnData['message']);
					window.location.href = window.location.href;
				} else {
					alert(returnData['message']);
				}
			});
    	},1);
    });

    $('#staDatartTime').val(moment.unix($('#staDatartTime').attr('date-time')).format("YYYY-MM-DD HH:mm:ss"));
	$('#endDataTime').val(moment.unix($('#endDataTime').attr('date-time')).format("YYYY-MM-DD HH:mm:ss"));
	$('#staDatartTime').datetimepicker({
		format:"Y-m-d H:i:s", 
		onChangeDateTime:function(dp, $input) {
			$('#startTime').val(moment($input.val()).unix().valueOf());
		}
	});
	$('#endDataTime').datetimepicker({
		format:"Y-m-d H:i:s", 
		onChangeDateTime:function(dp, $input) {
			$('#endTime').val(moment($input.val()).unix().valueOf());
		}
	});
</script>

    </body>
</html>