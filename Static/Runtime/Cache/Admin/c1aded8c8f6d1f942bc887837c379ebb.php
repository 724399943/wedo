<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>积分商品管理</title>
    <link rel="stylesheet" href="/Static/Public/Admin/css/style.default.css" type="text/css" />
    <link rel="shortcut icon" href="/Static/Public/xcrozz.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/Static/Public/Admin/css/plugins/uniform.tp.css" />
    <link rel="stylesheet" href="/Static/Public/Admin/css/pop.css" type="text/css" />
    
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
	<?php if(checkControllerAuth(array('Point'))): ?><li <?php if(in_array(ACTION_NAME, array('pointGoodsList'))): echo chr(32);?>class="current"<?php endif; ?>>
			<a class="date-tit sys-tj" href="<?php echo getAuthUrl(array('Point-pointGoodsList'));?>" class="addons">积分管理</a>
			<ul class="Jcon-ctr">
				<?php if(checkActionAuth('Point-pointGoodsList')): ?><li class="<?php if(in_array(ACTION_NAME, array('pointGoodsList', 'pointAddGoods', 'pointEditGoods'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('Point/pointGoodsList');?>">积分商品列表</a>
					</li><?php endif; ?>
				<?php if(checkActionAuth('PointOrder-pointOrderList')): ?><li class="<?php if(in_array(ACTION_NAME, array('pointOrderList', 'pointOrderDetail'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('PointOrder/pointOrderList');?>">积分订单列表</a>
					</li><?php endif; ?>
				<?php if(checkActionAuth('Point-pointLog')): ?><li class="<?php if(in_array(ACTION_NAME, array('pointLog'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('Point/pointLog');?>">积分记录</a>
					</li><?php endif; ?>
				<?php if(checkActionAuth('Point-pointSetting')): ?><li class="<?php if(in_array(ACTION_NAME, array('pointSetting'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('Point/pointSetting');?>">积分获取规则设置</a>
					</li><?php endif; ?>
			</ul>
		</li><?php endif; ?>
</ul>

            <a class="togglemenu"></a>
            <br /><br />
        </div>
        <div class="centercontent">
            
	<div class="pageheader">
	    <h1 class="pagetitle">
	    	积分商品管理
	    	<?php if(checkActionAuth('Point-pointAddGoods')): ?><a href="<?php echo U('Point/pointAddGoods');?>" class="btn btn_link" style="float: right"><span style="font-size:14px">新增+</span></a><?php endif; ?>
	    </h1>
	    <span class="pagedesc"></span>
	</div>
	<div id="contentwrapper" class="contentwrapper">
        <form  action="<?php echo U('Goods/import');?>" method="post" enctype="multipart/form-data" onsubmit="return checked()">
            <span style="float:right;margin-right: 20px;">
			
        	</span>
        </form>
		<form action="<?php echo U('Point/pointGoodsList');?>" method="get" id="searchForm">
            <div>
             店铺名称：<input type="text" name="agent_name"  placeholder="输入搜索店铺名称" value="<?php echo ($return['agent_name']); ?>" />&nbsp;&nbsp;&nbsp;
              商品名称：<input type="text" name="goods_name"  placeholder="输入搜索商品名称" value="<?php echo ($return['goods_name']); ?>" />&nbsp;&nbsp;&nbsp;
             	<label>商品类型：</label>
				<select name="goods_type">
				    <option value="-1">全部</option>
					<option value="0" <?php if($return['goods_type'] == 0): ?>selected<?php endif; ?>>自营商品</option>
					<option value="1" <?php if($return['goods_type'] == 1): ?>selected<?php endif; ?>>卖家商品</option>
				</select>
				<label>商品状态：</label>
				<select name="is_on_sale">
					<option value="-1">全部</option>
					<option value="0" <?php if($return['is_on_sale'] == 0): ?>selected<?php endif; ?>>已下架</option>
					<option value="1" <?php if($return['is_on_sale'] == 1): ?>selected<?php endif; ?>>已上架</option>
				</select>
             	<input type="submit" value="筛选"/>
             	
             	<input type="button" id="JsetOnSale" value="批量下架" />
             	
             	<input type="button" id="JdeleteAll" value="批量删除" />
            </div>
            <br>
            <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
               
                 <tr>
                    <th><input type="checkbox" id = "ids" /></th>
					<th>ID</th>
					<th>商品名称</th>
					<th>店铺名称</th>
					<th>商品类型</th>
					<th>销量</th>
					<th>收藏</th>
					<th>兑换积分</th>
					<th>库存</th>
					<th>状态</th>
					<th>时间</th>
					<th>操作</th>
				</tr>
				
				<?php if(empty($pointGoodsList)): ?><tr>
						<td colspan="12">暂无相关数据</td>
					</tr>
				<?php else: ?>
					<?php if(is_array($pointGoodsList)): $i = 0; $__LIST__ = $pointGoodsList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($i % 2 );++$i;?><tr>
                            <td><input type="checkbox" name="checkbox"  class="ids" value="<?php echo ($goods["id"]); ?>"/></td>
							<td><?php echo ($goods['id']); ?></td>
							<td><img src="<?php echo ($goods['goods_image']); ?>" alt="" height="40px">&nbsp;&nbsp;&nbsp;<?php echo (mySubstr($goods['goods_name'],43)); ?></td>
							<td><?php echo ($goods['agent_name']); ?></td>
							<td><?php echo ($goods['goods_type']); ?></td>
							<td><?php echo ($goods['sale_number']); ?></td>
							<td><?php echo ($goods['collect_number']); ?></td>
							<td><?php echo ($goods['goods_price']); ?></td>
							<td><?php echo ($goods['goods_number']); ?></td>
							<td>
								<?php if($goods['is_on_sale'] == '1' ): ?>已上架
								<?php else: ?>
									已下架<?php endif; ?>
							</td>
							<td><?php echo (time_format($goods['add_time'])); ?></td>
							<td class="center">
								<?php if(checkActionAuth('Goods-editGoods') || checkActionAuth('Goods-setOnSale') || checkActionAuth('Goods-delGoods')): ?><a class="stdbtn btn_lime" href="<?php echo U('Point/pointEditGoods', array('id'=>$goods['id']));?>">编辑</a>&nbsp;&nbsp;<?php endif; ?>
								<?php if(checkActionAuth('Point-setOnSale')): if($goods['is_on_sale'] == '1'): ?><a class="stdbtn btn_lime" href="<?php echo U('Point/setOnSale', array('ids'=>$goods['id'], 'is_on_sale'=> '0'));?>">下架</a>&nbsp;&nbsp;
									<?php else: ?>
										<a class="stdbtn btn_lime" href="<?php echo U('Point/setOnSale', array('ids'=>$goods['id'], 'is_on_sale'=> '1'));?>">上架</a>&nbsp;&nbsp;<?php endif; endif; ?>
								<?php if(checkActionAuth('Point-delGoods')): ?><a class="stdbtn btn_lime" href="<?php echo U('Point/pdelGoods', array('id'=>$goods['id']));?>">删除</a>&nbsp;&nbsp;<?php endif; ?>
							</td>
						</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					<tr>
						<td colspan="12">
							<div class="page-box"><?php echo ($show); ?></div>
						</td>
					</tr><?php endif; ?>
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
    
<script type="text/javascript">
	$(function(){
    	//全选
        $('#ids').click(function(){
            if($(this).is(":checked"))
            {
                $('.ids').prop('checked',true);
            }else{
                $('.ids').prop('checked',false);
            }
        }); 

		//批量删除
		$("#JdeleteAll").click(function () {
			var objs = $('.ids');
			var ids = '';
			for(var j=0;j<objs.length;j++)
			{   
				if ($(objs[j]).is(':checked'))
				{
			    	ids += $(objs[j]).val()+',';
				}
			}
			var jumpUrl = "<?php echo U('Point/delGoods');?>" + '?ids=' + ids;
			window.location.href = jumpUrl;
		});

		//批量下架
		$("#JsetOnSale").click(function () {
			var objs = $('.ids');
			var ids = '';
	        var is_on_sale = '0';
	        
			for(var j=0;j<objs.length;j++)
			{   
				if ($(objs[j]).is(':checked'))
				{
			    	ids += $(objs[j]).val()+',';
				}
			}
			var jumpUrl = "<?php echo U('Point/setOnSale');?>" + '?ids=' + ids + '&is_on_sale=' + is_on_sale;
			window.location.href = jumpUrl;
		});
	});
</script>

</body>
</html>