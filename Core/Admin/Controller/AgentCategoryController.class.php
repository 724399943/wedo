<?php
namespace Admin\Controller;
use Think\Controller;
class AgentCategoryController extends BaseController {
    /**
     * [agentCategory 店铺分类管理]
     * @author shichun <672517056@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function agentCategory() {
        $categoryRelevanceModel = M('agent_category_relevance');
        $categoryModel = M('agent_category');
        $category_name = I('get.keywords', '', 'urldecode');
        if ( !empty($category_name) ) {
           $where['category_name'] = array('LIKE', "%$category_name%");
        }
        $count = $categoryModel->where($where)->count();
        $Page = new \Think\Page($count, 25);
        $show = $Page->show(); 
        $counting = $Page->totalRows;
        $categoryList = $categoryModel->where($where)->field('id,category_name,app_icon,pid,sort')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($categoryList as $key => &$value) {
            $value['count'] = $categoryRelevanceModel->where(array('category_id'=> $value['id']))->count();
        }
        $this->assign('show', $show);
        $this->assign('categoryList', $categoryList);
        $this->assign('counting', $counting);
        $this->assign('keywords', $keywords);
        $this->display();
    }

    /**
     * [addAgentCategory 添加店铺分类]
     * @author shichun <672517056@qq.com>
     * @copyright Copyright (c) 2017 Xcrozz (http://www.xcrozz.com)
     */
    public function addAgentCategory() {
        if ( IS_POST ) {
            $agentCategory = D('agent_category');
            $data = $agentCategory->create(I('post.'), 1);
            if ( !empty($data) ) {
                ( $agentCategory->add($data) !== false ) ? 
                    $this->success('添加成功', U('AgentCategory/agentCategory')) : 
                    $this->error('添加失败', U('AgentCategory/agentCategory'));
            } else {
                exit(statusCode(array(), 400000, $agentCategory>getError()));
            }
        } else {
            $this->display();
        }
    }

    /**
     * [editAgentCategory 编辑店铺分类]
     * @author shichun <672517056@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function editAgentCategory() {
        $agentCategory = D('agent_category');
        if ( IS_POST ) {
            $data = $agentCategory->create(I('post.'), 2);
            if ( !empty($data) ) {
                ( $agentCategory->save($data) !== false ) ? 
                    $this->success('编辑成功', U('AgentCategory/agentCategory')) : 
                    $this->error('编辑失败', U('AgentCategory/agentCategory'));
            } else {
                exit(statusCode(array(), 400000, $agentCategory->getError()));
            }
        } else {
            $id = I('get.id');
            $agentcat = $agentCategory->where(array('id'=>$id))->find($id);
            $this->assign('agentcat', $agentcat);
            $this->display();
        }
    }

    /**
     * [delAgentCategory 删除店铺分类]
     * @author shichun <672517056@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function delAgentCategory() {
        $id = I('id');
        $agent_category_relevance = M('agent_category_relevance');
        $agent_category = D('agent_category');
        if ( strpos($id, ',') !== false ) {
            $idData = explode(',', trim($id, ','));
            foreach ($idData as $value) {
                if ( $agent_category_relevance->where(array('category_id'=> $value))->count() > 0 ) {
                    $this->error('该分类下有所属商品，不可删除', U('AgentCategory/agentCategory'));
                }
            }
            if ( $agent_category>where(array('id'=> array('IN', $idData)))->delete() ) {
                $this->success('删除成功', U('AgentCategory/agentCategory'));
            } else {
                $this->error('删除失败', U('AgentCategory/agentCategory'));
            }
        } else {
            if ( $agent_category_relevance->where(array('category_id'=> $id))->count() > 0 ) {
                $this->error('该分类下有所属商品，不可删除', U('AgentCategory/agentCategory'));
            }
            if ( $agent_category->delete($id) ) {
                $this->success('删除成功', U('AgentCategory/agentCategory'));
            } else {
                $this->error('删除失败', U('AgentCategory/agentCategory'));
            }
        }
    }

    // 上传图片
    public function photoUpload() {
        // 图片保存路径
        fileUpload('AgentCategory/', function($e) {
            echo json_encode(array('error'=>0, 'url'=>trim($e['filePath'], '.')));
        });
    }
}