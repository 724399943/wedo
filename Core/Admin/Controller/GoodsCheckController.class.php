<?php
namespace Admin\Controller;
class GoodsCheckController extends BaseController {   
    /**
     * [goodsAuth 商品认证管理]
     * @author shichun <672517056@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function goodsAuth() {
        $dbPrefix = C('DB_PREFIX');
        $goods_check = M('goods_check');
        $goods_name    = I('get.goods_name', '', 'urldecode');
        $startTime      = I('get.start_time','');
        $endTime        = I('get.end_time','');
        $agent_name    = I('get.agent_name', '', 'urldecode');
        $status = I('get.status', '');
        $category_name = I('get.category_name','');
        
        $category = M('goods_category')->field('id,category_name')->select();
        $whereStr = ' AND `gc`.`check_type` = \'0\' AND `gc`.`is_pay` = \'1\'';
        $link_parameter = '';
        $leftJoin = '';
        //商品名称
        if ( !empty($goods_name) ) {
            $whereStr .= " AND `g`.`goods_name` LIKE '%{$goods_name}%'";
            $link_parameter .= '/goods_name/' . $goods_name;
        }   
        
        //店铺名称
        if ( !empty($agent_name) ) {
            $whereStr .= " AND `a`.`agent_name` LIKE '%{$agent_name}%'";
            $link_parameter .= '/agent_name/' . $agent_name;
        }
           
        //验证状态   
        if ($status != '') {
            $whereStr .= " AND `gc`.`status` = '{$status}'";
            $link_parameter .= '/status/' . $status;
        }
        
        //注册时间
        if ( !empty($startTime) && !empty($endTime) ) {
            $whereStr .= " AND `gc`.`start_time` BETWEEN {$startTime} AND {$endTime}";
            $link_parameter .= '/startTime/' . $startTime . '/endTime/' . $endTime;
        } else {
            $startTime = strtotime(date('Y', time()) . '-01-01');
            $endTime = strtotime('+1 days');
        }
       
        //平台分类
        if ( !empty($category_name) ){
            
            $whereStr .= " AND `g`.`category_id` = '{$category_name}'";
            $link_parameter .= '/category_name/' . $category_name;
        }
        
        $return['agent_name']=$agent_name;
        $return['goods_name']=$goods_name;
        $return['endTime'] = $endTime;
        $return['startTime'] = $startTime;
        $return['status']=$status;
        
        $sql = "SELECT COUNT(*) AS `count` 
                FROM `{$dbPrefix}goods_check` AS `gc`
                LEFT JOIN `{$dbPrefix}goods` AS `g` ON `gc`.`goods_id` = `g`.`id`  
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `g`.`agent_id` = `a`.`id` 
                {$leftJoin}
                WHERE 1{$whereStr}";
        $count = M()->query($sql);
        $count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
        $page = new \Think\Page($count, 25);
        $page->setConfig('link', '/Admin/GoodsCheck/goodsAuth/p/zz' . $link_parameter);
        $show = $page->show();
       
        $sql = "SELECT `gc`.`id`, `gc`.`end_time`, `gc`.`add_time`,`gc`.`start_time`, `gc`.`status`, `gc`.`goods_id`, `g`.`goods_name`, `g`.`introduction`, `g`.`category_id`, `g`.`agent_id`, `a`.`agent_name` 
                FROM `{$dbPrefix}goods_check` AS `gc`
                LEFT JOIN `{$dbPrefix}goods` AS `g` ON `gc`.`goods_id` = `g`.`id`  
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `g`.`agent_id` = `a`.`id` 
                {$leftJoin}
                WHERE 1{$whereStr} 
                ORDER BY `gc`.`id` DESC
                LIMIT {$page->firstRow}, {$page->listRows}";
        $goodsAuth = M()->query($sql);
        foreach($goodsAuth as $key => &$value) {
            $value['category_name'] = getCategoryName($value['category_id']);
        }
        $this->assign('goodsAuth', $goodsAuth);
        $this->assign('category', $category);
        $this->assign('status', $status);
        $this->assign('return', $return);
        $this->assign('agentList', $agentList);
        $this->assign("show", $show);
        $this->display();
    }

    /**
     * [verifyAuth 商品认证验证]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function verifyAuth() {
        $goodsModel = M('goods');
        $goodsCheckModel = M('goods_check');
        $id = I('get.id');
        $status = I('get.status');
        if ( $goodsCheckModel->where(array('id'=>$id))->save(array('status'=>$status)) !== false ) {
            $checkData = $goodsCheckModel->find($id);
            $saveData = array(
                'id' => $checkData['goods_id'],
                'is_auth' => '1',
                'auth_finish_time' => $checkData['end_time']
            );
            $goodsModel->save($saveData);
            $this->success('完成审核！');
        } else {
            $this->error('审核失败！');
        }
    }

    /**
     * [verifyAuthAll 全选商品认证验证]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function verifyAuthAll() {
        $goodsModel = M('goods');
        $goodsCheckModel = M('goods_check');
        $ids = I('get.ids');
        $status = I('get.status');
        if (empty($ids)) {
            $this->error('请选择要审核的商品！');
        } 
        $where['id'] = array('IN', $ids);
        if ( $goodsCheckModel->where($where)->save(array('status'=>$status)) !== false ) {
            $list = $goodsCheckModel->where($where)->select();
            foreach ($list as $key => $value) {
                if ( empty($value['check_type']) ) {
                    $saveData = array(
                        'id' => $value['goods_id'],
                        'is_auth' => '1',
                        'auth_finish_time' => $value['end_time']
                    );
                } else {
                    $saveData = array(
                        'id' => $value['goods_id'],
                        'is_recommend' => '1',
                        'recommend_finish_time' => $value['end_time']
                    );
                }
                $goodsModel->save($saveData);
            }
            $this->success('完成审核！');
        } else {
            $this->error('审核失败！');
        }
    }

    /**
     * [goodsTop 商品置顶管理]
     * @author shichun <672517056@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function goodsTop() {
        $dbPrefix = C('DB_PREFIX');
        $goods = M('goods_check');
        $goods_name    = I('get.goods_name', '', 'urldecode');
        $agent_name    = I('get.agent_name', '', 'urldecode');
        $status = I('get.status', '');
        $startTime      = I('get.start_time','');
        $endTime        = I('get.end_time','');
        $category_name = I('get.category_name','');
        
        $category = M('goods_category')->field('id,category_name')->select();
        $whereStr = ' AND `gc`.`check_type` = \'1\' AND `gc`.`is_pay` = \'1\' ';
        $link_parameter = '';
        $leftJoin = '';
          
        //商品名称
        if ( !empty($goods_name) ) {
            $whereStr .= " AND `g`.`goods_name` LIKE '%{$goods_name}%'";
            $link_parameter .= '/goods_name/' . $goods_name;
        }   
        
        //店铺名称
        if ( !empty($agent_name) ) {
            $whereStr .= " AND `a`.`agent_name` LIKE '%{$agent_name}%'";
            $link_parameter .= '/agent_name/' . $agent_name;
        }
           
        //验证状态   
        if ($status != '') {
            $whereStr .= " AND `gc`.`status` = '{$status}'";
            $link_parameter .= '/status/' . $status;
        }
        
        //注册时间
        if ( !empty($startTime) && !empty($endTime) ) {
            $whereStr .= " AND `gc`.`start_time` BETWEEN {$startTime} AND {$endTime}";
            $link_parameter .= '/startTime/' . $startTime . '/endTime/' . $endTime;
        } else {
            $startTime = strtotime(date('Y', time()) . '-01-01');
            $endTime = strtotime('+1 days');
        }
        
        //平台分类
        if ( !empty($category_name) ){
            $whereStr .= " AND `g`.`category_id` = '{$category_name}'";
            $link_parameter .= '/category_name/' . $category_name;
        }
        
        $return['agent_name']=$agent_name;
        $return['goods_name']=$goods_name;
        $return['endTime'] = $endTime;
        $return['startTime'] = $startTime;
        $return['status']=$status;
        
        $where['check_type'] = 0;
        $sql = "SELECT COUNT(*) AS `count` 
                FROM `{$dbPrefix}goods_check` AS `gc`
                LEFT JOIN `{$dbPrefix}goods` AS `g` ON `gc`.`goods_id` = `g`.`id`  
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `g`.`agent_id` = `a`.`id` 
                {$leftJoin}
                WHERE 1{$whereStr}";
        $count = M()->query($sql);
        $count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
      
        $page = new \Think\Page($count, 25);
        $page->setConfig('link', '/Admin/GoodsCheck/goodsAuth/p/zz' . $link_parameter);
        $show = $page->show();
       
        $sql = "SELECT `gc`.`id`, `gc`.`end_time`, `gc`.`add_time`,`gc`.`start_time`, `gc`.`status`, `g`.`goods_name`,
         `g`.`introduction`, `g`.`category_id`, `g`.`agent_id`,`g`.`sale_number`, `a`.`agent_name` 
                FROM `{$dbPrefix}goods_check` AS `gc`
                LEFT JOIN `{$dbPrefix}goods` AS `g` ON `gc`.`goods_id` = `g`.`id`  
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `g`.`agent_id` = `a`.`id` 
                {$leftJoin}
                WHERE 1{$whereStr} 
                ORDER BY `gc`.`id` DESC
                LIMIT {$page->firstRow}, {$page->listRows}";
        $goodsAuth = M()->query($sql);
        foreach($goodsAuth as $key => &$value) {
            $value['category_name'] = getCategoryName($value['category_id']);
        }
        $this->assign('goodsAuth', $goodsAuth);
        $this->assign('category', $category);
        $this->assign('status', $status);
        $this->assign('return', $return);
        $this->assign('agentList', $agreturnentList);
        $this->assign("show", $show);
        $this->display();
	}
  
    /**
     * [verifyTop 商品置顶验证]
     * @author shichun <[672517056@qq.com]>
     */
    public function verifyTop() {
        $goodsModel = M('goods');
        $goodsCheckModel = M('goods_check');
        $id = I('get.id');
        $status = I('get.status');
        if ($goodsCheckModel->where(array('id'=>$id))->save(array('status'=>$status)) !== false) {
            $checkData = $goodsCheckModel->find($id);
            $saveData = array(
                'id' => $checkData['goods_id'],
                'is_recommend' => '1',
                'recommend_finish_time' => $checkData['end_time']
            );
            $goodsModel->save($saveData);
            $this->success('完成审核！');
        } else {
            $this->error('审核失败！');
        }
    }

    /**
     * [authSetting 商品认证设置]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function authSetting() {
        if ( IS_POST ) {
            $this->updateConfig();
        } else {
            $configList = M('config')->select();
            $this->assign('configList', $configList);
            $this->display();
        }
    }

    /**
     * [topSetting 商品置顶设置]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function topSetting() {
        if ( IS_POST ) {
            $this->updateConfig();
        } else {
            $configList = M('config')->select();
            $this->assign('configList', $configList);
            $this->display();
        }
    }

    private function updateConfig() {
        $config = M('config');
        $data   = I('post.config');
        foreach ($data as $key => $value) {
            $saveData = array();
            $saveData['config_value'] = $value['config_value'];
            $config->where(array('config_sign'=>$value['config_sign']))->save($saveData);
        }
        $this->success('更新成功！');
    }
}