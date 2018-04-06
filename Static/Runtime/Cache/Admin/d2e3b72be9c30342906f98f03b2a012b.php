<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo C('systemName');?>管理后台</title>
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
	<?php if(checkActionAuth(array('Money-customerRechargeLog', 'Money-moneyLog', 'Money-moneyList', 'Money-agentBalanceList'))): ?><li <?php if(checkActionAuth(array('Credit-creditSetting', 'Credit-userCredit'))): echo chr(32);?>class="current"<?php endif; ?>>
			<a class="date-tit sys-tj" href="<?php echo getAuthUrl(array('Credit-creditSetting', 'Credit-userCredit'));?>" class="addons">信用管理</a>
			<ul class="Jcon-ctr">
				<?php if(checkActionAuth('Credit-creditSetting')): ?><li class="<?php if(in_array(ACTION_NAME, array('creditSetting'))): ?>on<?php endif; ?>">
						<a href="<?php echo U('Credit/creditSetting');?>">扣减/增加信用规则设置</a>
					</li><?php endif; ?>
				<?php if(checkActionAuth('Credit-userCredit')): ?><li <?php if(in_array(ACTION_NAME, array('userCredit'))): echo chr(32);?>class="on"<?php endif; ?>>
						<a href="<?php echo U('Credit/userCredit');?>">用户信用</a>
					</li><?php endif; ?>
			</ul>
		</li><?php endif; ?>
</ul>

            <a class="togglemenu"></a>
            <br /><br />
        </div>
        <div class="centercontent">
            
	<div class="pageheader">
	    <h1 class="pagetitle">扣减/增加信用规则设置</h1>
	</div>

	<div id="contentwrapper" class="contentwrapper">
        <form class="stdform stdform2" action="<?php echo U(Credit/creditSetting);?>" method="post">
        	<h4>买家</h4>
	        <div id="contentwrapper" class="contentwrapper">
	        	<div id="updates" class="subcontent" style="display: block;">
					<?php if(is_array($configList)): $i = 0; $__LIST__ = $configList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(in_array($vo['config_sign'], array('userCancelOrderCredit', 'userLogoutCredit'))): ?><div class="line-dete">
								<label><?php echo ($vo['config_name']); ?></label>
				                <span class="field">
				                	<input type="hidden" name="config[<?php echo ($key); ?>][config_sign]" value="<?php echo ($vo['config_sign']); ?>">
				                	<input type="hidden" name="config[<?php echo ($key); ?>][config_name]" value="<?php echo ($vo['config_name']); ?>">
				                	<input type="text" name="config[<?php echo ($key); ?>][config_value]"  class="smallinput" value="<?php echo ($vo['config_value']); ?>">
				                	<input type="hidden" name="config[<?php echo ($key); ?>][explain]" value="<?php echo ($vo['explain']); ?>">
				                	<small class="desc"><?php echo ($vo['explain']); ?></small>
				                </span>
				            </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
	            </div>
	        </div>
	        <h4>卖家</h4>
	        <div id="contentwrapper" class="contentwrapper">
	        	<div id="updates" class="subcontent" style="display: block;">
			        <?php if(is_array($configList)): $i = 0; $__LIST__ = $configList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(in_array($vo['config_sign'], array('agentCancelOrderCredit', 'agentLogoutCredit'))): ?><div class="line-dete">
								<label><?php echo ($vo['config_name']); ?></label>
				                <span class="field">
				                	<input type="hidden" name="config[<?php echo ($key); ?>][config_sign]" value="<?php echo ($vo['config_sign']); ?>">
				                	<input type="hidden" name="config[<?php echo ($key); ?>][config_name]" value="<?php echo ($vo['config_name']); ?>">
				                	<input type="text" name="config[<?php echo ($key); ?>][config_value]"  class="smallinput" value="<?php echo ($vo['config_value']); ?>">
				                	<input type="hidden" name="config[<?php echo ($key); ?>][explain]" value="<?php echo ($vo['explain']); ?>">
				                	<small class="desc"><?php echo ($vo['explain']); ?></small>
				                </span>
				            </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
		        	<input type="submit" class="big-btn stdbtn" value="更新">
	        	</div>
		    </div>
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
    
<script type="text/javascript" src="/Static/Public/Admin/js/plugins/jquery-1.8.3.min.js"></script>
<script type="text/javascript">

</script>

</body>
</html>