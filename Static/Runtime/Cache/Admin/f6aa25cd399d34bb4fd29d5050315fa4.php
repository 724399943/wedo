<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>订单列表</title>
    <link rel="stylesheet" href="/Static/Public/Admin/css/style.default.css" type="text/css" />
    <link rel="shortcut icon" href="/Static/Public/xcrozz.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/Static/Public/Admin/css/plugins/uniform.tp.css" />
    <link rel="stylesheet" href="/Static/Public/Admin/css/pop.css" type="text/css" />
    
	<link rel="stylesheet" href="/Static/Public/Admin/css/jquery.datetimepicker.css" type="text/css" />

</head>

<body class="withvernav">
    <div class="bodywrapper">
        <div class="topheader">
            <div class="left" style="color:#fff">
                <ul>
                    <li>欢迎你 <?php echo (session('adminAccount')); ?></li>
                </ul>
            </div>
        </div>
        
        <div class="header">
        	<ul class="headermenu">
	<?php if(checkControllerAuth('Index')): ?><li class="<?php if(in_array(CONTROLLER_NAME, array('Index','Keyword'))): ?>current<?php endif; ?>">
			<a href="<?php echo getAuthUrl(array('Index-systemInfo','Index-statistics', 'Index-clearCache'));?>">
				<span class="icon icon-console"></span>
				<span class="tet">控制台</span>
			</a>
			<em></em>
		</li><?php endif; ?>

	<?php if(checkControllerAuth('Auth', 'Admin')): ?><li class="<?php if(in_array(CONTROLLER_NAME, array('Auth', 'Admin', 'Feedback'))): ?>current<?php endif; ?>">
			<a href="<?php echo getAuthUrl(array('Admin-adminList', 'Admin-addAdmin', 'Auth-roleList', 'Auth-addRole', 'Feedback-feedbackList'));?>">
				<span class="icon icon-admin"></span>
				<span class="tet">管理员中心</span>
			</a>
			<em></em>
		</li><?php endif; ?>

	<?php if(checkControllerAuth(array('Article'))): ?><li class="<?php if(in_array(CONTROLLER_NAME, array('Article'))): ?>current<?php endif; ?>">
			<a href="<?php echo getAuthUrl(array('Article-index'));?>">
				<span class="icon icon-content"></span>
				<span class="tet">内容管理</span>
			</a>
			<em></em>
		</li><?php endif; ?>

	<?php if(checkControllerAuth(array('User'))): ?><li class="<?php if(in_array(CONTROLLER_NAME, array('User'))): ?>current<?php endif; ?>">
			<a href="<?php echo getAuthUrl(array('User-userList'));?>">
				<span class="icon icon-users"></span>
				<span class="tet">用户管理</span>
			</a>
			<em></em>
		</li><?php endif; ?>

	<?php if(checkControllerAuth(array('Goods', 'Category', 'GoodsCheck', 'Bidding', 'AgentCategory'))): ?><li class="<?php if(in_array(CONTROLLER_NAME, array('Goods', 'Category', 'GoodsCheck', 'Bidding','AgentCategory', 'Ad'))): ?>current<?php endif; ?>">
			<a href="<?php echo getAuthUrl(array('AgentCategory-agentCategory'));?>">
				<span class="icon icon-message"></span>
				<span class="tet">商城管理</span>
			</a>
			<em></em>
		</li><?php endif; ?>

	<?php if(checkControllerAuth(array('Order'))): ?><li class="<?php if(in_array(CONTROLLER_NAME, array('Order','GoodsComment'))): ?>current<?php endif; ?>">
			<a href="<?php echo getAuthUrl(array('Order-orderList'));?>">
				<span class="icon icon-orders"></span>
				<span class="tet">订单管理</span>
			</a>
			<em></em>
		</li><?php endif; ?>

	<?php if(checkControllerAuth(array('Money'))): ?><li class="<?php if(in_array(CONTROLLER_NAME, array('Money'))): ?>current<?php endif; ?>">
			<a href="<?php echo getAuthUrl(array('Money-customerRechargeLog'));?>">
				<span class="icon icon-chart"></span>
				<span class="tet">财务管理</span>
			</a>
			<em></em>
		</li><?php endif; ?>

	<?php if(checkControllerAuth(array('Credit'))): ?><li class="<?php if(in_array(CONTROLLER_NAME, array('Credit'))): ?>current<?php endif; ?>">
			<a href="<?php echo getAuthUrl(array('Credit-creditSetting'));?>">
				<span class="icon icon-credit"></span>
				<span class="tet">信用管理</span>
			</a>
			<em></em>
		</li><?php endif; ?>

	<?php if(checkControllerAuth(array('Point', 'PointOrder'))): ?><li class="<?php if(in_array(CONTROLLER_NAME, array('Point', 'PointOrder'))): ?>current<?php endif; ?>">
			<a href="<?php echo getAuthUrl(array('Point-pointGoodsList'));?>">
				<span class="icon icon-point"></span>
				<span class="tet">积分管理</span>
			</a>
			<em></em>
		</li><?php endif; ?>

	<?php if(checkControllerAuth(array('Agent'))): ?><li class="<?php if(in_array(CONTROLLER_NAME, array('Agent'))): ?>current<?php endif; ?>">
			<a href="<?php echo getAuthUrl(array('Agent-checkAgentList'));?>">
				<span class="icon icon-store"></span>
				<span class="tet">商家管理</span>
			</a>
			<em></em>
		</li><?php endif; ?>

	<?php if(checkControllerAuth('System', 'Message')): ?><li class="<?php if(in_array(CONTROLLER_NAME, array('System', 'Message'))): ?>current<?php endif; ?>">
			<a href="<?php echo getAuthUrl(array('System-setting', 'Message-messageCheckList'));?>">
				<span class="icon icon-system"></span>
				<span class="tet">系统设置</span>
			</a>
			<em></em>
		</li><?php endif; ?>

	<li>
		<a href="<?php echo U('Login/logout');?>">
			<span class="icon icon-exit"></span>
			<span class="tet">退出登录</span>
		</a>
		<em></em>
	</li>
</ul>
        </div>
        <div class="main-date-lr">
          <div class="vernav2 iconmenu">
            
	<ul>
	<?php if(checkControllerAuth(array('Order'))): ?><li <?php if(in_array(ACTION_NAME, array('orderList'))): echo chr(32);?>class="current"<?php endif; ?>>
			<a class="date-tit sys-tj" href="<?php echo getAuthUrl(array('Order-orderList'));?>" class="addons">订单管理</a>
			<ul class="Jcon-ctr">
				<?php if(checkActionAuth('Order-orderList')): ?><li class="<?php if(in_array(ACTION_NAME, array('orderList', 'orderDetail'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('Order/orderList');?>">订单列表</a>
					</li><?php endif; ?>

				<?php if(checkActionAuth('GoodsComment-goodsComment')): ?><li class="<?php if(in_array(ACTION_NAME, array('goodsComment'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('GoodsComment/goodsComment');?>">评价管理</a>
					</li><?php endif; ?>
			</ul>
		</li><?php endif; ?>
</ul>

            <a class="togglemenu"></a>
            <br /><br />
        </div>
        <div class="centercontent">
            
	 <div class="pageheader">
	    <h1 class="pagetitle">订单列表</h1>
	    <span class="pagedesc"></span>
	    <p class="select-style1">
			<?php if(checkActionAuth('Order-export')): ?><span class="stdbtn btn_lime" style="cursor: pointer; float:right"; id="btn-export">订单导出</span><?php endif; ?>
			
		</p>	
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form method="get" action="<?php echo U('Order/orderList');?>" class="order-list">
			<p class="select-style1">
				<label style="margin-right:3px">订单号：</label>
				<input type="text" name="order_sn" placeholder="输入搜索订单号" value="<?php echo ($return['order_sn']); ?>">
				
				<label>店铺名称：</label>
				<input type="text" name="agent_name" placeholder="输入搜索店铺名称" value="<?php echo ($return['agent_name']); ?>">
			
			    <label>配送方式：</label>
				<select name="express_type">
					<option value="-1">请选择</option>
					<option value="0" <?php if($return['express_type'] == '0' ): ?>selected<?php endif; ?>>送货上门</option>
					<option value="1" <?php if($return['express_type'] == '1' ): ?>selected<?php endif; ?>>到店取货</option>
				</select> 

				<label>订单状态：</label>
				<select name="status">
					<option value="-1">请选择</option>
					<option value="0" <?php if($return['status'] == '0' ): ?>selected<?php endif; ?>>已取消</option>
					<option value="1" <?php if($return['status'] == '1' ): ?>selected<?php endif; ?>>待付款</option>
					<option value="2" <?php if($return['status'] == '2' ): ?>selected<?php endif; ?>>待发货</option>
					<option value="3" <?php if($return['status'] == '3' ): ?>selected<?php endif; ?>>待评价</option>
					<option value="4" <?php if($return['status'] == '4' ): ?>selected<?php endif; ?>>待收货</option>
					<option value="5" <?php if($return['status'] == '5' ): ?>selected<?php endif; ?>>已收货</option>
					
				</select>
				
			<p class="select-style1">
				<label>下单日期：</label>
				<input type="text" name="" id="staDatartTime" date-time="<?php echo ($return['startTime']); ?>" value="" style="margin-right: 0px">
				<input type="hidden" name="startTime" id="startTime" value="<?php echo ($return['startTime']); ?>" >
				-
				<input type="text" name="" id="endDataTime" date-time="<?php echo ($return['endTime']); ?>" value="">
				<input type="hidden" name="endTime" id="endTime" value="<?php echo ($return['endTime']); ?>">
				
				<input type="submit" value="搜索">
			</p>
		</form>
		
		<form action="" method="post">
			<table cellpadding="0" cellspacing="0" border="0" class="stdtable order-table">

				<thead>
					<tr>
						<th><input type="checkbox" id = "ids"/></th>
						<th>序号</th>
						<th>订单编号</th>
						<th>商品名称</th>
						<th>商品型号</th>
						<th>数量</th>
						<th>商品单价</th>
						<th>实付金额</th>
						<th>下单时间</th>
						<th>用户昵称</th>
						<th>配送方式</th>
						<th>订单状态</th>
						<th>店铺名称</th>
						<th>操作</th>
					</tr>
				</thead>
				<?php if(empty($orderList)): ?><tr>
						<td colspan="13">没有商品列表~！</td>
					</tr>
				<?php else: ?>
				<tbody>
				  <?php if(is_array($orderList)): $i = 0; $__LIST__ = $orderList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><tr class="tr-th">
						<td><input type="checkbox" class="ids" value="<?php echo ($list['id']); ?>"/></td>
						<td><a href="javascript:;" class="open" date-id="<?php echo ($list['order_sn']); ?>"><?php echo ($list['id']); ?></a></td>
						<td><?php echo ($list['order_sn']); ?></td>
						<td><?php echo ($list['goods_name']); ?></td>
						<td>
							<?php switch($list['goods_type']): case "0": ?>普通商品<?php break;?>
						 		<?php case "1": ?>赠品<?php break;?>
						 		<?php case "2": ?>套餐<?php break; endswitch;?>
						</td>
						<td><?php echo ($list['goods_number']); ?></td>
						<td><?php echo ($list['unit_price']); ?></td>
						<td><?php echo ($list['price']); ?></td>
						<td><?php echo (time_format($list['add_time'])); ?></td>
						<td><?php echo ($list['nickname']); ?></td>
						<td><?php if($list['express_type'] == '1'): ?>到店取货<?php else: ?>送货上门<?php endif; ?></td>
						<td>
							<?php if($list['is_out_date'] == 1): ?>已取消
								<?php elseif($list['is_pay'] == 0 AND $list['is_out_date'] == 0): ?>
									待付款
								<?php elseif($list['is_pay'] == 1 AND $list['delivery_status'] == 0 ): ?>
								 	待发货
								<?php elseif($list['is_pay'] == 1 AND $list['delivery_status'] == 1 AND $list['received'] == 1 AND $list['is_comment'] == 0 ): ?>
								 	待评价
								<?php elseif($list['is_pay'] == 1 AND $list['delivery_status'] == 1 AND $list['received'] == 0): ?>
									待收货
								<?php else: ?>
									已收货<?php endif; ?>
						</td>
						<td><?php echo ($list['agent_name']); ?></td>
						<td>
						<?php if(checkActionAuth('Order-orderDetail')): ?><a href="<?php echo U('Order/orderDetail', array('id' => $list['id']));?>">查看详情</a>
							<?php else: ?>
								无权限操作<?php endif; ?>
						</td>
					</tr>
					<tr class="tr-tc1">
						<td colspan="16">
							<table style="background:rgba(0, 210, 255, 0.3);">
								<tr>
									<td>
										<!-- 商品价格 -->
										<table>
											<tr>
												<td>商品总额</td>
												<td>RM<?php echo ($list['goodsListPrice']); ?></td>
											</tr>
											<tr>
												<td>配送费用</td>
												<td><?php echo ($list['carriage']); ?></td>
											</tr>
											<tr>
												<td>订单减免</td>
												<td><?php echo ($list['order_discount_money']); ?></td>
											</tr>
											<tr>
												<td>订单总额</td>
												<td>RM<?php echo ($list['total']); ?></td>
											</tr>
											<tr>
												<td>已支付金额</td>
												<td>RM<?php echo ($list['pay_total']); ?></td>
											</tr>
											<tr>
												<td>积分抵扣金额</td>
												<td>(NULL)</td>
											</tr>
										</table>
									</td>
									<td>
										<!-- 订单其他信息 -->
										<table>
											<tr>
												<td>配送方式</td>
												<td><?php if($list['express_type'] == 1): ?>顾客自提<?php else: ?>送货上门<?php endif; ?></td>
											</tr>
											<tr>
												<td>配送保价</td>
												<td>(NULL)</td>
											</tr>
											<tr>
												<td>商品重量</td>
												<td>(NULL)</td>
											</tr>
											<tr>
												<td>支付方式</td>
												<td><?php if($list['is_pay'] == 1): if($list['pay_type'] == 1): ?>在线支付<?php else: ?>货到付款<?php endif; else: ?>未支付<?php endif; ?></td>
											</tr>
											<tr>
												<td>可得积分</td>
												<td>(NULL)</td>
											</tr>
										</table>
									</td>
									<td>
										<!-- 购买人信息 -->
										<table>
											<tr>
												<td>用户名</td>
												<td><?php echo ($list['unickname']); ?></td>
											</tr>
											<tr>
												<td>姓名</td>
												<td><?php echo ($list['uname']); ?></td>
											</tr>
											<tr>
												<td>电话</td>
												<td><?php echo ($list['uphone']); ?></td>
											</tr>
											<tr>
												<td>地区</td>
												<td>
													<?php echo getRegionName($list['uprovince']);?>
													<?php echo getRegionName($list['ucity']);?>
													<?php echo getRegionName($list['ucounty']);?>
												</td>
											</tr>
											<tr>
												<td>email</td>
												<td><?php echo ($list['uemail']); ?></td>
											</tr>
										</table>
									</td>
									<td>
										<!-- 收货人信息 -->
										<table>
											<tr>
												<td>发货日期</td>
												<td>
												<?php if($list['delivery_time']): echo time_format($list['delivery_time']);?>
													<?php else: ?>
													未发货<?php endif; ?>
												</td>
											</tr>
											<tr>
												<td>姓名</td>
												<td><?php echo ($list['consignee']); ?></td>
											</tr>
											<tr>
												<td>手机</td>
												<td><?php echo ($list['telephone']); ?></td>
											</tr>
											<tr>
												<td>地区</td>
												<td>
													<?php echo getRegionName($list['province']);?>
													<?php echo getRegionName($list['city']);?>
													<?php echo getRegionName($list['county']);?>
												</td>
											</tr>
											<tr>
												<td>地址</td>
												<td><?php echo ($list['address']); ?></td>
											</tr>
											<tr>
												<td>邮编</td>
												<td><?php echo ($list['zipcode']); ?></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr class="tr-tc2">
						<td colspan="16">
							<table style="background:rgba(0, 128, 255, 0.42);">
								<tr id="goodsListTr-<?php echo ($list['order_sn']); ?>">
									<td>商品货号</td>
									<td>货品名称</td>
									<td>商品类型</td>
									<td>单价</td>
									<td>合计金额</td>
									<td>购买数量</td>
									<td>说明</td>
								</tr>
							</table>
						</td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					<tr>
						<td colspan="16" class="table-page">
							<?php if($counting < 25): else: ?>
						<div class="page-box"><?php echo ($show); ?></div><?php endif; ?>
						</td>
					</tr>
				</tbody><?php endif; ?>
			</table>
		</form>
	</div>

        </div>
        </div>
    </div>
    
    <script type="text/javascript" src="/Static/Public/Admin/js/plugins/jquery-1.8.3.min.js"></script>
    <script src="/Static/Public/Shop/js/baiduTemplate.js" type="text/javascript"></script>
    <script src="/Static/Public/Wechat/js/common.js" type="text/javascript"></script>
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
        $(function(){
            // 页码点击
            $('.page-btn').click(function(){
                jump_page = $('input[name="p"]').val();
                length = $('.page').length;
                for (var i = 0; i < length; i++) {
                    var that = $('.page').eq(i);
                    if ( that.attr('href') ) {
                        href = that.attr('href');
                        href_page = that.text();
                        break;
                    }
                }
                jump = href.replace('/p/'+href_page, '/p/'+jump_page);
                window.location.href = jump;
            });
        })
    </script>
    

	<script type="text/javascript" src="/Static/Public/Admin/js/jquery.datetimepicker.js"></script>
	<script type="text/javascript" src="/Static/Public/Admin/js/moment.min.js"></script>
	<script type="text/javascript">

		$('.btn').click(function() {
			var val = $('.text').val();
			var p = $('.text').attr('name');
			var url = window.location.href;
			var txt = $('.txt').html();
			var num = txt.substring (1,txt.indexOf('，')-1);
			var num = parseInt(num);
			// alert(num)
			// alert(isNaN(val));
			if ( val=='' ) {
				window.location.href = window.location.href;
			} else if ( val > num) {
				alert('查无此页');
			} else if ( parseInt(val) == val && val > 0 ) {
				if (val.indexOf('.') == true) {
						var oL=val.lastIndexOf(".");
						var val=val.substr(0,oL);
					} 
					var url = url.replace('.html','');
					// alert(url.indexOf('/p/'))
					if ( url.indexOf('/p/') != -1) {
						var url = url.substring(0,url.indexOf('/p/'));
						if (val == 1) {
							window.location.href = url+'.html'
						} else {
							window.location.href = url+'/'+p+'/'+val+'.html'
						}
					} else {
						if (val == 1) {
							window.location.href = url+'.html'
						} else {
							window.location.href = url+'/'+p+'/'+val+'.html'
						}
					}

			} else {
				alert('查无此页');
			}
		});

		$('.open').click(function() {
			var order_sn = $(this).attr('date-id');
			var goodsList = $('#goodsList-' + order_sn).html();
			if (goodsList == undefined) {
				$.ajax({
					url: '<?php echo U("Order/ajaxOrderDetail");?>',
					type: 'POST',
					dataType: 'json',
					data: {
						order_sn: order_sn
					},
				})
				.done(function(data) {
					var str = '';
			        // console.log(data);
					if (data.length > 0) {
	            		for (var i = 0; i < data.length; i ++) {
	            			var goodsType,
	            				unitPrice,
	            				price;
	            			if (data[i]['goods_type'] == 1) {
	            				goodsType = '赠品';
	            				unitPrice = '0.00';
	            				price = '0.00';
	            			} else {
	            				goodsType = '普通';
	            				unitPrice = data[i]['unit_price'];
	            				price = data[i]['price'];
	            			}
	            			str += '<tr id="goodsList-' + order_sn + '"><td>' + data[i]['goods_id'] + '</td><td>' + data[i]['goods_name'] + '</td><td>' + goodsType + '</td><td>' + unitPrice + '</td><td>' + price + '</td><td>' + data[i]['goods_number'] + '</td><td>'+goodsType+'</td></tr>';
	            		}
			        }
					$('#goodsListTr-' + order_sn).after(str);
				});
			}
		});

		var orderTable = $('.order-table');
		orderTable.on('click','.open',function(){
			$(this).parents('.tr-th').nextUntil('.tr-th').toggle();
		})

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

		$('#ids').click(function(){
	        if($(this).is(":checked"))
	        {
	            $('.ids').prop('checked',true);
	        }else{
	            $('.ids').prop('checked',false);
	        }
    	});

	    $('#btnok').click(function(){
	                var len = $('#Echeckbox .select-item');
	                if($(len[0]).find('input').is(":checked"))
	                {
	                    var flag = true;
	                }else{
	                    var flag = false;
	                }
	                for(var i=0;i<len.length;i++)
	                {
	                    if(flag)
	                    {
	                        $(len[i]).find('input').prop('checked','');
	                    }else{
	                        $(len[i]).find('input').prop('checked','checked');
	                    }
	                }
	    });
	    $('#btn-export').click(function(){
		
			var objs = $('.ids');
			var ids = '';
			for(var j=0;j<objs.length;j++)
			{   
				if ($(objs[j]).is(':checked'))
				{
			    	ids += $(objs[j]).val()+',';
				}
			}
			if (ids == '') {
				alert('请选择导出的数据');
				return false;
			}
			window.location.href="<?php echo U('Order/export');?>?&ids="+ids;
		});
		$('#JdeleteBtn').on('click', function(){
    		var ids = $('.ids:checked'),
    			idsTemp = '';
    		if (ids.length <= 0) {
    			alert('请选择要删除的商品！');
    			return;
    		}
    		
    		for (var i=0; i<ids.length; i++) {
    			idsTemp += ids.eq(i).val() + ',';
    		}
    		window.location.href = "<?php echo U('Order/delOrder');?>" + '?ids=' + idsTemp;
    	});
	</script>

</body>
</html>