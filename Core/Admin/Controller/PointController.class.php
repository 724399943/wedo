<?php
namespace Admin\Controller;
class PointController extends BaseController {
    /**
    * [pointGoodsList 商品管理列表]
    * @author shichun <672517056@qq.com>
    * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
    * @return    [type]        [description]
    */
    public function pointGoodsList() {
        $goods = M('goods');

        $agent_name     = I('get.agent_name', '', 'urldecode');
        $goods_name     = I('get.goods_name', '', 'urldecode');
        $goods_type     = I('get.goods_type','-1');
        $is_on_sale     = I('get.is_on_sale','-1');
        $link_parameter = '';
        //店铺
        if ( !empty($agent_name) ) {
            $where['agent_name'] = array('LIKE' , "%{$agent_name}%");
            $link_parameter .= '/agent_name/' . $agent_name;
            $id = M('agent')->where($where)->field('id')->select();
            foreach($id as $key =>$value){
                $idarr[$key]=$value['id'];
            }
            if ( !empty($id)) {
                $where['agent_id']=array('IN',$idarr);
                unset($where['agent_name']);
            } else{
                unset($where);
                $where['id'] = 0;
            }
        }
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

        $where['goods_type'] = 1;
        $count = $goods->where($where)->count();
        $page = new \Think\Page($count, 25);
        $page->setConfig('link', '/Admin/Point/pointGoodsList/p/zz' . $link_parameter);
        $show = $page->show();
        $pointGoodsList = $goods->where($where)->order('`id` DESC')->limit($page->firstRow, $page->listRows)->select();

        $userCollect = M('user_collect');
        foreach ($pointGoodsList as $key => &$value) {
            //店铺名称
            $value['agent_name'] = getAgentName($value['agent_id']);
            //收藏
            $value['collect_number'] = $userCollect->where(array('goods_id'=> $value['id']))->count();
        }
       
        $return['agent_name'] = $agent_name;
        $return['goods_name'] = $goods_name;
        $return['goods_type'] = $goods_type;
        $return['is_on_sale'] = $is_on_sale;
        $this->assign('pointGoodsList', $pointGoodsList);
        $this->assign('return', $return);
        $this->assign("show", $show);
        $this->assign('agentList', $agentList);
        $this->display();
    }

    /**
     * [pointEditGoods 编辑商品]
     * @author shichun <[672517056@qq.com]>
     */
    public function pointEditGoods() {
        $goodsModel = D('Goods');
        $goodsId = I('id', '', 'intval');
        if (IS_POST) {
            $data = $goodsModel->create(I('post.'), 5);
            if (empty($data)) {
                $this->error($goods->getError());
            } else {
                // 获取商品信息
                $goodsData = $goodsModel->find($goodsId);
                // 保存商品图片
                $images = I('images', '', 'urldecode');
                if (!empty($images)) {
                    $goodsImages = $goodsModel->editGoodsImages($images, $goodsData);
                    $data['goods_image'] = $goodsImages['goods_image'];
                    $data['goods_images_id'] = $goodsImages['goods_images_id'];
                } else {
                    exit(statusCode(array(), 400000, '请上传商品图片！'));
                }

                // 添加商品详情
                $description = I('description', '', 'htmlspecialchars_decode');
                $extensionId = $goodsModel->editGoodsExtension($description, $goodsData);
                $data['goods_ext_id'] = $extensionId;

                ( $goodsModel->save($data) !== false ) ?
                        $this->success('编辑成功') :
                        $this->error('编辑失败');
            }
        } else {
            // 商品基本信息
            $goodsInfo = $goodsModel->where(array('id'=>$goodsId))->find($goodsId);

            // 商品图片
            $goodsImg     = M('goods_images');
            $goodsImgList = $goodsImg->where(array('id'=>array('in', $goodsInfo['goods_images_id'])))->select();
            // 商品详情
            $goodsExt     = M('goods_extension');
            $goodsExtInfo = $goodsExt->where(array('id'=>$goodsInfo['goods_ext_id']))->find();

            $this->assign('goodsImg', $goodsImgList);
            $this->assign('goodsInfo', $goodsInfo);
            $this->assign('goodsExtInfo', $goodsExtInfo['goods_desc']);
            $this->display();
        }
    }


    /**
     * [delGoods 批量删除商品]
     * @author shichun <672517056@qq.com>
     * @copyright Copyright (c)           2015 Xcrozz (http://www.xcrozz.com)                           
     * @return    [type]        [description]
     */
    public function delGoods(){
        $goodsModel = M('Goods');
        $ids = I('get.ids', '');
        if (empty($ids)) {
            $this->error('请选择要删除的订单！');exit;
        }
        $ids = trim($ids, ',');
        $where['is_delete'] = 1;
        if ($goodsModel->where(array('id'=> array('IN', $ids)))->data($where)->save() !== false) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }
     
    /**
     * [setOnSale 设置商品上下架]
     * @author shichun <[672517056@qq.com]>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function setOnSale() {
        $goods = M('goods');
        $goodsId = I('get.ids');
        $is_on_sale = I('get.is_on_sale');
        if (empty($goodsId)) {
            $this->error('请选择要下架的商品！');
        }elseif ($goods->where(array('id' => array('IN',$goodsId)))->save(array('is_on_sale'=> $is_on_sale)) !== false)  {
            $this->success('下架成功！');
        } else {
            $this->error('下架失败！');
        } 
    }

    /**
     * [pointAddGoods 添加商品]
     * @author shichun <[672517056@qq.com]>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function pointAddGoods() {
        if (IS_POST) {
            $goodsModel = D('Goods');
            $data = $goodsModel->create(I('post.'), 4);
            if (empty($data)) {
                $this->error($goods->getError());
            } else {
                // 保存商品图片
                $images = I('images', '', 'urldecode');
                if (!empty($images)) {
                    $goodsImages = $goodsModel->addGoodsImages($images);
                    $data['goods_image'] = $goodsImages['goods_image'];
                    $data['goods_images_id'] = $goodsImages['goods_images_id'];
                } else {
                    exit(statusCode(array(), 400000, '请上传商品图片！'));
                }

                // 添加商品详情
                $description = I('description', '', 'htmlspecialchars_decode');
                $extensionId = $goodsModel->addGoodsExtension($description);
                $data['goods_ext_id'] = $extensionId;
                $data['goods_type'] = 1;

                ( $goodsModel->add($data) ) ?
                        $this->success('添加成功', U('Point/pointGoodsList')) :
                        $this->error('添加商品失败', U('Point/pointAddGoods'));
            }
        } else {
            $this->display('pointAddGoods');
        }
    }
   
    /**
     * [photoUpload 上传商品图片]
     * @author shichun <[672517056@qq.com]>
     */
    public function photoUpload() {
        // 图片保存路径
        $parameters['multi'] = true;
        $parameters['size'][0] = array('width'=>800, 'height'=>800);
        $parameters['size'][1] = array('width'=>350, 'height'=>350);
        $parameters['size'][2] = array('width'=>160, 'height'=>160);

        fileUpload('Goods_pic/', function($e) {
            echo json_encode(array('error'=>'', 'src'=>trim($e['filePath'], '.')));
        }, $parameters);
    }

    /**
     * [pointOrderList 积分订单列表]
     * @author shichun <672517056@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function pointOrderList() {
        $dbPrefix   = C('DB_PREFIX');
        $orderModel = M('point_order');
        $whereStr   = "";
        $leftJoin   = "";

        $order_sn     = I('get.order_sn',''); 
        $pay_state    = I('get.pay_state','-1'); 
        $startTime    = I('get.startTime',''); 
        $endTime      = I('get.endTime',''); 
        $express_type    = I('get.express_type','-1'); 
        $goods_name   = I('get.goods_name',''); 
       
        if ( $order_sn != '') {
            $whereStr .= " AND `o`.`order_sn` LIKE '%{$order_sn}%'";
        }
       
        if ( $pay_state != '-1') {
            switch ($pay_state) {
                case '0':
                    $whereStr .= " AND `o`.`is_pay` = 0 ";
                    break;
                case '1':
                    $whereStr .= " AND `o`.`is_pay` = 1 AND `o`.`delivery_status`= 0 ";
                    break;
                case '2':
                    $whereStr .= " AND `o`.`is_pay` = 1 AND `o`.`delivery_status`= 1 ";
                    break;
                case '4':
                    $whereStr .= " AND `o`.`is_pay` = 1 AND `o`.`received`= 1 ";
                    break;
                default:
                    # code...
                    break;
            }
        }
        if ($express_type != '-1') {
            $whereStr .= " AND `o`.`express_type` = '{$express_type}'";
            $link_parameter .= '/express_type/' . $express_type;
        }
        if ($startTime != '' && $endTime != '') {
            $whereStr .= " AND `o`.`pay_time` BETWEEN '{$startTime}' AND '{$endTime}'";
        } else {
            $startTime = strtotime('2015-1-1');
            $endTime = strtotime('+1 days');    
        }
        if ( $goods_name != '' ) {
          $whereStr .= " AND `u`.`goods_name` LIKE '%{$goods_name}%'";
          $link_parameter .= '/goods_name/' . $goods_name;
        }
       
        $return['order_sn'] = $order_sn;
        $return['telephone'] = $telephone;
        $return['startTime'] = $startTime;
        $return['endTime']  = $endTime;
        $return['pay_state'] = $pay_state;
        $return['express_type'] = $express_type;
        $return['goods_name'] = $goods_name;
        
        $countSql = "SELECT count(*) AS `count` FROM `{$dbPrefix}point_order` AS `o`
                   LEFT JOIN  `{$dbPrefix}point_order_detail` AS `u` ON `u`.`order_sn` = `o`.`order_sn` 
                   LEFT JOIN `{$dbPrefix}order` AS `order` ON `order`.`order_sn` = `u`.`order_sn`
                   {$leftJoin}
                    WHERE 1{$whereStr}";
        $countData = $orderModel->query($countSql);
        
        $page = new \Think\Page($countData['0']['count'], 25);
        $show = $page->show();
        $counting=$page->totalRows;
        $sql = "SELECT `o`.*, `u`.`goods_name` AS `g`,`order`.`status`
                FROM `{$dbPrefix}point_order` AS `o`
                LEFT JOIN `{$dbPrefix}point_order_detail` AS `u` ON `u`.`order_sn` = `o`.`order_sn`
                LEFT JOIN `{$dbPrefix}order` AS `order` ON `order`.`order_sn` = `o`.`order_sn`
                {$leftJoin} 
                WHERE 1{$whereStr} 
                ORDER BY `o`.`id` 
                LIMIT {$page->firstRow}, {$page->listRows}";
        $orderList = $orderModel->query($sql);

        $this->assign('show', $show);
        $this->assign('orderList', $orderList);
        $this->assign('counting',$counting);
        $this->assign('return', $return);
        $this->display();
    }

    public function pointOrderDetail() {
        $id = I('get.id');
        $orderModel = M('point_order');
        $pointOrder = $orderModel->where(array('id'=>$id))->find();
        $returnData = M('point_order_detail')->where(array('order_sn'=>$pointOrder['order_sn']))->field('id, goods_id, goods_name, unit_price, price, goods_number')->select();

        $user= M('user')->where(array('id'=>$pointOrder['user_id']))->find();
        
        $this->assign('returnData', $returnData);
        $this->assign('user', $user);
        $this->assign('pointOrder', $pointOrder); 
        $this->display();
    }

    /**
     * [pointLog 积分记录]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function pointLog() {
        $userModel = M('user');
        $pointLogModel = M('point_log');
        $nickname = I('get.nickname', '');
        $show = '';
        $list = '';
        $userInfo = '';
        $counting = 0;
        if ( !empty($nickname) ) {
            $userInfo = $userModel->where(array('nickname'=> array('LIKE', "%{$nickname}%")))->find();
            if ( empty($userInfo) ) {
                $this->error('没有搜索到相关账号', U('Point/pointLog'));
            }
            $where['to_id'] = $userInfo['id'];
            $count = $pointLogModel->where($where)->count();
            $page = new \Think\Page($count, 25);
            $page->setConfig('link', '/Admin/Point/pointLog/p/zz/nickname/' . $nickname);
            $show = $page->show();
            $counting = $page->totalRows;
            $list = $pointLogModel->where($where)->order('`id` DESC')->limit($limitStart.','.$limit)->select();
            foreach ($list as $key => &$value) {
                $value['event'] = $this->eventType($value['event_type'], $value['num']);
            }
        }
        $this->assign('list', $list);
        $this->assign('show', $show);
        $this->assign('nickname', $nickname);
        $this->assign('userInfo', $userInfo);
        $this->assign('counting', $counting);
        $this->display();
    }

    private function eventType($type, $point) {
        $eventType = array(
            '0'     => '注册推荐人',
            '1'     => '买家新注册',
            '2'     => '买家消费',
            '3'     => '买家充值',
            '4'     => '买家评价',
            '5'     => '卖家完成订单',
            '6'     => '卖家回复评价',
            '7'     => '兑换商品',
            '8'     => '推荐+' . $point . '积分',
        );
        return $eventType[$type];
    }

    /**
     * [pointSetting 积分获得规则设置]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function pointSetting() {
        if ( IS_POST ) {
            $config = M('config');
            $data   = I('post.config');
            foreach ($data as $key => $value) {
                $saveData = array();
                $saveData['config_value'] = $value['config_value'];
                $config->where(array('config_sign'=>$value['config_sign']))->save($saveData);
            }
            $this->success('更新成功！');
        } else {
            $configList = M('config')->select();
            $this->assign('configList', $configList);
            $this->display();
        }
    }
}