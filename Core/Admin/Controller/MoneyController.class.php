<?php
namespace Admin\Controller;
class MoneyController extends BaseController {
    /**
     * [moneyList 提现管理]
     * @author shichun <672517056@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function moneyList() {
        header("Content-type:text/html;charset=utf-8");
        $dbPrefix = C('DB_PREFIX');
        $user_withdraw = M('user_withdraw');
        $username    = I('get.username', '', 'urldecode');
        $nickname    = I('get.nickname', '', 'urldecode');
        $truename = I('get.truename','', 'urldecode');
        $status = I('get.status', '');
        $Jmoney = I('get.Jmoney', '');
        
        $whereStr = '';
        $link_parameter = '';
        $leftJoin = '';
        //账号
        if ( !empty($username) ) {
            $whereStr .= " AND `u`.`username` LIKE '%{$username}%'";
            $link_parameter .= '/username/' . $username;
        }   
        
        //昵称
        if ( !empty($nickname) ) {
            $whereStr .= " AND `u`.`nickname` LIKE '%{$nickname}%'";
            $link_parameter .= '/nickname/' . $nickname;
        }

        //真实姓名
        if ( !empty($truename) ) {
            $whereStr .= " AND `w`.`truename` LIKE '%{$truename}%'";
            $link_parameter .= '/truename/' . $truename;
        }
           
        //审核状态   
        if ($status != '') {
            $whereStr .= " AND `w`.`status` = '{$status}'";
            $link_parameter .= '/status/' . $status;
        }
       
        $return['username']=$username;
        $return['nickname']=$nickname;
        $return['truename']=$truename;
        $return['status']=$status;
        
        $sql = "SELECT COUNT(*) AS `count` 
                FROM `{$dbPrefix}user_withdraw` AS `w`
                LEFT JOIN `{$dbPrefix}user` AS `u` ON `w`.`user_id` = `u`.`id`
                {$leftJoin}
                WHERE 1{$whereStr}";
        $count = M()->query($sql);
        $count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
      
        $page = new \Think\Page($count, 25);
        $page->setConfig('link', '/Admin/Money/moneyList/p/zz' . $link_parameter);
        $show = $page->show();
       
        $sql = "SELECT `w`.`id`,`w`.`money`,`w`.`withdraw_type`,`w`.`account`,`w`.`truename`,`w`.`status` AS `audit`,`w`.`add_time`,`u`.`username`,`u`.`nickname`,`u`.`status`
                FROM `{$dbPrefix}user_withdraw` AS `w`
                LEFT JOIN `{$dbPrefix}user` AS `u` ON `w`.`user_id` = `u`.`id`   
                {$leftJoin}
                WHERE 1{$whereStr} 
                ORDER BY `w`.`id` DESC 
                LIMIT {$page->firstRow}, {$page->listRows}";
        $moneyList = M()->query($sql);
      
        $this->assign('moneyList', $moneyList);
        $this->assign('return', $return);
        $this->assign("show", $show);
        $this->display();
    }

    /**
     * [verifyMoney 提现验证]
     * @author shichun <[672517056@qq.com]>
     */
    public function verifyMoney() {
        $user_withdraw = M('user_withdraw');
        $moneyId = I('get.id');
        $audit = I('get.audit');
        if ($user_withdraw->where(array('id'=>$moneyId))->save(array('status'=>$audit)) !== false) {
            $this->success('完成审核！');
        } else {
            $this->error('审核失败！');
        }
    }

    /**
     * [agentBalanceList 商家余额管理]
     * @author shichun <[672517056@qq.com]>
     *
     */
    public function agentBalanceList() {
        $dbPrefix = C('DB_PREFIX');
        $agent  = M('agent');
        $user = M('user');
        $agent_name  = I('get.agent_name', '','urldecode');
        $username  = I('get.username', '','urldecode');
        $manager  = I('get.manager', '','urldecode');
        $is_on_sale   = I('get.is_on_sale', '-1');
        $startTime    = I('get.start_time','');
        $endTime    = I('get.end_time','');
        $category_id  = I('get.category_id', '','urldecode');
        $categoryMode = M('agent_category');
        $agentCategoryRelevance = M('agent_category_relevance');
        $link_parameter = '';
        $leftJoin = '';
        $whereStr = ' AND `u`.`status` = \'1\'';
        //店铺名称
        if (!empty($agent_name)) {
            $whereStr .= " AND `a`.`agent_name` LIKE '%{$agent_name}%'";
            $link_parameter .= '/agent_name/' . $agent_name;
        }
        //账号
        if (!empty($username)) {
            $whereStr .= " AND `u`.`username` LIKE '%{$username}%'";

            $link_parameter .= '/username/' . $username;
        }
        //真实姓名
        if (!empty($manager)) {
            $whereStr .= " AND `a`.`manager` LIKE '%{$manager}%'";
            $link_parameter .= '/manager/' . $manager;
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
        
        $return['category_name'] = $category_name;
        $return['endTime'] = $endTime;
        $return['startTime'] = $startTime;             
        $return['agent_name'] = $agent_name;
        $return['username'] = $username;
        $return['manager'] = $manager;
        $return['is_on_sale'] = $is_on_sale;
        
        $sql = "SELECT count(*) AS `count`
                FROM `{$dbPrefix}agent` AS `a`  
                LEFT JOIN `{$dbPrefix}user` AS `u` ON `u`.`id` = `a`.`user_id` 
                {$leftJoin} 
                WHERE 1{$whereStr}";
        $count = M()->query($sql);
        $count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
        $page = new \Think\Page($count, 25);
        $show = $page->show();
        $counting=$page->totalRows; 
        
        $sql = "SELECT `a`.`id`,`a`.`is_on_sale`,`a`.`agent_name`,`a`.`address`,`a`.`manager`,`u`.`id` AS `ids`,`u`.`money`,`u`.`username`
                FROM  `{$dbPrefix}agent` AS `a`   
                LEFT JOIN `{$dbPrefix}user` AS `u` ON `u`.`id` = `a`.`user_id`
                {$leftJoin} 
                WHERE 1{$whereStr} 
                LIMIT {$page->firstRow}, {$page->listRows}";
        $agentList = M()->query($sql); 
        foreach($agentList as $key => &$value){
            //商家分类
            $ids = $agentCategoryRelevance->where(array('agent_id'=>$value['id']))->select();
            if ( !empty($ids) ) {
                $idsData = array_column($ids,'category_id');
                $categoryData = $categoryMode->where(array('id'=>array('IN', $idsData)))->field('`category_name`')->select();
                $value['category_name'] = implode(',',array_column($categoryData,'category_name'));
            } else {
                $value['category_name'] = '-';
            }
        }
        $categoryList = $categoryMode->select();
        $this->assign('agentList', $agentList);
        $this->assign('categoryList', $categoryList);
        $this->assign('show', $show);
        $this->assign('counting',$counting);
        $this->assign('return', $return);
        $this->display();
    }
    
    /**
     * [editagentBalanceList 修改余额]
     * @author shichun <672517056@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function editagentBalanceList() {
        if (IS_POST) {
            $ids   = I('post.ids', '', 'int');
            $money = I('post.Jmoney', '');
            $user = M('user');
            $where = array(
                'id' => $ids,
                'status' => '1',
            );
            $userInfo = $user->where($where)->count();
            if ($userInfo > 0) {
                if ($user->where(array('id'=> $ids))->save(array('money'=> $money)) !== false ) {
                    exit(statusCode());
                } else {
                    exit(statusCode('', 400000, '修改余额失败'));
                }
            } else {
                exit(statusCode('', 400000, '没有该商家'));
            }
        } else {
            $this->assign('agent', $agent);
            $this->display();
        }
    }

    /**
     * [moneySetting 金额设置]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function moneySetting() {
        if ( IS_POST ) {
            $this->updateConfig();
        } else {
            $configList = M('config')->select();
            $this->assign('configList', $configList);
            $this->display();
        }
    }

    /**
     * [moneyLog 系统收支流水]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function moneyLog() {
        $dbPrefix = C('DB_PREFIX');
        $keyword = I('get.keyword', '', 'urldecode');
        $startTime = I('get.startTime', '');
        $endTime = I('get.endTime', '');
        $startMoney = I('get.startMoney', '');
        $endMoney = I('get.endMoney', '');
        $type = I('get.type', '-1');
        $from = I('get.from', '-1');
        $to = I('get.to', '-1');
        $link_parameter = '';
        if ( !empty($keyword) ) {
            $whereStr .= " AND `ml`.`description` LIKE '%{$keyword}%'";
            $link_parameter .= '/keyword/' . $keyword;
        }
        if ( !empty($startTime) && !empty($endTime) ) {
            $whereStr .= " AND `ml`.`add_time` BETWEEN '{$startTime}' AND '{$endTime}'";
            $link_parameter .= '/startTime/' . $startTime . '/endTime/' . $endTime;
        } else {
            $startTime = strtotime(date('Y', time()) . '-01-01');
            $endTime = strtotime('+1 days');
        }

        if ( !empty($startMoney) && !empty($endMoney) ) {
            $whereStr .= " AND `ml`.`money` BETWEEN '{$startMoney}' AND '{$endMoney}'";
            $link_parameter .= '/startMoney/' . $startMoney . '/endMoney/' . $endMoney;
        }

        if ( $type != '-1' ) {
            $whereStr .= " AND `ml`.`type` = '{$type}'";
            $link_parameter .= '/type/' . $type;
        }

        if ( $from != '-1' ) {
            $whereStr .= " AND `ml`.`from` = '{$from}'";
            $link_parameter .= '/from/' . $from;
        }

        if ( $to != '-1' ) {
            $whereStr .= " AND `ml`.`to` = '{$to}'";
            $link_parameter .= '/to/' . $to;
        }

        $sql = "SELECT COUNT(*) AS `count` 
                FROM `{$dbPrefix}money_log` AS `ml` 
                LEFT JOIN `{$dbPrefix}user` AS `u` ON `ml`.`to_id` = `u`.`id` 
                WHERE 1{$whereStr}";
        $count = M()->query($sql);
        $count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
        $page = new \Think\Page($count, 25);
        $page->setConfig('link', '/Admin/Money/moneyLog/p/zz' . $link_parameter);
        $show = $page->show();
        $counting = $page->totalRows;
        $sql = "SELECT `ml`.*, `u`.`username` 
                FROM `{$dbPrefix}money_log` AS `ml` 
                LEFT JOIN `{$dbPrefix}user` AS `u` ON `ml`.`to_id` = `u`.`id` 
                WHERE 1{$whereStr} 
                ORDER BY `ml`.`id` DESC 
                LIMIT {$page->firstRow} , {$page->listRows}";
        $list = M()->query($sql);
        foreach ($list as $key => &$value) {
            $value['event'] = $this->eventType($value['event_type'], $value['money']);
        }
        $return = array(
            'keyword' => $keyword,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'startMoney' => $startMoney,
            'endMoney' => $endMoney,
            'from' => $from,
            'to' => $to,
        );
        $this->assign('list', $list);
        $this->assign('show', $show);
        $this->assign('return', $return);
        $this->assign('counting', $counting);
        $this->display();
    }

    private function eventType($type, $money) {
        $event = array(
            '0'     => '购物',
            '1'     => '扫码支付+' . $money . '元',
            '2'     => '增益+' . $money . '元',
            '3'     => '充值+' . $money . '元',
            '4'     => '提现-' . $money . '元',
            '5'     => '商品认证-' . $money . '元',
            '6'     => '商品置顶-' . $money . '元',
            '7'     => '发布消息-' . $money . '元',
        );
        return $event[$type];
    }

    /**
     * [customerRechargeLog 顾客财务充值情况]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function customerRechargeLog() {
        $userModel = M('user');
        $username = I('get.username', '', 'urldecode');
        $nickname = I('get.nickname', '', 'urldecode');
        $link_parameter = '';
        if ( !empty($username) ) {
            $where['username'] = array('LIKE', "%{$username}%");
            $link_parameter .= '/username/' . $username;
        }
        if ( !empty($nickname) ) {
            $where['nickname'] = array('LIKE', "%{$nickname}%");
            $link_parameter .= '/nickname/' . $nickname;
        }
        $count = $userModel->where($where)->count();
        $page = new \Think\Page($count, 25);
        $page->setConfig('link', '/Admin/Money/customerRechargeLog/p/zz' . $link_parameter);
        $show = $page->show();
        $counting = $page->totalRows;
        $list = $userModel->where($where)->field('`id`, `headimgurl`, `username`, `nickname`, `sex`, `money`')->order('`id` DESC')->limit($page->firstRow.','.$page->listRows)->select();
        $return = array(
            'username' => $username,
            'nickname' => $nickname,
        );
        $this->assign('list', $list);
        $this->assign('show', $show);
        $this->assign('return', $return);
        $this->assign('counting', $counting);
        $this->display();
    }

    /**
     * [rechargeSetting 最低充值金额设置]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function rechargeSetting() {
        if ( IS_POST ) {
            $this->updateConfig();
        } else {
            $configList = M('config')->select();
            $this->assign('configList', $configList);
            $this->display();
        }
    }

    /**
     * [increaseRateSetting 钱包利率管理]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function increaseRateSetting() {
        if ( IS_POST ) {
            $this->updateConfig();
        } else {
            $configList = M('config')->select();
            $this->assign('configList', $configList);
            $this->display();
        }
    }

    /**
     * [agentRegisterSetting 新注册商家赠送金额管理]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function agentRegisterSetting() {
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