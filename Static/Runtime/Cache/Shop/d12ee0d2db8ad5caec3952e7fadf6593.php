<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo (L("_PC_GOODS_LEFT_CATEGORY_MENU_")); ?></title>
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
    
	<style type="text/css">
		.stdtable th, .stdtable td{text-align:center;}
		.txtEdit{display:inline-block;width:35px;height:35px;background:url(/Static/Public/Shop/images/edit_ico.png) center center no-repeat;background-size:25px;vertical-align:middle;cursor:pointer;}
		.txtEdit.on{background:url(/Static/Public/Shop/images/finish_ico.png) center center no-repeat;background-size:25px;}
		.intxt.on{border:1px solid #ccc;box-shadow: inset 0 1px 3px #ddd;-webkit-box-shadow: inset 0 1px 3px #ddd;-moz-box-shadow: inset 0 1px 3px #ddd;}
		.selnum{line-height:33px;vertical-align:middle;display:inline-block;}
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
			<li class="<?php if(in_array(CONTROLLER_NAME, array('Agent')) && in_array(ACTION_NAME, array('agentGoodsCategory', 'goodsModel', 'addModelAttr'))): ?>on<?php endif; ?>">
				<a href="<?php echo U('Agent/agentGoodsCategory');?>"><?php echo (L("_PC_GOODS_LEFT_CATEGORY_MENU_")); ?></a>
			</li>
			<li class="<?php if(in_array(CONTROLLER_NAME, array('Agent', 'Goods', 'GoodsForEdit')) && in_array(ACTION_NAME, array('agentGoods', 'goodsDetail', 'addGoods', 'editGoods', 'payForEdit'))): ?>on<?php endif; ?>">
				<a href="<?php echo U('Agent/agentGoods');?>"><?php echo (L("_PC_GOODS_LEFT_GOODS_MENU_")); ?></a>
			</li>
			<li class="<?php if(in_array(CONTROLLER_NAME, array('Agent')) && in_array(ACTION_NAME, array('agentGoodsComment'))): ?>on<?php endif; ?>">
				<a href="<?php echo U('Agent/agentGoodsComment');?>"><?php echo (L("_PC_GOODS_LEFT_COMMENT_MENU_")); ?></a>
			</li>
		</ul>
	</li>
</ul>

                <a class="togglemenu"></a>
                <br /><br />
            </div>
            <div class="centercontent">
                
	 <div class="pageheader">
	    <h1 class="pagetitle"><?php echo (L("_PC_GOODS_LEFT_CATEGORY_MENU_")); ?></h1>
	    <span class="pagedesc"></span>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<div style="margin-bottom:20px;">
     		<a href="javascript:;" class="btn btn_link" style="float:left;margin-right:15px;"><span style="font-size:14px;" id="Jdel"><?php echo (L("_PC_GOODS_RIGHT_MULTIPLE_DELETE_")); ?></span></a>
     		<span class="selnum">0<?php echo (L("_PC_GOODS_RIGHT_HAS_SELECTED_")); ?></span>
			<a href="javascript:;" class="btn btn_link" style="float:right;margin-right:15px;"><span style="font-size:14px;" id="newAdd"><?php echo (L("_COMMON_ADD_")); ?>+</span></a>
        </div>
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="template">
        	<tr id="Jtr">
				<th><?php echo (L("_COMMON_ALL_SELECTED_")); ?><input type="checkbox" id="ids"></th>
				<th><?php echo (L("_COMMON_CATEGORY_NAME_")); ?></th>
				<th><?php echo (L("_PC_GOODS_PRODUCTS_NUMBER_")); ?></th>
				<th><?php echo (L("_COMMON_SORT_")); ?></th>
				<th><?php echo (L("_COMMON_OPERATE_")); ?></th>
			</tr>
        </table>
        <div class="page-box" id="pageBox"></div>
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
        
<script id="category_tpl" type="text/html">
<%for(var i = 0; i < list.length; i ++){%>
	<%var data = list[i];%>
	<tr data-id="<%=data['id']%>" class="JgetID">
	    <td><input type="checkbox" name="checkbox" class="ids" value="{$goods.id}"/></td>
		<td>
			<input type="text" id="JcategoryName_<%=data['id']%>" value="<%=data['category_name']%>" readonly="readonly" class="intxt" style="border:none;box-shadow:none;-webkit-box-shadow:none;-moz-box-shadow:none;text-align:center;">
			<em class="txtEdit Tname" data-id="<%=data['id']%>"></em>
		</td>
		<td><%=data['goods_number']%></td>
		<td>
			<input type="text" id="JcategorySort_<%=data['id']%>" value="<%=data['sort']%>" readonly="readonly" class="intxt" style="border:none;box-shadow:none;-webkit-box-shadow:none;-moz-box-shadow:none;text-align:center;">
			<em class="txtEdit Tsort" data-id="<%=data['id']%>"></em>
		</td>
		<td class="center">
		<a class="stdbtn btn_lime" href="<?php echo U('Agent/goodsModel');?>?id=<%=data['id']%>"><?php echo (L("_PC_GOODS_EDIT_GOODS_MODEL_")); ?></a>&nbsp;&nbsp;
			<a class="stdbtn btn_lime" href="javascript:;" id="delType"><?php echo (L("_COMMON_DELETE_")); ?></a>
		</td>
	</tr>
<%}%>
</script>
<script type="text/javascript">
	var editbol = true,//控制修改还是完成状态
		addtime = true,//控制新增的频率
		page = 1,
		categoryId = "",//分类ID
		checknum = 0,//分类被选中的个数
		delcateId = [],//批量删除ID数组
		delId = "";//批量删除传后台ID
	var selectedLang = "<?php echo (L("_PC_GOODS_RIGHT_HAS_SELECTED_")); ?>";
	/*使用模板引擎*/
    var bt = baidu.template;
    function loadData(page) {
    	popupWin.show('<?php echo (L("_COMMON_LOADING_")); ?>');
    	$.ajax({
			url: '<?php echo U('Category/agentGoodsCategory');?>',
			type: 'POST',
			dataType: 'json',
			data: {
				id : "<?php echo (session('agentId')); ?>",
				page : page,
				type : 1
			}
		})
		.done(function(returnData) {
			if ( returnData['data']['list'].length ) {
				var html = bt('category_tpl', returnData['data']);				
				$('#Jtr').nextAll().remove();
				$('#Jtr').after(html);
				createPageTags(returnData['data']['page'], returnData['data']['count'], 0);
				listenPageEvent(loadData);
			} else {
				alert('<?php echo (L("_COMMON_NO_DATA_")); ?>');
			}
			popupWin.hide();
		});
    }
    loadData(1);

    // 编辑分类
    function editCategory(category_name,categoryId,sort) {
		$.ajax({
			url: '<?php echo U('Agent/editAgentGoodsCategory');?>',
			type: 'POST',
			dataType: 'json',
			data: {
				id : categoryId,
				category_name : category_name,
				sort : sort
			}
		})
		.done(function(returnData) {
			if ( returnData['status'] == '200000' ) {
				alert('<?php echo (L("_COMMON_SUCCESS_")); ?>');
				(function reload(){
					window.location.href = window.location.href;
				})();
			} else {
				alert(returnData['message']);
			}
		});
	}

	// 编辑名称
    $("#template").on('click','.Tname',function(e){
    	if(editbol){
    		//进入编辑
	    	$(this).addClass("on");
	    	$(this).siblings("input").css({"border":"1px solid #ccc","box-shadow":"inset 0 1px 3px #ddd","-webkit-box-shadow":"inset 0 1px 3px #ddd","-moz-box-shadow":"inset 0 1px 3px #ddd"});
	    	$(this).siblings("input").attr("readonly",false);
	    	editbol = false;
    	}else{
    		//完成编辑
    		categoryId = $(this).data('id');
    		var category_name = $('#JcategoryName_' + categoryId).val(),
    			sort = $('#JcategorySort_' + categoryId).val();
    		editCategory(category_name, categoryId, sort);
    		$(this).removeClass('on');
    		$(this).siblings("input").css({"border":"none","box-shadow":"none","-webkit-box-shadow":"none","-moz-box-shadow":"none"});
    		$(this).siblings("input").attr("readonly",true);
    		editbol = true;
    	}
    })

    // 编辑排序
    $("#template").on('click','.Tsort',function(e){
    	if(editbol){
    		//进入编辑
	    	$(this).addClass("on");
	    	$(this).siblings("input").css({"border":"1px solid #ccc","box-shadow":"inset 0 1px 3px #ddd","-webkit-box-shadow":"inset 0 1px 3px #ddd","-moz-box-shadow":"inset 0 1px 3px #ddd"});
	    	$(this).siblings("input").attr("readonly",false);
	    	editbol = false;
    	}else{
    		//完成编辑
    		categoryId = $(this).data('id');
    		var category_name = $('#JcategoryName_' + categoryId).val(),
    			sort = $('#JcategorySort_' + categoryId).val();
    		editCategory(category_name, categoryId, sort);
    		$(this).removeClass('on');
    		$(this).siblings("input").css({"border":"none","box-shadow":"none","-webkit-box-shadow":"none","-moz-box-shadow":"none"});
    		$(this).siblings("input").attr("readonly",true);
    		editbol = true;
    	}
    })

    //新增分类
    function addCategory(category_name) {
		$.ajax({
			url: '<?php echo U('Agent/addAgentGoodsCategory');?>',
			type: 'POST',
			dataType: 'json',
			data: {category_name : category_name}
		})
		.done(function(returnData) {
			if ( returnData['status'] == '200000' ) {
				alert('添加分类成功');
				(function reload(){
					window.location.href = window.location.href;
				})();
			} else {
				alert(returnData['message']);
			}
		});
	}
    $("#newAdd").click(function(){
    	var html = "<tr style='border:2px solid rgba(121, 121, 121, 1);' id='Addtr'><td></td><td><input type='text' name='' placeholder='<?php echo (L("_COMMON_PLEASE_ENTER_")); echo (L("_COMMON_CATEGORY_NAME_")); ?>' value='' style='border:none;box-shadow:none;-webkit-box-shadow:none;-moz-box-shadow:none;text-align:center;border-bottom:1px solid #ccc;'></td><td>0</td><td>1</td><td class='center'><a class='stdbtn btn_lime' href='javascript:;' id='Newcancel'><?php echo (L("_COMMON_CANCEL_")); ?></a>&nbsp;&nbsp;<a class='stdbtn btn_lime' href='javascript:;' id='Newsave'><?php echo (L("_COMMON_SAVE_")); ?></a></td></tr>";
    	if(addtime){
    		$("#Jtr").after(html);
    		addtime = false;
    	}
    })
    //保存
    $("#template").on('click','#Newsave',function(){
    	var category_name = $("#Addtr").find("input").val();
    	if( !category_name ){
    		alert("分类名不能为空！");
    		return;
    	}
    	addCategory(category_name);
    	addtime = true;
    	$("#Addtr").remove();
    })
    //取消
    $("#template").on('click','#Newcancel',function(){
    	addtime = true;
    	$("#Addtr").remove();
    })

    //删除分类
    function delCategory(categoryId) {
		$.ajax({
			url: '<?php echo U('Agent/delAgentGoodsCategory');?>',
			type: 'POST',
			dataType: 'json',
			data: {id : categoryId }
		})
		.done(function(returnData) {
			if ( returnData['status'] == '200000' ) {
				alert('删除分类成功');
				(function reload(){
					window.location.href = window.location.href;
				})();
			} else {
				alert(returnData['message']);
			}
		});
	}
	$("#template").on('click','#delType',function(){
		categoryId = $(this).parent().parent().attr("data-id");
		delCategory(categoryId);
	})

	//全部勾选
	$("#template").on("change","#ids",function(){
		if( !$(this).attr("checked") ){
			$("input[type='checkbox']").attr("checked",false);
			$(".selnum").text("0" + selectedLang);
			checknum = 0;
			delId = "";
			delcateId = [];
			return;	
		}
		delId = "";
		delcateId = [];
		var len = $("#template").find("input[type='checkbox']").length;
		checknum = len - 1;
		$("input[type='checkbox']").attr("checked",true);
		$(".selnum").text(checknum + selectedLang);
		$("#template").find(".JgetID").each(function(){
			delcateId.push($(this).attr("data-id"));
		})
		delId = delcateId.join(",");
	});

	// 单选
	$("#template").on("change",".ids",function(){
		categoryId = $(this).parent().parent().attr("data-id");
		if( !$(this).attr("checked") ){
			checknum-=1;
			$(".selnum").text(checknum + selectedLang);
			for(var i = 0;i<delcateId.length;i++){
				if(categoryId == delcateId[i]){
					delcateId.splice(i,1);
				}
			}
			delId = delcateId.join(",");
			return;
		}
		checknum+=1;
		$(".selnum").text(checknum + selectedLang);
		delcateId.push(categoryId);
		delId = delcateId.join(",");
	});

	//批量删除
	$("#Jdel").click(function(){
		if ( !delId ) {
			alert('<?php echo (L("_PC_GOODS_DELETE_CATEGORY_")); ?>');
			return;
		}
		delCategory(delId);
	})
</script>

    </body>
</html>