<?php
namespace [MODULE]\Controller;

use Think\Controller;

class [NAME] extends Controller
{
    public function index(){
    	$where = array();
    	$link_parameter = '';
[STARTCODE]
		$[TABLENAME]ViewModel = D('[TABLENAME]View');
		$count = $[TABLENAME]ViewModel->where($where)->count();
		$page = new \Think\Page($count,15);	//实例化分页类 传入总记录数和每页显示的记录数(15)
		$page->setConfig('link', '/[MODULE]/[TABLENAME]/index/p/zz' . $link_parameter);
		$show = $page->show();	//分页显示输出
		$list = $[TABLENAME]ViewModel->where($where)->limit($page->firstRow.','.$page->listRows)->select();	//分页查询
		$return = array();
[ENDCODE]
		$this->assign('show', $show);	//赋值分页输出
		$this->assign('return', $return);	
		$this->assign('list', $list);
		$this->display();
	}

	public function add[TABLENAME]() {
		if(IS_POST){
			$[TABLENAME] = D('[TABLENAME]');
			$data = $[TABLENAME]->create();
			if($[TABLENAME]->add($data)){
				$this->success('新建成功', U('[TABLENAME]/index')); 
			}else{
				$this->error('新建失败', U('[TABLENAME]/index')); 
			}
		}else{
			$this->display(); 
		}
	}

	public function edit[TABLENAME]() {
		$[TABLENAME] = D('[TABLENAME]');
		if(IS_POST){
			$data = $[TABLENAME]->create();
			if($[TABLENAME]->save($data) !== false){
				$this->success('编辑成功', U('[TABLENAME]/index')); 
			}else{
				$this->error('编辑失败', U('[TABLENAME]/index')); 
			}
		}else{
			$id = I('id'); 
			$vo = $[TABLENAME]->find($id);
			$this->assign('vo', $vo);
			$this->display();
		}
	}

	public function del[TABLENAME]() {
		$[TABLENAME] = D('[TABLENAME]');
		$id = I('id'); 
		if($[TABLENAME]->delete($id)){
			$this->success('删除成功', U('[TABLENAME]/index'));
		}else{
			$this->error('删除失败', U('[TABLENAME]/index'));
		}
	}
}
