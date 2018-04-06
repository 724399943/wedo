<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>商家审核</title>
    <link rel="stylesheet" href="/Static/Public/Admin/css/style.default.css" type="text/css" />
    <link rel="shortcut icon" href="/Static/Public/xcrozz.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/Static/Public/Admin/css/plugins/uniform.tp.css" />
    <link rel="stylesheet" href="/Static/Public/Admin/css/pop.css" type="text/css" />
    
	<link rel="stylesheet" href="/Static/Public/Admin/css/jquery.datetimepicker.css" type="text/css" />
	<style type="text/css">
		.givepop-box{ width: 400px; min-height: 180px; background: #fff; overflow: hidden; }
		.givepop-box .g-line{ line-height: 32px; margin-top: 20px; padding-left: 120px; }
		
		.givepop-box .stdbtn{ margin-left: 150px; margin-top: 30px}
		.caiji-box{ max-height: 500px; overflow: auto; }
	</style>

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
	<?php if(checkControllerAuth(array('Agent'))): ?><li <?php if(in_array(ACTION_NAME, array('checkAgentList', 'agentList'))): echo chr(32);?>class="current"<?php endif; ?>>
			<a class="date-tit sys-tj" href="<?php echo getAuthUrl(array('Agent-checkAgentList'));?>" class="addons">商家管理</a>
			<ul class="Jcon-ctr">
				<?php if(checkActionAuth('Agent-checkAgentList')): ?><li class="<?php if(in_array(ACTION_NAME, array('checkAgentList', 'agentDetail'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('Agent/checkAgentList');?>">商家审核</a>
					</li><?php endif; ?>
				<?php if(checkActionAuth('Agent-agentList')): ?><li class="<?php if(in_array(ACTION_NAME, array('agentList', 'agentGoods'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('Agent/agentList');?>">商家商品管理</a>
					</li><?php endif; ?>
				<?php if(checkActionAuth('Agent-salesStatistics')): ?><li class="<?php if(in_array(ACTION_NAME, array('salesStatistics'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('Agent/salesStatistics');?>">商家销售统计</a>
					</li><?php endif; ?>
			</ul>
		</li><?php endif; ?>
</ul>

            <a class="togglemenu"></a>
            <br /><br />
        </div>
        <div class="centercontent">
            
	<div class="pageheader">
	    <h1 class="pagetitle">商家审核</h1>
	    <span class="pagedesc"></span>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form action="<?php echo U('Agent/checkAgentList');?>" method="get" id="searchForm">
            <p>
            	<label>账号：</label>
				<input type="text" name="nickname" placeholder="输入搜索账号" value="<?php echo ($return['nickname']); ?>">
				&nbsp;&nbsp;
            	<label>店铺名称：</label>
                <input type="text" name="agent_name" placeholder="输入搜索店铺名称" value="<?php echo ($return['agent_name']); ?>"/>
				&nbsp;&nbsp;
				<label>真实姓名：</label>
                <input type="text" name="manager" placeholder="输入真实姓名" value="<?php echo ($return['manager']); ?>"/>
				&nbsp;&nbsp;
            	<label>审核状态：</label>
				<select name="status">
					<option value="-1">全部</option>
					<option value="0" <?php if($return['status'] == '0'): ?>selected<?php endif; ?>>正在审核</option>
					<option value="2" <?php if($return['status'] == '2'): ?>selected<?php endif; ?>>未通过</option>
				</select>
				&nbsp;&nbsp;
            </p>
            <p>
           		<label>注册时间：</label>
				<input type="text" id="staDatartTime" date-time="<?php echo ($return['startTime']); ?>" style="margin-right: 0px">
				<input type="hidden" name="start_time" id="startTime" value="<?php echo ($return['startTime']); ?>" >
				-
				<input type="text" id="endDataTime" date-time="<?php echo ($return['endTime']); ?>">
				<input type="hidden" name="end_time" id="endTime" value="<?php echo ($return['endTime']); ?>">
				&nbsp;&nbsp;
				<label>平台分类：</label>
				<select name="category_id">
					<option value="">全部</option>
					<?php if(is_array($categoryList)): $i = 0; $__LIST__ = $categoryList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;?><option value="<?php echo ($data['id']); ?>" <?php if($return['category_id'] == $data['id']): ?>selected<?php endif; ?>><?php echo ($data['category_name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
				</select>
				&nbsp;&nbsp;
             	<input type="submit" value="筛选"/>
            </p>
        </form>
		<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
			<tr>
				<th>序号</th>
				<th>账号</th>
				<th>店铺名称</th>
				<th>店铺分类</th>
				<th>店铺位置</th>
				<th>真实姓名</th>
				<th>注册时间</th>
				<th>审核状态</th>
				<th width="19%">操作</th>
			</tr>
			<?php if(empty($list)): ?><tr>
					<td colspan="9">暂无相关数据</td>
				</tr>
			<?php else: ?>
				<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;?><tr>
						<td><?php echo ($data['id']); ?></td>
						<td><?php echo ($data['nickname']); ?></td>
						<td><?php echo ($data['agent_name']); ?></td>
						<td><?php echo ($data['category_name']); ?></td>
						<td><?php echo ($data['address']); ?></td>
						<td><?php echo ($data['manager']); ?></td>
						<td><?php echo time_format($data['add_time']);?></td>
						<td>
							<?php if($data['status'] == '0'): ?>正在审核
							<?php else: ?>
								未通过<?php endif; ?>
						</td>
						<td class="center">
							<?php if(checkActionAuth(array('Agent-agentDetail', 'Agent-checkAgent'))): if(checkActionAuth('Agent-agentDetail')): ?><a class="stdbtn btn_lime" href="<?php echo U('Agent/agentDetail', array('id'=>$data['id']));?>">查看
								    </a><?php endif; ?>
								<?php if(checkActionAuth('Agent-checkAgent')): ?><!-- <button class="stdbtn btn_lime" onclick="checkAgent(<?php echo ($data['id']); ?>,'1')">通过
								    </button>&nbsp;&nbsp; -->
								    <a href="javascript:checkAgent(<?php echo ($data['id']); ?>,'1');" class="stdbtn btn_lime">通过</a>
								    <a href="javascript:checkAgent(<?php echo ($data['id']); ?>,'2');" class="stdbtn btn_lime">拒绝</a><?php endif; ?>
							<?php else: ?>
								无权限操作<?php endif; ?>
						</td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
				<tr>
					<td colspan="9">
						<?php if($counting < 25): else: ?>
						   <div class="page-box"><?php echo ($show); ?></div><?php endif; ?>
					</td>
				</tr><?php endif; ?>
		</table>
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
	function checkAgent(id,status){
		$.ajax({
			url : '<?php echo U('Agent/checkAgent');?>',
			type : 'GET',
			dataType : 'json',
			data:{id: id,status: status}
		})
		.done(function(returnData){
			if(returnData['status'] == '200000') {
				//出现弹窗
				var pass = '<div class="givepop-box"><h1 class="g-line">审核已通过</h1><div class="g-line"><span class="stdbtn Jdelivery-commit" >确定</span></div></div>'
				popwin('',pass);
			} else {
				alert('审核失败');
			}
		})
	}
	$(function(){
		$('#staDatartTime').val(moment.unix($('#staDatartTime').attr('date-time')).format("YYYY-MM-DD HH:mm:ss"));
		$('#endDataTime').val(moment.unix($('#endDataTime').attr('date-time')).format("YYYY-MM-DD HH:mm:ss"));
		$('#staDatartTime').datetimepicker({
			format:"Y-m-d H:i:s", 
			onChangeDateTime:function(dp, $input) {
				$('#startTime').val(moment($input.val()).unix().valueOf(-60));
			}
		});
		$('#endDataTime').datetimepicker({
			format:"Y-m-d H:i:s", 
			onChangeDateTime:function(dp, $input) {
				$('#endTime').val(moment($input.val()).unix().valueOf(-60));
			}
		});
		
		//关闭弹窗
		$(document).on('click','.Jdelivery-commit',function(){
			// 关闭弹窗
			popclose();
			window.location.href="<?php echo U('Agent/checkAgentList');?>";
		});
	})
</script>

</body>
</html>