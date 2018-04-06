<?php
namespace Admin\Controller;

use Think\Controller;

class ArticleController extends BaseController
{
	/**
	 * [index 文章列表]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
    public function index(){
    	$where = array();
    	$link_parameter = '';
		$articleModel = D('Article');
		$count = $articleModel->where($where)->count();
		$page = new \Think\Page($count,15);	//实例化分页类 传入总记录数和每页显示的记录数(15)
		$page->setConfig('link', '/Admin/Article/index/p/zz' . $link_parameter);
		$show = $page->show();	//分页显示输出
		$counting = $page->totalRows;
		$list = $articleModel->where($where)->limit($page->firstRow.','.$page->listRows)->select();	//分页查询
		$return = array();
		$this->assign('page', $show);	//赋值分页输出
		$this->assign('list', $list);
		$this->assign('return', $return);	
		$this->assign('counting', $counting);	
		$this->display();
	}

	/**
	 * [editArticle 文章编辑]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
	public function editArticle() {
		$Article = D('Article');
		if ( IS_POST ) {
			$data = $Article->create();
			$data['update_time'] = time();
			$data['content'] = htmlspecialchars_decode($data['content']);
			if($Article->save($data) !== false){
				$this->success('编辑成功', U('Article/index')); 
			}else{
				$this->error('编辑失败', U('Article/index')); 
			}
		} else {
			$id = I('id'); 
			$vo = $Article->find($id);
			$vo['content'] = htmlspecialchars_decode($vo['content']);
			$this->assign('vo', $vo);
			$this->display();
		}
	}

	/**
	 * [articleDetail 文章详情]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
	public function articleDetail() {
		$id = I('get.id');
		$articleModel = M('article');
		if ( empty($id) ) {
			$this->error('没有该文章');
		}
		$vo = $articleModel->find($id);
		$this->assign('vo', $vo);
		$this->display();
	}

	// 图片上传使用
    public function photoUpload() {
        // 图片保存路径
        fileUpload('Article/', function($e) {
            echo json_encode(array('error'=>0, 'url'=>trim($e['filePath'], '.')));
        });
    }
}
