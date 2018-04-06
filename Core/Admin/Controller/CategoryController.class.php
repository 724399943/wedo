<?php
namespace Admin\Controller;
use Think\Controller;
class CategoryController extends BaseController {
    /**
     * [goodsCategory 商品分类管理]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function goodsCategory() {
        $goodsModel = M('goods');
        $categoryModel = M('goods_category');
        $category_name = I('get.category_name', '', 'urldecode');
        if ( !empty($category_name) ) {
           $where['category_name'] = array('LIKE', "%$category_name%");
        }
        $count = $categoryModel->where($where)->count();
        $Page = new \Think\Page($count, 25);
        $show = $Page->show(); 
        $counting = $Page->totalRows;
        $categoryList = $categoryModel->where($where)->field('id,category_name,app_icon,pid,sort')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($categoryList as $key => &$value) {
            $value['count'] = $goodsModel->where(array('category_id'=> $value['id']))->count();
        }
        $this->assign('show', $show);
        $this->assign('categoryList', $categoryList);
        $this->assign('counting', $counting);
        $this->assign('category_name', $category_name);
        $this->display();
    }

    /**
     * [addGoodsCategory 添加商品分类]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c) 2017 Xcrozz (http://www.xcrozz.com)
     */
    public function addGoodsCategory() {
        if ( IS_POST ) {
            $goodsCategory = D('GoodsCategory');
            $data = $goodsCategory->create(I('post.'), 1);
            if ( !empty($data) ) {
                ( $goodsCategory->add($data) !== false ) ? 
                    $this->success('添加成功', U('Category/goodsCategory')) : 
                    $this->error('添加失败', U('Category/goodsCategory'));
            } else {
                $this->error($goodsCategory->getError());
            }
        } else {
            $this->display();
        }
    }

    /**
     * [editGoodsCategory 编辑商品分类]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function editGoodsCategory() {
        $goodsCategory = D('GoodsCategory');
        if ( IS_POST ) {
            $data = $goodsCategory->create(I('post.'), 2);
            if ( !empty($data) ) {
                ( $goodsCategory->save($data) !== false ) ? 
                    $this->success('编辑成功', U('Category/goodsCategory')) : 
                    $this->error('编辑失败', U('Category/goodsCategory'));
            } else {
                $this->error($goodsCategory->getError());
            }
        } else {
            $id = I('get.id');
            $goodscate = $goodsCategory->find($id);
            $this->assign('goodscate', $goodscate);
            $this->display();
        }
    }

    /**
     * [delGoodsCategory 删除商品分类]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function delGoodsCategory() {
        $id = I('id');
        $goods = M('goods');
        $goodsCategory = D('GoodsCategory');
        if ( strpos($id, ',') !== false ) {
            $idData = explode(',', trim($id, ','));
            foreach ($idData as $value) {
                if ( $goods->where(array('category_id'=> $value))->count() > 0 ) {
                    $this->error('该分类下有所属商品，不可删除', U('Category/goodsCategory'));
                }
            }
            if ( $goodsCategory->where(array('id'=> array('IN', $idData)))->delete() ) {
                $this->success('删除成功', U('Category/goodsCategory'));
            } else {
                $this->error('删除失败', U('Category/goodsCategory'));
            }
        } else {
            if ( $goods->where(array('category_id'=> $id))->count() > 0 ) {
                $this->error('该分类下有所属商品，不可删除', U('Category/goodsCategory'));
            }
            if ( $goodsCategory->delete($id) ) {
                $this->success('删除成功', U('Category/goodsCategory'));
            } else {
                $this->error('删除失败', U('Category/goodsCategory'));
            }
        }
    }

    // 上传图片
    public function photoUpload() {
        // 图片保存路径
        fileUpload('Category/', function($e) {
            echo json_encode(array('error'=>0, 'url'=>trim($e['filePath'], '.')));
        });
    }
}