<?php
namespace TPH\Controller;

use Think\Controller;

class ArController extends Controller {
    public function index(){
    	$page = I('page', 1);
    	$page = ($page == '0') ? 1 : $page;
    	$name = I('name');
    	$adminAuthRule = M('admin_auth_rule');
    	$where = array();
    	$tempStr = '';
    	if (!empty($name)) {
    		$where['name'] = array('LIKE', "%{$name}%");
    		$tempStr = '/name/' . $name; 
    	}
    	$limit = 20;
    	$limitStart = ($page - 1) * $limit;
    	$list = $adminAuthRule->where($where)->limit($limitStart . ',' . $limit)->select();
    	$count = $adminAuthRule->where($where)->count();
    	$pageNumber = ceil($count / $limit);

    	$show = '<ul class="pagination">' . "\n";
    	$show .= '<li><a href="/TPH/Ar/index/page/'.(($page-1) == '0' ? 1 : ($page-1)). $tempStr .'">Prev</a></li>' . "\n";
    	for ($i=0; $i < $pageNumber; $i++) { 
    		$show .= '<li><a href="/TPH/Ar/index/page/'.($i+1). $tempStr .'">'.($i+1).'</a></li>' . "\n";
    	}
    	$show .= '<li><a href="/TPH/Ar/index/page/'.(($page+1) > $pageNumber ? $pageNumber : ($page+1)). $tempStr .'">Next</a></li>' . "\n";
    	$show .= '</ul>';
    	$this->assign('list', $list);
    	$this->assign('page', $page);
    	$this->assign('show', $show);
		$this->display();
    }

    public function add() {
    	$adminAuthRule = M('admin_auth_rule');
    	if (IS_POST) {
    		$data = $adminAuthRule->create();
    		if ($adminAuthRule->add($data)) {
    			$this->success('添加成功', U('Ar/index'));
    		} else {
    			$this->error('添加失败');
    		}
    	} else {
    		$this->display();
    	}
    }

    public function edit() {
    	if (IS_POST) {
    		$form = I('form');
    		$adminAuthRule = M('admin_auth_rule');
    		foreach ($form as $key => $value) {
    			$adminAuthRule->save($value);
    		}
    		exit(json_encode(array('status'=> '200000', 'message'=> '保存成功')));
    	}
    }
}
