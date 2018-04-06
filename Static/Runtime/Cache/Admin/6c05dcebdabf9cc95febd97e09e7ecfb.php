<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>管理员列表</title>
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
	<?php if(checkControllerAuth(array('Admin'))): ?><li <?php if(in_array(CONTROLLER_NAME, array('Admin'))): echo chr(32);?>class="current"<?php endif; ?>>
			<a class="date-tit sys-tj" href="<?php echo getAuthUrl(array('Admin-adminList', 'Admin-addAdmin'));?>" class="addons">管理员</a>
			<ul class="Jcon-ctr">
				<?php if(checkActionAuth('Admin-adminList')): ?><li class="<?php if(in_array(ACTION_NAME, array('adminList', 'editAdmin'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('Admin/adminList');?>">管理员列表</a>
					</li><?php endif; ?>

				<?php if(checkActionAuth('Admin-addAdmin')): ?><li class="<?php if(in_array(ACTION_NAME, array('addAdmin'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('Admin/addAdmin');?>">添加管理员</a>
					</li><?php endif; ?>
			</ul>
		</li><?php endif; ?>

	<?php if(checkControllerAuth(array('Auth'))): ?><li <?php if(in_array(CONTROLLER_NAME, array('Auth'))): echo chr(32);?>class="current"<?php endif; ?>>
			<a class="date-tit sys-tj" href="<?php echo getAuthUrl(array('Auth-roleList', 'Auth-addRole'));?>" class="addons">角色权限</a>
			<ul class="Jcon-ctr">
				<?php if(checkActionAuth('Auth-roleList')): ?><li <?php if(in_array(ACTION_NAME, array('roleList', 'roleRename', 'editRolePower'))): echo chr(32);?>class="on"<?php endif; ?>>
						<a href="<?php echo U('Auth/roleList');?>">角色列表</a>
					</li><?php endif; ?>

				<?php if(checkActionAuth('Auth-addRole')): ?><li <?php if(in_array(ACTION_NAME, array('addRole'))): echo chr(32);?>class="on"<?php endif; ?>>
						<a href="<?php echo U('Auth/addRole');?>">添加角色</a>
					</li><?php endif; ?>
			</ul>
		</li><?php endif; ?>

	<?php if(checkControllerAuth(array('Feedback'))): ?><li <?php if(in_array(CONTROLLER_NAME, array('Feedback'))): echo chr(32);?>class="current"<?php endif; ?>>
			<a class="date-tit sys-tj" href="<?php echo getAuthUrl(array('Feedback-feedbackList'));?>" class="addons">意见反馈管理</a>
			<ul class="Jcon-ctr">
				<?php if(checkActionAuth('Feedback-feedbackList')): ?><li <?php if(in_array(ACTION_NAME, array('feedbackList'))): echo chr(32);?>class="on"<?php endif; ?>>
						<a href="<?php echo U('Feedback/feedbackList');?>">意见反馈列表</a>
					</li><?php endif; ?>
			</ul>
		</li><?php endif; ?>
</ul>

            <a class="togglemenu"></a>
            <br /><br />
        </div>
        <div class="centercontent">
            
	 <div class="pageheader">
	    <h1 class="pagetitle">管理员列表</h1>
	    <span class="pagedesc"></span>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
			<tr>
				<th width="10%">ID</th>
				<th>账号</th>
				<th>角色</th>
				<th>是否锁定</th>
				<th>加入时间</th>
				<th width="16%">操作</th>
			</tr>

			<?php if(empty($adminList)): ?><tr>
					<td colspan="6">没有管理员列表~！</td>
				</tr>
			<?php else: ?>
				<?php if(is_array($adminList)): $i = 0; $__LIST__ = $adminList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$user): $mod = ($i % 2 );++$i;?><tr>
						<td><?php echo ($user['id']); ?></td>
						<td><?php echo ($user['admin_account']); ?></td>
						<td><?php echo ((isset($user['title']) && ($user['title'] !== ""))?($user['title']):"无所属"); ?></td>
						<td>
							<?php if($user['is_lock'] == 1): ?>是
							<?php else: ?>
								否<?php endif; ?>
						</td>
						<td><?php echo (time_format($user['add_time'])); ?></td>
						<td class="center">
							<?php if(checkActionAuth('Admin-editAdmin') || checkActionAuth('Admin-delAdmin')): if(checkActionAuth('Admin-editAdmin')): ?><a class="stdbtn btn_lime" href="<?php echo U('Admin/editAdmin', array('id'=>$user['id']));?>">编辑</a>&nbsp;&nbsp;<?php endif; ?>
								<?php if(checkActionAuth(array('Admin-delAdmin'))): ?><a class="stdbtn btn_lime" href="<?php echo U('Admin/delAdmin', array('id'=>$user['id']));?>">删除</a>&nbsp;&nbsp;<?php endif; ?>
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
			</tbody>
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
    
</body>
</html>