<?php
namespace Admin\Controller;
class GoodsCommentController extends BaseController {

	/**
     * [goodsComment 评价管理]
     * @author shichun <[672517056@qq.com]> 
     */
    public function goodsComment() {
        $dbPrefix = C('DB_PREFIX');
        $commentModel = M('goods_comment');
        $whereStr = "";
        $leftJoin = "";
        $link_parameter = '';
        $express_type    = I('get.express_type','-1'); 
        $star    = I('get.star','-1');
        $status    = I('get.status','-1'); 
        $agent_name   = I('get.agent_name','','urldecode');
        $goods_name   = I('get.goods_name','','urldecode');  
        //商品名称
        if ( !empty($goods_name)) {
            $whereStr .= " AND `od`.`goods_name` LIKE '%{$goods_name}%'";
            $link_parameter .= '/goods_name/' . $goods_name;
        }
        //店铺名称
        if ( !empty($agent_name)) {
            $whereStr   .= " AND `a`.`agent_name` LIKE '%{$agent_name}%' ";
            $link_parameter .= '/agent_name/' . $agent_name;
        }
        //配送方式
        if ($express_type != '-1') {
            $whereStr .= " AND `o`.`express_type` = '{$express_type}'";
            $link_parameter .= '/express_type/' . $express_type;
        }
        //评论状态
        if ( $status != '-1') {
            $whereStr .= " AND `gc`.`status` = '{$status}'";
            $link_parameter .= '/status/' . $status;
        }
        //评分
        if ( $star != '-1') {
            $whereStr .= " AND `gc`.`star` = '{$star}'";
            $link_parameter .= '/star/' . $star;
        }
       
        $return['express_type'] = $express_type;
        $return['star'] = $star;
        $return['status'] = $status;
        $return['agent_name'] = $agent_name;
        $return['goods_name'] = $goods_name;
        
        $countSql = "SELECT count(*) AS `count` 
                FROM `{$dbPrefix}goods_comment` AS `gc`
                LEFT JOIN `{$dbPrefix}order` AS `o` ON `o`.`order_sn` = `gc`.`order_sn`
                LEFT JOIN `{$dbPrefix}order_detail` AS `od` ON `od`.`order_sn` = `gc`.`order_sn`
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `a`.`id` = `gc`.`agent_id`
                {$leftJoin}
                WHERE 1{$whereStr} ";
        $countData = $commentModel->query($countSql);
        $page = new \Think\Page($countData['0']['count'], 25);
        $show = $page->show();
        $counting=$page->totalRows;
        $sql = "SELECT `gc`.*,`a`.`agent_name`,`o`.`express_type`,`od`.`goods_name`,`od`.`price`,`od`.`goods_type`
                FROM `{$dbPrefix}goods_comment` AS `gc`
                LEFT JOIN `{$dbPrefix}order` AS `o` ON `o`.`order_sn` = `gc`.`order_sn`
                LEFT JOIN `{$dbPrefix}order_detail` AS `od` ON `od`.`order_sn` = `gc`.`order_sn`
                LEFT JOIN `{$dbPrefix}agent` AS `a` ON `a`.`id` = `gc`.`agent_id`
                {$leftJoin}
                WHERE 1{$whereStr} 
                ORDER BY `gc`.`id` DESC 
                LIMIT {$page->firstRow}, {$page->listRows}";
        $goodsComment = $commentModel->query($sql);
       
        $this->assign('show', $show);
        $this->assign('goodsComment', $goodsComment);
        $this->assign('counting',$counting);
        $this->assign('return', $return);
        $this->display();
    }

    /**
     * [setOnSale 评价状态屏蔽]
     * @author shichun <[672517056@qq.com]>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function setOnSale() {
        $goods = M('goods_comment');
        $goodsId = I('get.ids');
        $status = I('get.status');
        if (empty($goodsId)) {
            $this->error('请选择要审核的商品！');
        }elseif ($goods->where(array('id' => array('IN',$goodsId)))->save(array('status'=> $status)) !== false)  {
            $this->success('审核完成！');
        } else {
            $this->error('审核失败！');
        } 
    }
}