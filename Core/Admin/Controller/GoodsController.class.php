<?php
namespace Admin\Controller;
// 商品控制器
class GoodsController extends BaseController {
    private $goodsModel;
    public function __construct() {
        parent::__construct();
        $this->goodsModel = M('goods');
    }

    /**
     * [goodsList 商品列表]
     * @author kofu <[418382595@qq.com]>
     */
    public function goodsList() {
        $dbPrefix = C('DB_PREFIX');
        $userCollectModel = M('user_collect');
        $agent_name = I('get.agent_name', '', 'urldecode');
        $goods_name = I('get.goods_name', '', 'urldecode');
        $category_id = I('get.category_id', '');
        $is_on_sale = I('get.is_on_sale', '-1');
        $whereStr = ' AND `g`.`goods_main_id` = \'0\' AND `g`.`is_delete` = \'0\'';
        $link_parameter = '';
        if ( !empty($agent_name) ) {
            $whereStr .= " AND `a`.`agent_name` LIKE '%{$agent_name}%'";
            $link_parameter .= '/agent_name/' . $agent_name;
        }
        if ( !empty($goods_name) ) {
            $whereStr .= " AND `g`.`goods_name` LIKE '%{$goods_name}%'";
            $link_parameter .= '/goods_name/' . $goods_name;
        }
        if ( !empty($category_id) ) {
            $whereStr .= " AND `g`.`category_id` = '{$category_id}'";           
            $link_parameter .= '/category_id/' . $category_id;
        }
        if ( $is_on_sale != '-1' ) {
            $whereStr .= " AND `g`.`is_on_sale` = '{$is_on_sale}'";           
            $link_parameter .= '/is_on_sale/' . $is_on_sale;
        }
        
        $sql = "SELECT COUNT(*) AS `count`
                FROM `{$dbPrefix}goods` AS `g` 
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `g`.`agent_id` = `a`.`id` 
                WHERE 1{$whereStr}";
        $count = M()->query($sql);
        $count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
        $page = new \Think\Page($count, 25);
        $page->setConfig('link', '/Admin/Goods/goodsList/p/zz' . $link_parameter);
        $show = $page->show();
        $counting = $page->totalRows;
        $sql = "SELECT `g`.`id`, `g`.`goods_name`, `g`.`goods_image`, `g`.`category_id`, `g`.`sale_number`, `g`.`is_on_sale`, `g`.`goods_price`, `g`.`add_time`, `g`.`goods_number`, `a`.`agent_name` 
                FROM `{$dbPrefix}goods` AS `g` 
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `g`.`agent_id` = `a`.`id` 
                WHERE 1{$whereStr} 
                ORDER BY `g`.`id` DESC
                LIMIT {$page->firstRow} , {$page->listRows}";
        $list = $this->goodsModel->query($sql);
        foreach ($list as $key => &$value) {
            $value['category_name'] = getCategoryName($value['category_id']);
            $where = array(
                'goods_id' => $value['id'],
                'type' => '0'
            );
            $value['collect_number'] = $userCollectModel->where($where)->count();
        }
        $return = array(
            'agent_name' => $agent_name,
            'goods_name' => $goods_name,
            'category_id' => $category_id,
            'is_on_sale' => $is_on_sale,
        );
        $categoryList = M('goods_category')->field('`id`, `category_name`')->select();
        $this->assign('show', $show);
        $this->assign('list', $list);
        $this->assign('return', $return);
        $this->assign('counting', $counting);
        $this->assign('categoryList', $categoryList);
        $this->display();
    }

    /**
     * [goodsRanking 商品查询排行]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function goodsRanking() {
        $p = I('get.p', 1);
        $dbPrefix = C('DB_PREFIX');
        $userCollectModel = M('user_collect');
        $agent_name = I('get.agent_name', '', 'urldecode');
        $goods_name = I('get.goods_name', '', 'urldecode');
        $category_id = I('get.category_id', '');
        $is_on_sale = I('get.is_on_sale', '-1');
        $whereStr = ' AND `g`.`goods_main_id` = \'0\' AND `g`.`is_delete` = \'0\'';
        $link_parameter = '';
        if ( !empty($agent_name) ) {
            $whereStr .= " AND `a`.`agent_name` LIKE '%{$agent_name}%'";
            $link_parameter .= '/agent_name/' . $agent_name;
        }
        if ( !empty($goods_name) ) {
            $whereStr .= " AND `g`.`goods_name` LIKE '%{$goods_name}%'";
            $link_parameter .= '/goods_name/' . $goods_name;
        }
        if ( !empty($category_id) ) {
            $whereStr .= " AND `g`.`category_id` = '{$category_id}'";           
            $link_parameter .= '/category_id/' . $category_id;
        }
        if ( $is_on_sale != '-1' ) {
            $whereStr .= " AND `g`.`is_on_sale` = '{$is_on_sale}'";           
            $link_parameter .= '/is_on_sale/' . $is_on_sale;
        }
        
        $sql = "SELECT COUNT(*) AS `count`
                FROM `{$dbPrefix}goods` AS `g` 
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `g`.`agent_id` = `a`.`id` 
                WHERE 1{$whereStr}";
        $count = M()->query($sql);
        $count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
        $page = new \Think\Page($count, 25);
        $page->setConfig('link', '/Admin/Goods/goodsRanking/p/zz' . $link_parameter);
        $show = $page->show();
        $counting = $page->totalRows;
        $sql = "SELECT `g`.`id`, `g`.`goods_name`, `g`.`goods_image`, `g`.`category_id`, `g`.`sale_number`, `g`.`is_on_sale`, `g`.`goods_price`, `g`.`add_time`, `g`.`search_number`, `a`.`agent_name` 
                FROM `{$dbPrefix}goods` AS `g` 
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `g`.`agent_id` = `a`.`id` 
                WHERE 1{$whereStr} 
                ORDER BY `g`.`search_number` DESC
                LIMIT {$page->firstRow} , {$page->listRows}";
        $list = $this->goodsModel->query($sql);
        foreach ($list as $key => &$value) {
            $value['category_name'] = getCategoryName($value['category_id']);
            $where = array(
                'goods_id' => $value['id'],
                'type' => '0'
            );
            $value['collect_number'] = $userCollectModel->where($where)->count();
            $value['ranking'] = ($p - 1) * 25 + $key + 1;
        }
        $return = array(
            'agent_name' => $agent_name,
            'goods_name' => $goods_name,
            'category_id' => $category_id,
            'is_on_sale' => $is_on_sale,
        );
        $categoryList = M('goods_category')->field('`id`, `category_name`')->select();
        $this->assign('show', $show);
        $this->assign('list', $list);
        $this->assign('return', $return);
        $this->assign('counting', $counting);
        $this->assign('categoryList', $categoryList);
        $this->display();
    }

    /**
     * [setOnSale 设置商品上下架]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c) 2017 Xcrozz (http://www.xcrozz.com)
     */
    public function setOnSale() {
        $ids = I('ids');
        $is_on_sale = I('is_on_sale');
        $where['id'] = array('IN', trim($ids, ','));
        $saveData['is_on_sale'] = $is_on_sale;
        if ( $this->goodsModel->where($where)->save($saveData) !== false ) {
            $goodsData = explode(',', trim($ids, ','));
            foreach ($goodsData as $key => $value) {
                $this->goodsModel->where(array('goods_main_id'=> $value))->save($saveData);
            }
            if ( IS_POST ) {
                exit(statusCode());
            } else {
                $this->success('更新成功！');                
            }
        } else {
            if ( IS_POST ) {
                exit(statusCode('', 400000, '更新失败！'));
            } else {
                $this->error('更新失败！');
            }
        }
    }

    /**
     * [goodsDetail 商品详情]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function goodsDetail() {
        $id = I('get.id');
        $vo = $this->goodsModel->find($id);
        $goodsImagesModel = M('goods_images');
        $goodsCommentModel = M('goods_comment');
        $goodsExtensionModel = M('goods_extension');
        // 商品图片
        $goodsImages = $goodsImagesModel->where(array('id'=>array('IN', $vo['goods_images_id'])))->select();
        // 商品详情内容
        $goodsDesc = $goodsExtensionModel->where(array('id'=>$vo['goods_ext_id']))->getField('`goods_desc`');
        // 商品评价数量
        $commentCount = $goodsCommentModel->where(array('goods_id'=> $id))->count();
        $this->assign('vo', $vo);
        $this->assign('goodsImages', $goodsImages);
        $this->assign('goodsDesc', $goodsDesc);
        $this->assign('commentCount', $commentCount);
        $this->display();
    }

    /**
     * [goodsComment 商品评论列表]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function goodsComment() {
        if ( IS_POST ) {
            $goodsComment = D('GoodsComment');
            $limit = 25;
            $page = I('page', 1) - 1;
            $page  = $page < 0 ? 0 : $page;
            $limitStart = $page * $limit;
            $parameter = array(
                'goods_id'      => I('goods_id'),
                'type'          => I('type'),
                'page'          => $page,
                'limitStart'    => $limitStart,
                'limit'         => $limit,
            );
            $returnData = $goodsComment->getComment($parameter);
            exit(statusCode($returnData));
        } else {
            $this->display();
        }
    }
}