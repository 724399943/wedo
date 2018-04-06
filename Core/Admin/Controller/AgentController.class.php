<?php
namespace Admin\Controller;
class AgentController extends BaseController {
    /**
     * [checkAgentList 商家审核]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function checkAgentList() {
        $dbPrefix = C('DB_PREFIX');
        $agentCategory = M('agent_category');
        $agentCategoryRelevance = M('agent_category_relevance');
        $agent_name     = I('agent_name', '', 'urldecode');
        $nickname       = I('nickname', '', 'urldecode');
        $status         = I('status', '-1');
        $startTime      = I('get.start_time', '');
        $endTime        = I('get.end_time', '');
        $category_id    = I('get.category_id', '');
        $manager        = I('get.manager', '', 'urldecode');
        $whereStr = ' AND `a`.`status` != \'1\'';
        $link_parameter = '';
        if ( !empty($agent_name) ) {
            $whereStr .= " AND `a`.`agent_name` LIKE '%{$agent_name}%'";
            $link_parameter .= '/agent_name/' . $agent_name;
        }

        if ( !empty($nickname) ) {
            $whereStr .= " AND `u`.`nickname` LIKE '%{$nickname}%'";
            $link_parameter .= '/nickname/' . $nickname;
        }

        if ( !empty($manager) ) {
            $whereStr .= " AND `a`.`manager` LIKE '%{$manager}%'";
            $link_parameter .= '/manager/' . $manager;
        }

        if ( $status != '-1' ) {
            $whereStr .= " AND `a`.`status` = '{$status}'";
            $link_parameter .= '/status/' . $status;
        }

        if ( !empty($startTime) && !empty($endTime) ) {
            $whereStr .= " AND `a`.`add_time` BETWEEN {$startTime} AND {$endTime}";
            $link_parameter .= '/startTime/' . $startTime . '/endTime/' . $endTime;
        } else {
            $startTime = strtotime(date('Y', time()) . '-01-01');
            $endTime = strtotime('+1 days');
        }

        if ( !empty($category_id) ) {
            $agentData = $agentCategoryRelevance->where(array('category_id'=> $category_id))->field('`agent_id`')->select();
            if ( !empty($agentData) ) {
                $agentData = implode(',', array_column($agentData, 'agent_id'));
                $whereStr .= " AND `a`.`id` IN($agentData) ";
            } else {
                $whereStr .= " AND `a`.`id` = '0'";
            }
            $link_parameter .= '/category_id/' . $category_id;
        }

        $sql = "SELECT COUNT(*) AS `count` 
                FROM `{$dbPrefix}agent` AS `a` 
                LEFT JOIN `{$dbPrefix}user` AS `u` ON `a`.`user_id` = `u`.`id` 
                WHERE 1{$whereStr}";
        $count = M()->query($sql);
        $count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
        $page = new \Think\Page($count, 25);
        $page->setConfig('link', '/Admin/Agent/checkAgentList/p/zz' . $link_parameter);
        $show = $page->show();
        $counting = $page->totalRows;
        $sql = "SELECT `a`.*, `u`.`nickname` 
                FROM `{$dbPrefix}agent` AS `a` 
                LEFT JOIN `{$dbPrefix}user` AS `u` ON `a`.`user_id` = `u`.`id` 
                WHERE 1{$whereStr} 
                ORDER BY `a`.`id` DESC 
                LIMIT {$page->firstRow} , {$page->listRows}";
        $list = M()->query($sql);
        foreach ($list as $key => &$value) {
            $ids = $agentCategoryRelevance->where(array('agent_id'=> $value['id']))->select();
            if ( !empty($ids) ) {
                $idsData = array_column($ids, 'category_id');
                $categoryData = $agentCategory->where(array('id'=> array('IN', $idsData)))->field('`category_name`')->select();
                $value['category_name'] = implode(',', array_column($categoryData, 'category_name'));
            } else {
                $value['category_name'] = '-';
            }
        }
        $return = array(
            'agent_name' => $agent_name,
            'nickname' => $nickname,
            'status' => $status,
            'manager' => $manager,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'category_id' => $category_id
        );
        $categoryList = $agentCategory->select();
        $this->assign('show', $show);
        $this->assign('list', $list);
        $this->assign('return', $return);
        $this->assign('counting', $counting);
        $this->assign('categoryList', $categoryList);
        $this->display();
    }

    /**
     * [agentDetail 查看商家详情]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function agentDetail() {
        if ( IS_POST ) {

        } else {
            $id = I('get.id', '', 'int');
            $agentModel = M('agent');
            $goodsModel = M('goods');
            $agentCategory = M('agent_category');
            $agentCategoryRelevance = M('agent_category_relevance');
            if ( empty($id) ) { 
                $this->error('参数丢失！');
            }
            $agentInfo = $agentModel->find($id);
            if (empty($agentInfo)) {
                $this->error('没有该会员！');
            }
            $userInfo = M('user')->find($agentInfo['user_id']);

            //商家分类
            $ids = $agentCategoryRelevance->where(array('agent_id'=> $id))->select();
            if ( !empty($ids) ) {
                $idsData = array_column($ids, 'category_id');
                $categoryData = $agentCategory->where(array('id'=> array('IN', $idsData)))->field('`category_name`')->select();
                $agentInfo['category_name'] = implode(',', array_column($categoryData, 'category_name'));
            } else {
                $agentInfo['category_name'] = '-';
            }

            $this->assign('agentInfo', $agentInfo);
            $this->assign('userInfo', $userInfo);
            $this->display();
        }
    }

    /**
     * [checkAgent 进行审核商家]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function checkAgent() {
        $id = I('id', '');
        $status = I('status', '');
        if ( empty($id) || empty($status) ) {
            $this->error('没有该商家');
        }
        $agentModel = M('agent');
        ( $agentModel->save(array('id'=> $id, 'status'=> $status)) !== false ) ? 
            exit(statusCode()) : 
            exit(statusCode(array(), 400000, '审核失败'));
    }

    /**
     * [agentList 商家商品管理]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function agentList() {
        $dbPrefix = C('DB_PREFIX');
        $agent  = M('agent');
        $goods = M('goods');
        $orderModel = M('order');
        $agent_name  = I('get.agent_name', '','urldecode');
        $manager  = I('get.manager', '','urldecode');
        $account  = I('get.account', '','urldecode');
        $category_id  = I('get.category_id', '','urldecode');
        $categoryModel = M('agent_category');
        $agentCategoryRelevance = M('agent_category_relevance');
        $link_parameter = '';
        $whereStr = '';
        //店铺名称
        if (!empty($agent_name)) {
            $whereStr .= " AND `a`.`agent_name` LIKE '%{$agent_name}%'";
            $link_parameter .= '/agent_name/' . $agent_name;
        }
        //真实姓名
        if (!empty($manager)) {
            $whereStr .= " AND `a`.`manager` LIKE '%{$manager}%'";
            $link_parameter .= '/manager/' . $manager;
        }
        //账号
        if (!empty($account)) {
            $whereStr .= " AND `a`.`account` LIKE '%{$account}%'";
            $link_parameter .= '/account/' . $account;
        }
        //店铺分类
        if ( !empty($category_id )){
            $agentData = $agentCategoryRelevance->where(array('category_id'=>$category_id))->field('agent_id')->select();
            if( !empty($agentData) ){
                $agentData = implode(',',array_column($agentData,'agent_id'));
                $whereStr.= " AND `a`.`id` IN($agentData) ";
            } else{
                $whereStr.= " AND `a`.`id` = '0'";
            }
            $link_parameter .= '/category_id/' . $category_id;
        }
        $sql = "SELECT count(*) AS `count`
                FROM `{$dbPrefix}agent` AS `a` 
                LEFT JOIN `{$dbPrefix}user` AS `u` ON `u`.`id` = `a`.`user_id` 
                WHERE 1{$whereStr}";
        $count = M()->query($sql);
        $count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
        $page = new \Think\Page($count, 25);
        $show = $page->show();
        $counting = $page->totalRows;
        $sql = "SELECT `a`.`id`,`a`.`account`,`a`.`is_on_sale`,`a`.`agent_name`,`a`.`address`,`a`.`manager`,`a`.`goods_number`,`u`.`add_time`,`u`.`order_number`,`u`.`last_login_time` 
                FROM `{$dbPrefix}agent` AS `a`  
                LEFT JOIN `{$dbPrefix}user` AS `u` ON `u`.`id` = `a`.`user_id`
                WHERE 1{$whereStr} 
                ORDER BY `a`.`id` DESC 
                LIMIT {$page->firstRow}, {$page->listRows}";
        $list = M()->query($sql); 
        foreach($list as $key => &$value){
            //商家分类
            $ids = $agentCategoryRelevance->where(array('agent_id'=>$value['id']))->select();
            if ( !empty($ids) ) {
                $idsData = array_column($ids,'category_id');
                $categoryData = $categoryModel->where(array('id'=>array('IN', $idsData)))->field('`category_name`')->select();
                $value['category_name'] = implode(',',array_column($categoryData,'category_name'));
            } else {
                $value['category_name'] = '-';
            }
        }
        $return['category_name'] = $category_name;
        $return['agent_name'] = $agent_name;
        $return['manager'] = $manager;
        $return['account'] = $account;

        $categoryList = $categoryModel->select();
        $this->assign('show', $show);
        $this->assign('list', $list);
        $this->assign('return', $return);
        $this->assign('counting',$counting);
        $this->assign('categoryList', $categoryList);
        $this->display();
    }

    /**
     * [agentGoods 查看商家商品]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function agentGoods() {
        $id = I('id');
        $agentGoodsCategoryModel = M('agent_goods_category');
        $userCollectModel = M('user_collect');
        $goodsModel = M('goods');
        $goods_name     = I('get.goods_name', '', 'urldecode');
        $goods_type     = I('get.goods_type','-1');
        $is_on_sale     = I('get.is_on_sale','-1');
        $category_id    = I('get.category_id','-1');
        $agent_category_id = I('get.agent_category_id','-1');
        $link_parameter = '';
        //商品    
        if ($goods_name != '') {
            $where['goods_name'] = array('LIKE' , "%{$goods_name}%");
            $link_parameter .= '/goods_name/' . $goods_name;
        }
        //类型
        if ($goods_type != '-1') {
            $where['agent_id'] = empty($goods_type) ? '0' : array('NEQ', '0');
            $link_parameter .= '/goods_type/' . $goods_type;
        }
        //状态
        if ($is_on_sale != '-1') {
            $where['is_on_sale'] = $is_on_sale;
            $link_parameter .= '/is_on_sale/' . $is_on_sale;
        }
        if ($category_id != '-1') {
            $where['category_id'] = $category_id;
            $link_parameter .= '/category_id/' . $category_id;
        }
        if ($agent_category_id != '-1') {
            $where['agent_category_id'] = $agent_category_id;
            $link_parameter .= '/agent_category_id/' . $agent_category_id;
        }

        $where['goods_main_id'] = '0';
        $where['is_delete'] = '0';
        $where['agent_id'] = $id;
        $count = $goodsModel->where($where)->count();
        $page = new \Think\Page($count, 25);
        $page->setConfig('link', '/Admin/Agent/agentGoods/p/zz/id/' . $id . $link_parameter);
        $show = $page->show();
        $counting = $page->totalRows;
        $goodsList = $goodsModel->where($where)->limit($page->firstRow, $page->listRows)->select();
        
        foreach ($goodsList as $key => &$value) {
            $value['agent_name'] = getAgentName($value['agent_id']);
            $value['category_name'] = getCategoryName($value['category_id']);
            $value['agent_category_name'] = getAgentCategoryName($value['agent_category_id']);
            $value['collect_number'] = $userCollectModel->where(array('goods_id'=> $value['id']))->count();
        }

        $return = array(
            'goods_name' => $goods_name,
            'goods_type' => $goods_type,
            'is_on_sale' => $is_on_sale,
            'id' => $id,
            'category_id' => $category_id,
            'agent_category_id' => $agent_category_id,
        );
        $goodsCategoryList = M('goods_category')->select();
        $agentGoodsCategoryList = M('agent_goods_category')->where(array('agent_id'=> $id))->select();

        $goodsCount = $goodsModel->where(array('agent_id'=> $id))->count();
        $onSaleCount = $goodsModel->where(array('agent_id'=> $id, 'is_on_sale'=> '1'))->count();
        $offSaleCount = $goodsModel->where(array('agent_id'=> $id, 'is_on_sale'=> '0'))->count();
        $categoryCount = $agentGoodsCategoryModel->where(array('agent_id'=> $id))->count();

        $this->assign("show", $show);
        $this->assign('return', $return);
        $this->assign("counting", $counting);
        $this->assign('goodsList', $goodsList);
        $this->assign('goodsCategoryList', $goodsCategoryList);
        $this->assign('agentGoodsCategoryList', $agentGoodsCategoryList);
        $this->assign('goodsCount', $goodsCount);
        $this->assign('onSaleCount', $onSaleCount);
        $this->assign('offSaleCount', $offSaleCount);
        $this->assign('categoryCount', $categoryCount);
        $this->display();
    }

    /**
     * [salesStatistics 销售统计]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function salesStatistics() {
        $p = I('get.p', 1);
        $select_time = I('get.select_time');
        $category_id = I('get.category_id');
        $dbPrefix = C('DB_PREFIX');
        $whereStr = '';
        $link_parameter = '';
        $agentModel = M('agent');
        $goodsModel = M('goods');
        $agentCategoryModel = M('agent_category');
        $agentSalesStatisticsModel = M('agent_sales_statistics');
        $agentCategoryRelevanceModel = M('agent_category_relevance');
        if ( !empty($category_id) ) {
            $agentData = $agentCategoryRelevanceModel->where(array('category_id'=> $category_id))->field('`agent_id`')->select();
            if ( !empty($agentData) ) {
                $agentData = implode(',', array_column($agentData, 'agent_id'));
                $where['id'] = array('IN', $agentData);
            } else {
                $where['id'] = '0';
            }
            $link_parameter .= '/category_id/' . $category_id;
        }
        if ( !empty($select_time) ) {
            $selectMonth = date('m', $select_time);
            $link_parameter .= '/select_time/' . $select_time;
        } else {
            $select_time = strtotime(date('Y-m'));
            $selectMonth = date('m', $select_time);
        }
        $count = $agentModel->where($where)->count();
        $page = new \Think\Page($count, 25);
        $page->setConfig('link', '/Admin/Agent/salesStatistics/p/zz' . $link_parameter);
        $show = $page->show();
        $counting = $page->totalRows;
        $list = $agentModel->where($where)->field('`id`, `agent_name`, `all_sales`, `all_income`')->order('`all_income` DESC')->limit($page->firstRow. ',' .$page->listRows)->select();
        foreach ($list as $key => &$value) {
            $map['agent_id'] = $value['id'];
            $ids = $agentCategoryRelevanceModel->where($map)->select();
            if ( !empty($ids) ) {
                $idsData = array_column($ids, 'category_id');
                $categoryData = $agentCategoryModel->where(array('id'=> array('IN', $idsData)))->field('`category_name`')->select();
                $value['category_name'] = implode(',', array_column($categoryData, 'category_name'));
            } else {
                $value['category_name'] = '-';
            }
            $salesMap = [
                'agent_id' => $value['id'],
                'date' => date('Ym', $select_time), 
            ];
            $salesStatistics = $agentSalesStatisticsModel->where($salesMap)->field('`month_sales`, `month_income`')->find();
            if ( empty($salesStatistics) ) {
                $salesStatistics['month_sales'] = 0;
                $salesStatistics['month_income'] = 0;
            }
            $value = array_merge($value, $salesStatistics);
            $value['goods_total'] = $goodsModel->where($map)->count();
            $value['ranking'] = ($p - 1) * 25 + $key + 1;
        }
        $return = array(
            'select_time' => $select_time,
            'category_id' => $category_id
        );
        $categoryList = $agentCategoryModel->select();
        $this->assign('list', $list);
        $this->assign('show', $show);
        $this->assign('return', $return);
        $this->assign('counting', $counting);
        $this->assign('selectMonth', $selectMonth);
        $this->assign('categoryList', $categoryList);
        $this->display();
    }
}