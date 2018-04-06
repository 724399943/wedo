<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>商品管理列表</title>
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
	<?php if(checkActionAuth(array('Category-goodsCategory'))): ?><li <?php if(checkActionAuth(array('AgentCategory-agentCategory'))): echo chr(32);?>class="current"<?php endif; ?>>
			<a class="date-tit sys-tj" href="<?php echo getAuthUrl(array('AgentCategory-agentCategory'));?>" class="addons">商城管理</a>
			<ul class="Jcon-ctr">
				<?php if(checkActionAuth(array('AgentCategory-agentCategory'))): ?><li class="<?php if(in_array(CONTROLLER_NAME, array('AgentCategory')) && in_array(ACTION_NAME, array('agentCategory', 'addAgentCategory', 'editAgentCategory'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('AgentCategory/agentCategory');?>">店铺分类管理</a>
					</li><?php endif; ?>

				<?php if(checkActionAuth(array('Category-goodsCategory'))): ?><li class="<?php if(in_array(CONTROLLER_NAME, array('Category')) && in_array(ACTION_NAME, array('goodsCategory', 'addGoodsCategory', 'editGoodsCategory'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('Category/goodsCategory');?>">商品分类管理</a>
					</li><?php endif; ?>

				<?php if(checkActionAuth(array('Goods-goodsRanking'))): ?><li class="<?php if(in_array(CONTROLLER_NAME, array('Goods')) && in_array(ACTION_NAME, array('goodsRanking'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('Goods/goodsRanking');?>">商品查询排行</a>
					</li><?php endif; ?>

				<?php if(checkActionAuth(array('Goods-goodsList'))): ?><li class="<?php if(in_array(CONTROLLER_NAME, array('Goods')) && in_array(ACTION_NAME, array('goodsList', 'goodsDetail'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('Goods/goodsList');?>">商品管理</a>
					</li><?php endif; ?>

				<?php if(checkActionAuth('GoodsCheck-goodsAuth')): ?><li class="<?php if(in_array(CONTROLLER_NAME, array('GoodsCheck')) && in_array(ACTION_NAME, array('goodsAuth', 'authSetting'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('GoodsCheck/goodsAuth');?>">商品认证管理</a>
					</li><?php endif; ?>

				<?php if(checkActionAuth('GoodsCheck-goodsTop')): ?><li class="<?php if(in_array(CONTROLLER_NAME, array('GoodsCheck')) && in_array(ACTION_NAME, array('goodsTop', 'topSetting'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('GoodsCheck/goodsTop');?>">商品置顶管理</a>
					</li><?php endif; ?>

				<?php if(checkActionAuth('Bidding-biddingIndexGoods')): ?><li class="<?php if(in_array(CONTROLLER_NAME, array('Bidding')) && in_array(ACTION_NAME, array('biddingIndexGoods', 'auditBiddingIndexGoods'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('Bidding/biddingIndexGoods');?>">首页商品竞价管理</a>
					</li><?php endif; ?>

				<?php if(checkActionAuth('Bidding-biddingFavorableGoods')): ?><li class="<?php if(in_array(CONTROLLER_NAME, array('Bidding')) && in_array(ACTION_NAME, array('biddingFavorableGoods', 'auditBiddingFavorableGoods'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('Bidding/biddingFavorableGoods');?>">优惠商品竞价管理</a>
					</li><?php endif; ?>

				<?php if(checkActionAuth('Bidding-biddingAgent')): ?><li class="<?php if(in_array(CONTROLLER_NAME, array('Bidding')) && in_array(ACTION_NAME, array('biddingAgent', 'auditBiddingAgent'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('Bidding/biddingAgent');?>">首页商家竞价管理</a>
					</li><?php endif; ?>

				<?php if(checkActionAuth('Bidding-biddingBanner')): ?><li class="<?php if(in_array(CONTROLLER_NAME, array('Bidding', 'Ad')) && in_array(ACTION_NAME, array('biddingBanner', 'index', 'addAd', 'auditBiddingBanner'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('Bidding/biddingBanner');?>">广告位竞价管理</a>
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
	    	店铺分类管理
	    	<?php if(checkActionAuth('AgentCategory-addAgentCategory')): ?><a href="<?php echo U('AgentCategory/addAgentCategory');?>" class="btn btn_link" style="float: right"><span style="font-size:14px">添加</span></a><?php endif; ?>
	    </h1>
	    <span class="pagedesc"></span>
	</div>
	<div id="contentwrapper" class="contentwrapper">
        <form  action="<?php echo U('Goods/import');?>" method="post" enctype="multipart/form-data" onsubmit="return checked()">
            <span style="float:right;margin-right: 20px;">
			</span>
        </form>
		<form action="<?php echo U('AgentCategory/agentCategory');?>" method="get" id="searchForm">
            <div>
            
              分类名称：<input type="text" name="keywords" style="width:20%" placeholder="请输入分类名称"  onfocus="if(this.value == ''){ this.value = '';this.style.color='#00000';}"/>&nbsp;&nbsp;&nbsp;
             	

             	<input type="submit" value="筛选"/>
             	
             	<input type="button" id="JdeleteAll" value="删除" />
            </div>
            <br>
            <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
                <tr>
                    <th><input type="checkbox" id = "ids" /></th>
					<th>分类名称</th>
					<th>店铺数</th>
					<th>排序</th>
					<th>icon</th>
					<th>操作</th>
				</tr>

          		<?php if(empty($categoryList)): ?><tr>
						<td colspan="13">没有商品列表~！</td>
					</tr>
				<?php else: ?>
					<?php if(is_array($categoryList)): $i = 0; $__LIST__ = $categoryList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$agent): $mod = ($i % 2 );++$i;?><tr>
                            <td><input type="checkbox" name="checkbox"  class="ids" value="<?php echo ($agent["id"]); ?>"/></td>
							<td>&nbsp;&nbsp;&nbsp;<?php echo (mySubstr($agent['category_name'],43)); ?></td>
							<td><?php echo ($agent['count']); ?></td>
							<td><?php echo ($agent['sort']); ?></td>
							<td><?php echo ($agent['app_icon']); ?></td>
							<td class="center">
								<?php if(checkActionAuth('AgentCategory-editAgentCategory') ||checkActionAuth('AgentCategory-delAgentCategory')): ?><a class="stdbtn btn_lime" href="<?php echo U('AgentCategory/editAgentCategory', array('id'=>$agent['id']));?>">编辑</a>&nbsp;&nbsp;<?php endif; ?>
								<?php if(checkActionAuth('AgentCategory-delAgentCategory')): ?><a class="stdbtn btn_lime" href="<?php echo U('AgentCategory/delAgentCategory', array('id'=>$agent['id']));?>">删除</a>&nbsp;&nbsp;<?php endif; ?>
							</td>
						</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					<tr>
						<td colspan="13">
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
    
$(function() {
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
		var jumpUrl = "<?php echo U('AgentCategory/delAgentCategory');?>" + '?ids=' + ids;
		window.location.href = jumpUrl;
	});
});
</script>

</body>
</html>