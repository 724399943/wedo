<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>顾客信息管理</title>
    <link rel="stylesheet" href="/Static/Public/Admin/css/style.default.css" type="text/css" />
    <link rel="shortcut icon" href="/Static/Public/xcrozz.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/Static/Public/Admin/css/plugins/uniform.tp.css" />
    <link rel="stylesheet" href="/Static/Public/Admin/css/pop.css" type="text/css" />
    
	<link rel="stylesheet" href="/Static/Public/Admin/css/jquery.datetimepicker.css" type="text/css" />
	<style type="text/css">
		.tspan{height:30px;display:inline-block;vertical-align:middle;}
		.tspan i{display:block;width:20px;height:10px;background:url(/Static/Public/Admin/images/ico.png) center top no-repeat;background-size:50%;margin:4px 0;cursor: pointer;}
		.tspan i.one{transform:rotate(180deg);--webkit-transform:rotate(180deg);margin-top:2px;}
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
	<?php if(checkControllerAuth(array('User'))): ?><li <?php if(in_array(CONTROLLER_NAME, array('User'))): echo chr(32);?>class="current"<?php endif; ?>>
			<a class="date-tit sys-tj" href="<?php echo getAuthUrl(array('User-userList'));?>" class="addons">用户管理</a>
			<ul class="Jcon-ctr">
				<?php if(checkActionAuth('User-userList')): ?><li <?php if(in_array(ACTION_NAME, array('userList', 'editUser'))): echo chr(32);?>class="on"<?php endif; ?>>
						<a href="<?php echo U('User/userList');?>">顾客信息管理</a>
					</li><?php endif; ?>
				<?php if(checkActionAuth('User-agentList')): ?><li <?php if(in_array(ACTION_NAME, array('agentList', 'editAgent'))): echo chr(32);?>class="on"<?php endif; ?>>
						<a href="<?php echo U('User/agentList');?>">商家信息管理</a>
					</li><?php endif; ?>
			</ul>
		</li><?php endif; ?>
</ul>

            <a class="togglemenu"></a>
            <br /><br />
        </div>
        <div class="centercontent">
            
	<div class="pageheader">
	    <h1 class="pagetitle">顾客信息管理</h1>
	    <span class="pagedesc"></span>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form method="get" action="<?php echo U('User/userList');?>" class="menber" id="KsearchForm">
			<p>
				<label>账号：</label>
				<input type="text" name="username" placeholder="输入搜索账号" value="<?php echo ($return['username']); ?>">
				&nbsp;&nbsp;
				<label>昵称：</label>
				<input type="text" name="nickname" placeholder="输入搜索昵称" value="<?php echo ($return['nickname']); ?>">
				&nbsp;&nbsp;
				<label>用户状态：</label>
				<select name="is_lock">
				    <option value="-1">全部</option>
					<option value="0" <?php if($return['is_lock'] == 0): ?>selected<?php endif; ?>>正常</option>
					<option value="1" <?php if($return['is_lock'] == 1): ?>selected<?php endif; ?>>屏蔽</option>
				</select>
				&nbsp;&nbsp;
				<label>用户来源：</label>
				<select name="from">
				    <option value="-1">全部</option>
					<option value="0" <?php if($return['from'] == 0): ?>selected<?php endif; ?>>手机号</option>
					<option value="1" <?php if($return['from'] == 1): ?>selected<?php endif; ?>>邮箱</option>
				</select>
			</p>
	        <p>
				<label>注册时间：</label>
				<input type="text" id="staDatartTime" date-time="<?php echo ($return['startTime']); ?>" value="" style="margin-right: 0px">
				<input type="hidden" name="start_time" id="startTime" value="<?php echo ($return['startTime']); ?>" >
				-
				<input type="text" id="endDataTime" date-time="<?php echo ($return['endTime']); ?>" value="">
				<input type="hidden" name="end_time" id="endTime" value="<?php echo ($return['endTime']); ?>">
				&nbsp;&nbsp;
				<input type="hidden" name="order_number" value="<?php echo ($return['order_number']); ?>">
				<input type="hidden" name="add_time" value="<?php echo ($return['add_time']); ?>">
				<input type="hidden" name="last_login_time" value="<?php echo ($return['last_login_time']); ?>">
				<input type="submit" value="筛选">
			</p>
		</form>

		<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
			<tr>
				<th width="10%">ID</th>
				<th>头像</th>
				<th>登录账号</th>
				<th>昵称</th>
				<th>性别</th>
				<th>
					订单量
					<span class="tspan">
						<i class="one KorderNumber" data-sort="0"></i>
						<i class="two KorderNumber" data-sort="1"></i>
					</span>
				</th>
				<th>用户状态</th>
				<th>用户来源</th>
				<th>
					注册时间
					<span class="tspan">
						<i class="one KaddTime" data-sort="0"></i>
						<i class="two KaddTime" data-sort="1"></i>
					</span>
				</th>
				<th>
					最近访问
					<span class="tspan">
						<i class="one KlastLoginTime" data-sort="0"></i>
						<i class="two KlastLoginTime" data-sort="1"></i>
					</span>
				</th>
				<th>操作</th>
			</tr>

			<?php if(empty($list)): ?><tr>
					<td colspan="11">暂无相关数据</td>
				</tr>
			<?php else: ?>
				<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;?><tr>
						<td><?php echo ($data['id']); ?></td>
						<td><img src="<?php echo ($data['headimgurl']); ?>" width="30px" height="30px" /></td>
						<td><?php echo ($data['username']); ?></td>
						<td><?php echo ($data['nickname']); ?></td>
						<td>
							<?php if($data['sex'] == '1'): ?>男
							<?php else: ?>
								女<?php endif; ?>
						</td>
						<td><?php echo ($data['order_number']); ?></td>
						<td>
							<?php if($data['is_lock'] == 1 ): ?>屏蔽 
							<?php else: ?>
								正常<?php endif; ?>
                        </td>
						<td>
                            <?php if($data['phone'] == ''): ?>邮箱
                             <?php else: ?>
                             	手机号<?php endif; ?>
 						</td>
						<td><?php echo (time_format($data['add_time'])); ?></td>
						<td>
							<?php if(!empty($data['last_login_time'])): echo time_format($data['last_login_time']);?>
							<?php else: ?>
								-<?php endif; ?>
						</td>
						<td class="center">
							<?php if(checkActionAuth('User-editUser')): if(checkActionAuth('User-editUser')): ?><a class="stdbtn btn_lime" href="<?php echo U('User/editUser', array('id'=>$data['id']));?>">查看</a>&nbsp;&nbsp;<?php endif; ?>
							<?php if(checkActionAuth('User-setOnSale')): if($data['is_lock'] == '1'): ?><a class="stdbtn btn_lime" href="<?php echo U('user/setOnSale', array('id'=>$data['id'], 'is_lock'=> 0));?>">恢复</a>&nbsp;&nbsp;
								<?php else: ?>
									<a class="stdbtn btn_lime" href="<?php echo U('user/setOnSale', array('id'=>$data['id'], 'is_lock'=> 1));?>">屏蔽</a>&nbsp;&nbsp;<?php endif; endif; ?>
							<?php else: ?>
								无权限操作<?php endif; ?>
						</td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
				<tr>
					<td colspan="11">
						<?php if($counting >= 25): ?><div class="page-box"><?php echo ($show); ?></div><?php endif; ?>
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

	// 订单量排序
	$('.KorderNumber').click(function(){
		var sort = $(this).data('sort');
		$('input[name="order_number"]').val(sort);
		$('input[name="add_time"]').val('-1');
		$('input[name="last_login_time"]').val('-1');
		$('#KsearchForm').submit();
	});

	// 注册时间排序
	$('.KaddTime').click(function(){
		var sort = $(this).data('sort');
		$('input[name="add_time"]').val(sort);
		$('input[name="order_number"]').val('-1');
		$('input[name="last_login_time"]').val('-1');
		$('#KsearchForm').submit();
	});

	// 最近访问排序
	$('.KlastLoginTime').click(function(){
		var sort = $(this).data('sort');
		$('input[name="last_login_time"]').val(sort);
		$('input[name="add_time"]').val('-1');
		$('input[name="order_number"]').val('-1');
		$('#KsearchForm').submit();
	});
</script>

</body>
</html>