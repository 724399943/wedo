<?php
namespace Admin\Controller;
use Plugins\Point\Point;
class UserController extends BaseController {
    /**
     * [userList 用户列表]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function userList() {
        $orderModel = M('order');
        $userModel = M('user');
        $username           = I('get.username', '', 'urldecode');
        $nickname           = I('get.nickname', '', 'urldecode');
        $is_lock            = I('get.is_lock', '-1');
        $from               = I('get.from', '-1');
        $startTime          = I('get.start_time', '');
        $endTime            = I('get.end_time', '');
        $order_number       = I('get.order_number', '-1', 'string');
        $add_time           = I('get.add_time', '-1', 'string');
        $last_login_time    = I('get.last_login_time', '-1', 'string');
        $order = '`id` DESC';
        // 账号
        if ( !empty($username) ) {
            $where['username'] = array('LIKE', "%{$username}%");
        }
        // 昵称
        if ( !empty($nickname) ) {
            $where['nickname'] = array('LIKE', "%{$nickname}%");
        }
        // 用户状态
        if ($is_lock != '-1') {
            $where['is_lock'] = $is_lock;
        }
        // 用户来源
        if ($from != '-1') {
            if ( $from == '0' ) {
                $where['phone'] = array('NEQ', ''); 
            } else {
                $where['email'] = array('NEQ', ''); 
            }
        }
        // 注册时间
        if ( !empty($startTime) && !empty($endTime) ) {
            $where['add_time'] = array('between',array($startTime,$endTime));
        } else {
            $startTime = strtotime('2016-1-1');
            $endTime = strtotime('+1 days');
        }

        // 订单量排序
        if ( $order_number != '-1' ) {
            // 0：升序，1：降序
            $order = ( empty($order_number) ) ? '`order_number` ASC' : '`order_number` DESC';
            $order .= ',`id` DESC';
        }

        // 注册时间排序
        if ( $add_time != '-1' ) {
            // 0：升序，1：降序
            $order = ( empty($add_time) ) ? '`add_time` ASC' : '`add_time` DESC';
            $order .= ',`id` DESC';
        }

        // 最近访问排序
        if ( $last_login_time != '-1' ) {
            // 0：升序，1：降序
            $order = ( empty($last_login_time) ) ? '`last_login_time` ASC' : '`last_login_time` DESC';
            $order .= ',`id` DESC';
        }

        $count = $userModel->where($where)->count();
        $page = new \Think\Page($count, 25);
        $show = $page->show();
        $counting = $page->totalRows;
        $list = $userModel->where($where)->order($order)->limit($page->firstRow . ',' . $page->listRows)->select();

        $return = array(
            'username' => $username,
            'nickname' => $nickname,
            'is_lock' => $is_lock,
            'from' => $from,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'order_number' => $order_number,
            'add_time' => $add_time,
            'last_login_time' => $last_login_time,
        );

        $this->assign('show', $show);
        $this->assign('count',$count);
        $this->assign('return', $return);
        $this->assign('counting',$counting); 
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * [editUser 用户信息]
     * @author shichun <[672517056@qq.com]>
     */
    public function editUser() {
        if ( IS_POST ) {
        } else {
            $id       = I('get.id', '', 'int');
            $user  = M('user');

            if ( empty($id) ) {
                $this->error('参数丢失');
            }
            $userInfo = M('user')->where(array('id'=>$id))->find();
            
            if (empty($userInfo)) {
                $this->error('没有该会员');
            } else {
                $dbPrefix = C('DB_PREFIX');
                $sql= "SELECT count(*) AS `count` FROM " .$dbPrefix . "agent WHERE `user_id`= ".$id;
                $c=M('agent')->query($sql);
                $userInfo['count']=$c[0]['count'];
                $this->assign('count',$count);
                $this->assign('userInfo', $userInfo);
                $this->display();
              }
          }
    }


       /**
     * [editUserpassword 修改支付密码]
     * @author shichun <[672517056@qq.com]>
     */
    public function editUserpassword() {
        if (IS_POST) {
            $userId   = I('post.id', '', 'int');
            $pay_password = I('post.pay_password', '');
            $user = M('user');
            $userInfo = $user->where(array('id'=> $userId))->count();
            if ($userInfo > 0) {
                $pay_password = encrypt($pay_password);
            
                if ($user->where(array('id'=> $userId))->save(array('pay_password'=> $pay_password))!== false) {
                    exit(statusCode());
                } else {
                    exit(statusCode('', 400000, '修改支付密码失败'));
                }
            } else {
                exit(statusCode('', 400000, '没有该会员'));
            }
        } else {
            $this->assign('userInfo', $userInfo);
            $this->display();
        }
    }

      /**
     * [setOnSale 用户设置恢复屏蔽]
     * @author shichun <[672517056@qq.com]>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
     public function setOnSale() {
        $user = M('user');
        
        $userId = I('get.id');
        $is_lock = I('get.is_lock');
       
        if ($user->where(array('id'=>$userId))->data(array('is_lock'=>$is_lock))->save()) {  
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }

    /**
     * [agentSetOnSale 商家设置恢复屏蔽]
     * @author shichun <[672517056@qq.com]>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function agentSetOnSale() {
        $agent = M('agent');
        $agentId = I('get.id');
        $is_on_sale = I('get.is_on_sale');
        if ($agent->where(array('id'=>$agentId))->data(array('is_on_sale'=>$is_on_sale))->save()) {
            ( empty($is_on_sale) ) ?
                M('goods')->where(['agent_id'=>$agentId,'is_lock'=>'1'])->save(['is_lock'=>'0']) :
                M('goods')->where(['agent_id'=>$agentId,'is_delete'=>'0'])->save(['is_lock'=>'1']);
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }

    /**
     * [agentList 商家列表]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function agentList() {
        $dbPrefix = C('DB_PREFIX');
        $agent_name         = I('get.agent_name', '','urldecode');
        $manager            = I('get.manager', '','urldecode');
        $username           = I('get.username', '','urldecode');
        $is_on_sale         = I('get.is_on_sale', '-1');
        $startTime          = I('get.start_time','');
        $endTime            = I('get.end_time','');
        $category_id        = I('get.category_id', '','urldecode');
        $goods_number       = I('get.goods_number', '-1', 'string');
        $order_number       = I('get.order_number', '-1', 'string');
        $add_time           = I('get.add_time', '-1', 'string');
        $last_login_time    = I('get.last_login_time', '-1', 'string');
        $categoryModel = M('agent_category');
        $agentCategoryRelevance = M('agent_category_relevance');
        
        $whereStr = '';
        $link_parameter = '';
        $orderBy = '`a`.`id` DESC';
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
        if (!empty($username)) {
            $whereStr .= " AND `u`.`username` LIKE '%{$username}%'";
            $link_parameter .= '/username/' . $username;
        }
        //店铺状态
        if ($is_on_sale != '-1') {
            $whereStr .= " AND `a`.`is_on_sale` = '{$is_on_sale}'";
            $link_parameter .= '/is_on_sale/' . $is_on_sale;
           
        }
        //注册时间
        if ( !empty($startTime) && !empty($endTime) ) {
            $whereStr .= " AND `u`.`add_time` BETWEEN {$startTime} AND {$endTime}";
            $link_parameter .= '/startTime/' . $startTime . '/endTime/' . $endTime;
        } else {
            $startTime = strtotime('2016-1-1');
            $endTime = strtotime('+1 days');
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

        // 商品数量排序
        if ( $goods_number != '-1' ) {
            // 0：升序，1：降序
            $orderBy = ( empty($goods_number) ) ? '`a`.`goods_number` ASC' : '`a`.`goods_number` DESC';
            $orderBy .= ', `a`.`id` DESC';
        }

        // 订单量排序
        if ( $order_number != '-1' ) {
            // 0：升序，1：降序
            $orderBy = ( empty($order_number) ) ? '`u`.`order_number` ASC' : '`u`.`order_number` DESC';
            $orderBy .= ', `a`.`id` DESC';
        }

        // 注册时间排序
        if ( $add_time != '-1' ) {
            // 0：升序，1：降序
            $orderBy = ( empty($add_time) ) ? '`u`.`add_time` ASC' : '`u`.`add_time` DESC';
            $orderBy .= ', `a`.`id` DESC';
        }

        // 最近访问排序
        if ( $last_login_time != '-1' ) {
            // 0：升序，1：降序
            $orderBy = ( empty($last_login_time) ) ? '`u`.`last_login_time` ASC' : '`u`.`last_login_time` DESC';
            $orderBy .= ', `a`.`id` DESC';
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
        
        $sql = "SELECT `a`.`id`, `a`.`is_on_sale`, `a`.`agent_name`, `a`.`address`, `a`.`manager`, `a`.`goods_number`, `u`.`username`, `u`.`order_number`, `u`.`add_time`,`u`.`last_login_time` 
                FROM `{$dbPrefix}agent` AS `a`  
                LEFT JOIN `{$dbPrefix}user` AS `u` ON `u`.`id` = `a`.`user_id`
                WHERE 1{$whereStr} 
                ORDER BY {$orderBy} 
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

        $return = array(
            'agent_name' => $agent_name,
            'manager' => $manager,
            'username' => $username,
            'is_on_sale' => $is_on_sale,
            'category_name' => $category_name,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'goods_number' => $goods_number,
            'order_number' => $order_number,
            'add_time' => $add_time,
            'last_login_time' => $last_login_time,
        );

        $categoryList = $categoryModel->select();
        $this->assign('show', $show);
        $this->assign('return', $return);
        $this->assign('counting',$counting);
        $this->assign('list', $list);
        $this->assign('categoryList', $categoryList);
        $this->display();
    }

    /**
     * [editAgent 商家信息]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function editAgent() {
        if ( IS_POST ) {
        } else {
            $id = I('get.id', '', 'intval');
            $userModel = M('user');
            $agentModel = M('agent');
            $goodsModel = M('goods');
            $agentCategory = M('agent_category');
            $agentCategoryRelevance = M('agent_category_relevance');
            if ( empty($id) ) { 
                $this->error('参数丢失');
            }
            $agentInfo = $agentModel->find($id);
            if (empty($agentInfo)) {
                $this->error('没有该会员');
            }
            
            //最近访问时间
            $userInfo = $userModel->field('`username`, `last_login_time`')->find($agentInfo['user_id']);
            $agentInfo = array_merge($userInfo, $agentInfo);
            
            //商品总数
            $agentInfo['sum'] = $goodsModel->where(array('agent_id'=> $id))->count();

            //商品销售
            $where = array(
                'agent_id' => $id,
                'goods_type' => '0',
            );
            $agentInfo['goods'] = $goodsModel->where($where)->field('SUM(`sale_number`) AS `number`')->find();
            $agentInfo['goods'] = !empty($agentInfo['goods']['number']) ? $agentInfo['goods']['number'] : 0;

            //积分销售
            $where['goods_type'] = '1';
            $agentInfo['point'] = $goodsModel->where($where)->field('SUM(`sale_number`) AS `number`')->find();
            $agentInfo['point'] = !empty($agentInfo['point']['number']) ? $agentInfo['point']['number'] : 0;

            //总销售
            $agentInfo['numberTotal'] = $agentInfo['goods'] + $agentInfo['point'];

            //上架积分商品
            $where = array(
                'agent_id' => $id,
                'goods_type' => '0',
                'is_on_sale' => '1',
            );
            $agentInfo['is_point'] = $goodsModel->where($where)->count();
           
            //上架基础商品
            $where['goods_type'] = '1';
            $agentInfo['is'] = $goodsModel->where($where)->count();
            
            //下架基础商品
            $agentInfo['sale'] = $goodsModel->where(array('agent_id'=> $id, 'goods_type'=> '0', 'is_on_sale'=> '0'))->count();

            //商家分类
            $ids = $agentCategoryRelevance->where(array('agent_id'=> $id))->select();
            if ( !empty($ids) ) {
                $idsData = array_column($ids, 'category_id');
                $categoryData = $agentCategory->where(array('id'=> array('IN', $idsData)))->field('`category_name`')->select();
                $agentInfo['category_name'] = implode(',', array_column($categoryData, 'category_name'));
            } else {
                $agentInfo['category_name'] = '-';
            }

            $this->assign('count',$count);
            $this->assign('agentInfo', $agentInfo);
            $this->display();
        }
    }

    /**
     * [editAgentpassword 修改提现密码]
     * @author shichun <[672517056@qq.com]>
     */
    public function editAgentpassword() {
        if (IS_POST) {
            $userId   = I('post.id', '', 'int');
            $withdraw_password = I('post.withdraw_password', '');
            $user = M('user');
            $where = array(
                'id' => $userId,
                'status' => '1',
            );
            $userInfo = $user->where($where)->count();
            if ($userInfo > 0) {
                $withdraw_password = encrypt($withdraw_password);
                if ($user->where(array('id'=> $userId))->save(array('withdraw_password'=> $withdraw_password)) !== false ) {
                    exit(statusCode());
                } else {
                    exit(statusCode('', 400000, '修改提现密码失败'));
                }
            } else {
                exit(statusCode('', 400000, '没有该会员'));
            }
        } else {
            $this->assign('agentInfo', $agentInfo);
            $this->display();
        }
    }
}