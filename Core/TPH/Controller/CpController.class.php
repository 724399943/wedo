<?php
namespace TPH\Controller;

use Think\Controller;

class CpController extends Controller {
    public function index(){
    	$page = I('page', 1);
    	$page = ($page == '0') ? 1 : $page;
    	$controller_name = I('controller_name');
    	$cp = M('controller_power');
    	$where = array();
    	$tempStr = '';
    	if (!empty($controller_name)) {
    		$where['controller_name'] = array('LIKE', "%{$controller_name}%");
    		$tempStr = '/controller_name/' . $controller_name; 
    	}
    	$limit = 20;
    	$limitStart = ($page - 1) * $limit;
    	$list = $cp->where($where)->limit($limitStart . ',' . $limit)->select();
    	$count = $cp->where($where)->count();
    	$pageNumber = ceil($count / $limit);

    	$show = '<ul class="pagination">' . "\n";
    	$show .= '<li><a href="/TPH/Cp/index/page/'.(($page-1) == '0' ? 1 : ($page-1)). $tempStr .'">Prev</a></li>' . "\n";
    	for ($i=0; $i < $pageNumber; $i++) { 
    		$show .= '<li><a href="/TPH/Cp/index/page/'.($i+1). $tempStr .'">'.($i+1).'</a></li>' . "\n";
    	}
    	$show .= '<li><a href="/TPH/Cp/index/page/'.(($page+1) > $pageNumber ? $pageNumber : ($page+1)). $tempStr .'">Next</a></li>' . "\n";
    	$show .= '</ul>';
    	$this->assign('list', $list);
    	$this->assign('page', $page);
    	$this->assign('show', $show);
		$this->display();
    }

    public function add() {
    	$cp = M('controller_power');
    	if (IS_POST) {
    		$data = $cp->create();
    		if ($cp->add($data)) {
    			$this->success('添加成功', U('Cp/index'));
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
    		$cp = M('controller_power');
    		foreach ($form as $key => $value) {
    			$cp->save($value);
    		}
    		exit(json_encode(array('status'=> '200000', 'message'=> '保存成功')));
    	}
    }
}
