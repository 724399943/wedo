<?php
namespace Shop\Controller;
use Think\Controller;
class CategoryController extends BaseController {
    /**
     * [agentCategory 店铺分类]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function agentCategory() {
        if ( IS_POST ) {
            // $agentCategoryList = S('agentCategoryList');
            // if ( empty($agentCategoryList) ) {
                $agentCategoryList = M('agent_category')->field('`id`, `category_name`, `pid`, `category_path`, `app_icon`')->order('`sort` ASC')->select();
                // $agentCategoryList = $this->recursiveCategory(0, $agentCategoryList);
                // S('agentCategoryList', $agentCategoryList, 3600);
            // }

            exit(statusCode(array('list'=> $agentCategoryList)));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
    * [recursiveCategory 递归分类信息]
    * @author StanleyYuen <[350204080@qq.com]>
    */
    public function recursiveCategory($pid, $list = '') {
        static $categoryList;
        if ( !empty($list) ) {
            $categoryList = $list;
        }

        $childList = array();
        foreach ($categoryList as $key => $value) {
            if ( $value['pid'] == $pid ) {
                $childList[]    = $value;
                unset($categoryList[$key]);
            }
        }

        if (empty($childList)) {
            return false;
        } else {
            $result = array();
            foreach ($childList as $cvalue) {
                $tempResult = $this->recursiveCategory($cvalue['id']);
                if (!empty($tempResult)) {
                    foreach ($tempResult as &$value) {
                        $value['path'] = $pid . '-' . $value['path'];
                    }
                    $cvalue['childCategory'] = $tempResult;
                }
                $result[] = $cvalue;
            }

            return $result;
        }
    }

    /**
     * [agentGoodsCategory 店铺内商品分类]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function agentGoodsCategory() {
        if ( IS_POST ) {
            $id = I('id');
            $type = I('type', '0');
            $agentGoodsCategory = M('agent_goods_category');
            $where['agent_id'] = $id;
            $list = $agentGoodsCategory->where($where)->field('`id`, `category_name`, `sort`')->limit($this->limitStart.','.PAGE_LIMIT)->select();
            $count = 0;
            if ( !empty($type) ) {
                $goodsModel = M('goods');
                foreach ($list as $key => &$value) {
                    $value['goods_number'] = $goodsModel->where(array('agent_category_id'=> $value['id']))->count();
                }
                $count = $agentGoodsCategory->where($where)->count();
            }
            $returnData['list'] = $list;
            $returnData['page'] = $this->page + 1;
            $returnData['count'] = ceil($count / PAGE_LIMIT);
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [goodsCategory 平台商品分类]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function goodsCategory() {
        if ( IS_POST ) {
            $goodsCategory = S('goodsCategory');
            if ( empty($goodsCategory) ) {
                $goodsCategory = M('goods_category')->field('`id`, `category_name`, `pid`, `category_path`, `app_icon`')->order('`sort` DESC')->select();
                // $goodsCategory = $this->recursiveCategory(0, $goodsCategory);
                // S('goodsCategory', $goodsCategory, 3600);
            }

            exit(statusCode(array('list'=> $goodsCategory)));
        } else {
            exit(statusCode(array(), 100001));
        }
    }
}