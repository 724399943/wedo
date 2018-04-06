<?php
namespace Admin\Controller;
class BiddingController extends BaseController {
    /**
     * [biddingIndexGoods 首页商品竞价管理]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function biddingIndexGoods() {
        $dbPrefix = C('DB_PREFIX');
        $whereStr = ' AND `b`.`is_pay` = \'1\' AND `b`.`bidding_type` = \'0\' AND `b`.`status` = \'0\' ';
        $agent_name     = I('get.agent_name', '', 'urldecode');
        $goods_name     = I('get.goods_name', '', 'urldecode');
        $category_id    = I('get.category_id', '');
        $startTime      = I('get.start_time','');
        $endTime        = I('get.end_time','');
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

        if ( !empty($startTime) && !empty($endTime) ) {
            $whereStr .= " AND `b`.`start_time` BETWEEN {$startTime} AND {$endTime}";
            $link_parameter .= '/startTime/' . $startTime . '/endTime/' . $endTime;
        } else {
            $startTime = strtotime(date('Y', time()) . '-01-01');
            $endTime = strtotime('+1 days');
        }

        $sql = "SELECT COUNT(*) AS `count` 
                FROM `{$dbPrefix}bidding` AS `b` 
                LEFT JOIN `{$dbPrefix}goods` AS `g` ON `b`.`goods_id` = `g`.`id` 
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `g`.`agent_id` = `a`.`id` 
                WHERE 1{$whereStr}";
        $count = M()->query($sql);
        $count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
        $page = new \Think\Page($count, 25);
        $page->setConfig('link', '/Admin/Bidding/biddingIndexGoods/p/zz' . $link_parameter);
        $show = $page->show();
        $counting = $page->totalRows;
        $sql = "SELECT `b`.*, `g`.`goods_name`, `g`.`introduction`, `g`.`category_id` 
                FROM `{$dbPrefix}bidding` AS `b` 
                LEFT JOIN `{$dbPrefix}goods` AS `g` ON `b`.`goods_id` = `g`.`id` 
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `g`.`agent_id` = `a`.`id` 
                WHERE 1{$whereStr} 
                ORDER BY `b`.`id` DESC 
                LIMIT {$page->firstRow} , {$page->listRows}";
        $list = M()->query($sql);
        foreach ($list as $key => &$value) {
            $value['category_name'] = getCategoryName($value['category_id']);
            $value['agent_name'] = getAgentName($value['agent_id']);
        }
        $return = array(
            'agent_name' => $agent_name,
            'goods_name' => $goods_name,
            'category_id' => $category_id,
            'startTime' => $startTime,
            'endTime' => $endTime
        );
        $categoryList = M('goods_category')->select();
        $this->assign('list', $list);
        $this->assign('show', $show);
        $this->assign('return', $return);
        $this->assign('counting', $counting);
        $this->assign('categoryList', $categoryList);
        $this->display();
    }

    /**
     * [auditBiddingIndexGoods 已审核首页商品竞价管理]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function auditBiddingIndexGoods() {
        $dbPrefix = C('DB_PREFIX');
        $whereStr = ' AND `b`.`is_pay` = \'1\' AND `b`.`bidding_type` = \'0\' ';
        $agent_name     = I('get.agent_name', '', 'urldecode');
        $goods_name     = I('get.goods_name', '', 'urldecode');
        $category_id    = I('get.category_id', '');
        $status         = I('get.status', '-1', 'string');
        $startTime      = I('get.start_time','');
        $endTime        = I('get.end_time','');
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

        if ( $status != '-1' ) {
            $whereStr .= " AND `b`.`status` = '{$status}'";
        } else {
            $whereStr .= " AND `b`.`status` != '0'";
        }

        if ( !empty($startTime) && !empty($endTime) ) {
            $whereStr .= " AND `b`.`start_time` BETWEEN {$startTime} AND {$endTime}";
            $link_parameter .= '/startTime/' . $startTime . '/endTime/' . $endTime;
        } else {
            $startTime = strtotime(date('Y', time()) . '-01-01');
            $endTime = strtotime('+1 days');
        }
        $sql = "SELECT COUNT(*) AS `count` 
                FROM `{$dbPrefix}bidding` AS `b` 
                LEFT JOIN `{$dbPrefix}goods` AS `g` ON `b`.`goods_id` = `g`.`id` 
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `g`.`agent_id` = `a`.`id` 
                WHERE 1{$whereStr}";
        $count = M()->query($sql);
        $count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
        $page = new \Think\Page($count, 25);
        $page->setConfig('link', '/Admin/Bidding/auditBiddingIndexGoods/p/zz' . $link_parameter);
        $show = $page->show();
        $counting = $page->totalRows;
        $sql = "SELECT `b`.*, `g`.`goods_name`, `g`.`introduction`, `g`.`category_id` 
                FROM `{$dbPrefix}bidding` AS `b` 
                LEFT JOIN `{$dbPrefix}goods` AS `g` ON `b`.`goods_id` = `g`.`id` 
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `g`.`agent_id` = `a`.`id` 
                WHERE 1{$whereStr} 
                ORDER BY `b`.`id` DESC 
                LIMIT {$page->firstRow} , {$page->listRows}";
        $list = M()->query($sql);
        $time = time();
        foreach ($list as $key => &$value) {
            $value['category_name'] = getCategoryName($value['category_id']);
            $value['agent_name'] = getAgentName($value['agent_id']);
            $value['active_status'] = ( $value['end_time'] > $time ) ? '0' : '1';
        }
        $return = array(
            'agent_name' => $agent_name,
            'goods_name' => $goods_name,
            'category_id' => $category_id,
            'status' => $status,
            'startTime' => $startTime,
            'endTime' => $endTime
        );
        $categoryList = M('goods_category')->select();
        $this->assign('list', $list);
        $this->assign('show', $show);
        $this->assign('return', $return);
        $this->assign('counting', $counting);
        $this->assign('categoryList', $categoryList);
        $this->display();
    }

    /**
     * [biddingFavorableGoods 优惠商品竞价管理]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function biddingFavorableGoods() {
        $dbPrefix = C('DB_PREFIX');
        $whereStr = ' AND `b`.`is_pay` = \'1\' AND `b`.`bidding_type` = \'1\' AND `b`.`status` = \'0\'';
        $agent_name     = I('get.agent_name', '', 'urldecode');
        $goods_name     = I('get.goods_name', '', 'urldecode');
        $category_id    = I('get.category_id', '');
        $startTime      = I('get.start_time','');
        $endTime        = I('get.end_time','');
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

        if ( !empty($startTime) && !empty($endTime) ) {
            $whereStr .= " AND `b`.`start_time` BETWEEN {$startTime} AND {$endTime}";
            $link_parameter .= '/startTime/' . $startTime . '/endTime/' . $endTime;
        } else {
            $startTime = strtotime(date('Y', time()) . '-01-01');
            $endTime = strtotime('+1 days');
        }

        $sql = "SELECT COUNT(*) AS `count` 
                FROM `{$dbPrefix}bidding` AS `b` 
                LEFT JOIN `{$dbPrefix}goods` AS `g` ON `b`.`goods_id` = `g`.`id` 
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `g`.`agent_id` = `a`.`id` 
                WHERE 1{$whereStr}";
        $count = M()->query($sql);
        $count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
        $page = new \Think\Page($count, 25);
        $page->setConfig('link', '/Admin/Bidding/biddingFavorableGoods/p/zz' . $link_parameter);
        $show = $page->show();
        $counting = $page->totalRows;
        $sql = "SELECT `b`.*, `g`.`agent_id`, `g`.`goods_name`, `g`.`introduction`, `g`.`category_id`, `g`.`goods_price` 
                FROM `{$dbPrefix}bidding` AS `b` 
                LEFT JOIN `{$dbPrefix}goods` AS `g` ON `b`.`goods_id` = `g`.`id` 
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `g`.`agent_id` = `a`.`id` 
                WHERE 1{$whereStr} 
                ORDER BY `b`.`id` DESC 
                LIMIT {$page->firstRow} , {$page->listRows}";
        $list = M()->query($sql);
        foreach ($list as $key => &$value) {
            $value['category_name'] = getCategoryName($value['category_id']);
            $value['agent_name'] = getAgentName($value['agent_id']);
        }
        $return = array(
            'agent_name' => $agent_name,
            'goods_name' => $goods_name,
            'category_id' => $category_id,
            'startTime' => $startTime,
            'endTime' => $endTime
        );
        $categoryList = M('goods_category')->select();
        $this->assign('list', $list);
        $this->assign('show', $show);
        $this->assign('return', $return);
        $this->assign('counting', $counting);
        $this->assign('categoryList', $categoryList);
        $this->display();
    }

    /**
     * [auditBiddingFavorableGoods 已审核优惠商品竞价管理]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function auditBiddingFavorableGoods() {
        $dbPrefix = C('DB_PREFIX');
        $whereStr = ' AND `b`.`is_pay` = \'1\' AND `b`.`bidding_type` = \'1\' ';
        $agent_name     = I('get.agent_name', '', 'urldecode');
        $goods_name     = I('get.goods_name', '', 'urldecode');
        $category_id    = I('get.category_id', '');
        $status         = I('get.status', '-1', 'string');
        $startTime      = I('get.start_time','');
        $endTime        = I('get.end_time','');
        $link_parameter = '';

        if ( !empty($agent_name) ) {
            $whereStr .= " AND `a`.`agent_name` LIKE '%{$agent_name}%'";
            $link_parameter .= '/agent_name/' . $agent_name;
        }

        if ( !empty($goods_name) ) {
            $whereStr .= " AND `g`.`goods_name` LIKE '%{$goods_name}%'";
            $link_parameter .= '/goods_name/' . $goods_name;
        }

        if ( $status != '-1' ) {
            $whereStr .= " AND `b`.`status` = '{$status}'";
        } else {
            $whereStr .= " AND `b`.`status` != '0'";
        }

        if ( !empty($category_id) ) {
            $whereStr .= " AND `g`.`category_id` = '{$category_id}'";
            $link_parameter .= '/category_id/' . $category_id;
        }

        if ( !empty($startTime) && !empty($endTime) ) {
            $whereStr .= " AND `b`.`start_time` BETWEEN {$startTime} AND {$endTime}";
            $link_parameter .= '/startTime/' . $startTime . '/endTime/' . $endTime;
        } else {
            $startTime = strtotime(date('Y', time()) . '-01-01');
            $endTime = strtotime('+1 days');
        }

        $sql = "SELECT COUNT(*) AS `count` 
                FROM `{$dbPrefix}bidding` AS `b` 
                LEFT JOIN `{$dbPrefix}goods` AS `g` ON `b`.`goods_id` = `g`.`id` 
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `g`.`agent_id` = `a`.`id` 
                WHERE 1{$whereStr}";
        $count = M()->query($sql);
        $count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
        $page = new \Think\Page($count, 25);
        $page->setConfig('link', '/Admin/Bidding/auditBiddingFavorableGoods/p/zz' . $link_parameter);
        $show = $page->show();
        $counting = $page->totalRows;
        $sql = "SELECT `b`.*, `g`.`agent_id`, `g`.`goods_name`, `g`.`introduction`, `g`.`category_id`, `g`.`goods_price` 
                FROM `{$dbPrefix}bidding` AS `b` 
                LEFT JOIN `{$dbPrefix}goods` AS `g` ON `b`.`goods_id` = `g`.`id` 
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `g`.`agent_id` = `a`.`id` 
                WHERE 1{$whereStr} 
                ORDER BY `b`.`id` DESC 
                LIMIT {$page->firstRow} , {$page->listRows}";
        $list = M()->query($sql);
        foreach ($list as $key => &$value) {
            $value['category_name'] = getCategoryName($value['category_id']);
            $value['agent_name'] = getAgentName($value['agent_id']);
            $value['active_status'] = ( $value['end_time'] > $time ) ? '0' : '1';
        }
        $return = array(
            'agent_name' => $agent_name,
            'goods_name' => $goods_name,
            'category_id' => $category_id,
            'status' => $status,
            'startTime' => $startTime,
            'endTime' => $endTime
        );
        $categoryList = M('goods_category')->select();
        $this->assign('list', $list);
        $this->assign('show', $show);
        $this->assign('return', $return);
        $this->assign('counting', $counting);
        $this->assign('categoryList', $categoryList);
        $this->display();
    }

    /**
     * [biddingAgent 首页商家竞价]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function biddingAgent() {
        $dbPrefix = C('DB_PREFIX');
        $whereStr = ' AND `b`.`is_pay` = \'1\' AND `b`.`bidding_type` = \'2\' AND `b`.`status` = \'0\' ';
        $agent_name     = I('get.agent_name', '', 'urldecode');
        $category_id    = I('get.category_id', '');
        $startTime      = I('get.start_time','');
        $endTime        = I('get.end_time','');
        $link_parameter = '';
        $agentCategory = M('agent_category');
        $agentCategoryRelevance = M('agent_category_relevance');

        if ( !empty($agent_name) ) {
            $whereStr .= " AND `a`.`agent_name` LIKE '%{$agent_name}%'";
            $link_parameter .= '/agent_name/' . $agent_name;
        }

        if ( !empty($startTime) && !empty($endTime) ) {
            $whereStr .= " AND `b`.`start_time` BETWEEN {$startTime} AND {$endTime}";
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
                FROM `{$dbPrefix}bidding` AS `b` 
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `b`.`agent_id` = `a`.`id` 
                WHERE 1{$whereStr}";
        $count = M()->query($sql);
        $count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
        $page = new \Think\Page($count, 25);
        $page->setConfig('link', '/Admin/Bidding/biddingAgent/p/zz' . $link_parameter);
        $show = $page->show();
        $counting = $page->totalRows;
        $sql = "SELECT `b`.*, `a`.`agent_name` 
                FROM `{$dbPrefix}bidding` AS `b` 
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `b`.`agent_id` = `a`.`id` 
                WHERE 1{$whereStr} 
                ORDER BY `b`.`id` DESC 
                LIMIT {$page->firstRow} , {$page->listRows}";
        $list = M()->query($sql);
        foreach ($list as $key => &$value) {
            $ids = $agentCategoryRelevance->where(array('agent_id'=> $value['agent_id']))->select();
            $idsData = array_column($ids, 'category_id');
            $categoryData = $agentCategory->where(array('id'=> array('IN', $idsData)))->field('`category_name`')->select();
            $value['category'] = implode(',', array_column($categoryData, 'category_name'));
        }
        $return = array(
            'agent_name' => $agent_name,
            'goods_name' => $goods_name,
            'category_id' => $category_id,
            'startTime' => $startTime,
            'endTime' => $endTime
        );
        $categoryList = $agentCategory->select();
        $this->assign('list', $list);
        $this->assign('show', $show);
        $this->assign('return', $return);
        $this->assign('counting', $counting);
        $this->assign('categoryList', $categoryList);
        $this->display();
    }

    /**
     * [auditBiddingAgent 已审核首页商家竞价]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function auditBiddingAgent() {
        $dbPrefix = C('DB_PREFIX');
        $whereStr = ' AND `b`.`is_pay` = \'1\' AND `b`.`bidding_type` = \'2\' ';
        $agent_name     = I('get.agent_name', '', 'urldecode');
        $category_id    = I('get.category_id', '');
        $status         = I('get.status', '-1', 'string');
        $startTime      = I('get.start_time','');
        $endTime        = I('get.end_time','');
        $link_parameter = '';
        $agentCategory = M('agent_category');
        $agentCategoryRelevance = M('agent_category_relevance');

        if ( !empty($agent_name) ) {
            $whereStr .= " AND `a`.`agent_name` LIKE '%{$agent_name}%'";
            $link_parameter .= '/agent_name/' . $agent_name;
        }

        if ( !empty($startTime) && !empty($endTime) ) {
            $whereStr .= " AND `b`.`start_time` BETWEEN {$startTime} AND {$endTime}";
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

        if ( $status != '-1' ) {
            $whereStr .= " AND `b`.`status` = '{$status}'";
        } else {
            $whereStr .= " AND `b`.`status` != '0'";
        }

        $sql = "SELECT COUNT(*) AS `count` 
                FROM `{$dbPrefix}bidding` AS `b` 
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `b`.`agent_id` = `a`.`id` 
                WHERE 1{$whereStr}";
        $count = M()->query($sql);
        $count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
        $page = new \Think\Page($count, 25);
        $page->setConfig('link', '/Admin/Bidding/auditBiddingAgent/p/zz' . $link_parameter);
        $show = $page->show();
        $counting = $page->totalRows;
        $sql = "SELECT `b`.*, `a`.`agent_name` 
                FROM `{$dbPrefix}bidding` AS `b` 
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `b`.`agent_id` = `a`.`id` 
                WHERE 1{$whereStr} 
                ORDER BY `b`.`id` DESC 
                LIMIT {$page->firstRow} , {$page->listRows}";
        $list = M()->query($sql);
        foreach ($list as $key => &$value) {
            $ids = $agentCategoryRelevance->where(array('agent_id'=> $value['agent_id']))->select();
            $idsData = array_column($ids, 'category_id');
            $categoryData = $agentCategory->where(array('id'=> array('IN', $idsData)))->field('`category_name`')->select();
            $value['category'] = implode(',', array_column($categoryData, 'category_name'));
        }
        $return = array(
            'agent_name' => $agent_name,
            'goods_name' => $goods_name,
            'category_id' => $category_id,
            'status' => $status,
            'startTime' => $startTime,
            'endTime' => $endTime
        );
        $categoryList = $agentCategory->select();
        $this->assign('list', $list);
        $this->assign('show', $show);
        $this->assign('return', $return);
        $this->assign('counting', $counting);
        $this->assign('categoryList', $categoryList);
        $this->display();
    }

    /**
     * [biddingBanner 广告位竞价管理]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function biddingBanner() {
        $dbPrefix = C('DB_PREFIX');
        $whereStr = ' AND `b`.`is_pay` = \'1\' AND `b`.`bidding_type` = \'3\' AND `b`.`status` = \'0\' ';
        $agent_name     = I('get.agent_name', '', 'urldecode');
        $banner_type    = I('get.banner_type', '-1');
        $link_parameter = '';

        if ( !empty($agent_name) ) {
            $whereStr .= " AND `a`.`agent_name` LIKE '%{$agent_name}%'";
            $link_parameter .= '/agent_name/' . $agent_name;
        }

        if ( $banner_type != '-1' ) {
            $whereStr .= " AND `b`.`banner_type` = '{$banner_type}'";
            $link_parameter .= '/banner_type/' . $banner_type;
        }

        $sql = "SELECT COUNT(*) AS `count` 
                FROM `{$dbPrefix}bidding` AS `b` 
                LEFT JOIN `{$dbPrefix}goods` AS `g` ON `b`.`goods_id` = `g`.`id` 
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `g`.`agent_id` = `a`.`id` 
                WHERE 1{$whereStr}";
        $count = M()->query($sql);
        $count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
        $page = new \Think\Page($count, 25);
        $page->setConfig('link', '/Admin/Bidding/biddingBanner/p/zz' . $link_parameter);
        $show = $page->show();
        $counting = $page->totalRows;
        $sql = "SELECT `b`.*, `a`.`agent_name`, `g`.`goods_name`  
                FROM `{$dbPrefix}bidding` AS `b` 
                LEFT JOIN `{$dbPrefix}goods` AS `g` ON `b`.`goods_id` = `g`.`id` 
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `b`.`agent_id` = `a`.`id` 
                WHERE 1{$whereStr} 
                ORDER BY `b`.`id` DESC 
                LIMIT {$page->firstRow} , {$page->listRows}";
        $list = M()->query($sql);
        $return = array(
            'agent_name' => $agent_name,
            'banner_type' => $banner_type,
        );
        $categoryList = M('goods_category')->select();
        $this->assign('list', $list);
        $this->assign('show', $show);
        $this->assign('return', $return);
        $this->assign('counting', $counting);
        $this->assign('categoryList', $categoryList);
        $this->display();
    }

    /**
     * [auditBiddingBanner 已审核广告位竞价管理]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function auditBiddingBanner() {
        $dbPrefix = C('DB_PREFIX');
        $whereStr = ' AND `b`.`is_pay` = \'1\' AND `b`.`bidding_type` = \'3\' ';
        $agent_name     = I('get.agent_name', '', 'urldecode');
        $banner_type    = I('get.banner_type', '-1');
        $status         = I('get.status', '-1', 'string');
        $link_parameter = '';

        if ( !empty($agent_name) ) {
            $whereStr .= " AND `a`.`agent_name` LIKE '%{$agent_name}%'";
            $link_parameter .= '/agent_name/' . $agent_name;
        }

        if ( $banner_type != '-1' ) {
            $whereStr .= " AND `b`.`banner_type` = '{$banner_type}'";
            $link_parameter .= '/banner_type/' . $banner_type;
        }

        if ( $status != '-1' ) {
            $whereStr .= " AND `b`.`status` = '{$status}'";
        } else {
            $whereStr .= " AND `b`.`status` != '0'";
        }

        $sql = "SELECT COUNT(*) AS `count` 
                FROM `{$dbPrefix}bidding` AS `b` 
                LEFT JOIN `{$dbPrefix}goods` AS `g` ON `b`.`goods_id` = `g`.`id` 
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `g`.`agent_id` = `a`.`id` 
                WHERE 1{$whereStr}";
        $count = M()->query($sql);
        $count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
        $page = new \Think\Page($count, 25);
        $page->setConfig('link', '/Admin/Bidding/auditBiddingBanner/p/zz' . $link_parameter);
        $show = $page->show();
        $counting = $page->totalRows;
        $sql = "SELECT `b`.*, `a`.`agent_name`, `g`.`goods_name`  
                FROM `{$dbPrefix}bidding` AS `b` 
                LEFT JOIN `{$dbPrefix}goods` AS `g` ON `b`.`goods_id` = `g`.`id` 
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `b`.`agent_id` = `a`.`id` 
                WHERE 1{$whereStr} 
                ORDER BY `b`.`id` DESC 
                LIMIT {$page->firstRow} , {$page->listRows}";
        $list = M()->query($sql);
        $return = array(
            'agent_name' => $agent_name,
            'banner_type' => $banner_type,
            'status' => $status,
        );
        $categoryList = M('goods_category')->select();
        $this->assign('list', $list);
        $this->assign('show', $show);
        $this->assign('return', $return);
        $this->assign('counting', $counting);
        $this->assign('categoryList', $categoryList);
        $this->display();
    }

    /**
     * [checkBidding 审核竞价]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function checkBidding() {
        $bidding = M('bidding');
        $adModel = M('ad');
        $goodsModel = M('goods');
        $agentModel = M('agent');
        if ( IS_POST ) {
            $id = I('id', '', 'string');
            $sort = I('sort', '0', 'string');
            if ( $bidding->save(array('id'=> $id, 'sort'=> $sort)) !== false ) {
                $biddingData = $bidding->find($id);
                switch ( $biddingData['bidding_type'] ) {
                    case '0':
                    case '1':
                        $goodsModel->where(array('id'=> $biddingData['goods_id']))->save(array('sort'=> $sort));
                        break;
                    case '2':
                        $agentModel->where(array('id'=> $biddingData['agent_id']))->save(array('sort'=> $sort));
                        break;
                    case '3':
                        $where = array(
                            'agent_id' => $biddingData['agent_id'],
                            'type' => ( empty($biddingData['banner_type']) ) ? '1' : '2',
                        );
                        $adModel->where($where)->save(array('sort'=> $sort));
                        break;
                }
                exit(statusCode());
            } else {
                exit(statusCode(array(), 400000, '更新失败'));
            }
        } else {
            $ids = I('get.ids', '');
            $status = I('status', '1');
            $bidding_type = I('get.bidding_type', '0');
            if ( empty($ids) || empty($status) ) {
                $this->error('请选择要处理的数据');
            }
            $where = array(
                'id' => array('IN', $ids),
                'bidding_type' => $bidding_type
            );
            if ( $bidding->where($where)->save(array('status'=> $status)) !== false ) {
                $list = $bidding->where($where)->select();
                switch ( $bidding_type ) {
                    case '0':
                        foreach ($list as $key => $value) {
                            $saveData = array(
                                'bidding_money' => $value['total'],
                                'is_recommend' => '1',
                                'recommend_finish_time' => $value['end_time'],
                            );
                            $goodsModel->where(array('id'=> $value['goods_id']))->save($saveData);
                        }
                        break;
                    case '1':
                        foreach ($list as $key => $value) {
                            $saveData = array(
                                // 'is_favorable' => '0',
                                'bidding_money' => $value['total'],
                                'favorable_price' => $value['favorable_price'],
                                'favorable_start_time' => $value['start_time'],
                                'favorable_end_time' => $value['end_time'],
                            );
                            $where = array(
                                'id' => $value['goods_id'],
                                'goods_main_id' => $value['goods_id'],
                                '_logic' => 'OR',
                            );
                            $goodsModel->where($where)->save($saveData);
                        }
                        break;
                    case '2':
                        foreach ($list as $key => $value) {
                            $saveData = array(
                                'bidding_money' => $value['total'],
                                'start_show_time' => $value['start_time'],
                                'end_show_time' => $value['end_time'],
                            );
                            $agentModel->where(array('id'=> $value['agent_id']))->save($saveData);
                        }
                        break;
                    case '3':
                        foreach ($list as $key => $value) {
                            $addData = array(
                                'agent_id' => $value['agent_id'],
                                'image' => $value['image'],
                                'type' => ( empty($value['banner_type']) ) ? '1' : '2',
                                'bidding_money' => $value['total'],
                                'content' => ( empty($value['banner_type']) ) ? $value['agent_id'] : $value['goods_id'],
                                'start_time' => $value['start_time'],
                                'end_time' => $value['end_time'],
                            );
                            $adModel->add($addData);
                        }
                        break;
                }
                if ( $status == '2' ) {
                    $paypal = new \Shop\Controller\PaypalController;
                    foreach ($list as $v) {
                        if ( !empty($v['txn_id']) ) {
                            $paypal->paypal_refund($v['txn_id']);
                        }
                    }
                }
                $this->success('更新成功');
            } else {
                $this->error('更新失败');
            }
        }
    }
}