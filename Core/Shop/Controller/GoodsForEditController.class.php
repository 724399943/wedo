<?php
namespace Shop\Controller;
use Think\Controller;

class GoodsForEditController extends BaseController {
	/**
	 * [goodsForEdit 商品认证]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
	public function goodsForEdit() {
        $agentId = session('agentId');
        $postData = I('post.');
        $goodsForEditModel = D('GoodsForEdit');
        $data = $goodsForEditModel->create($postData, 1);
        if ( !empty($data) ) {
            unset($data['id']);
            $postData['introduction'] = htmlspecialchars_decode($postData['introduction']);
            $postData['description'] = htmlspecialchars_decode($postData['description']);
            $postData['images'] = urldecode($postData['images']);
            $data['goods_id'] = $postData['id'];
            $data['goods_data'] = json_encode($postData);
            $data['agent_id'] = $agentId;
            $data['total'] = C('goodsEditPrice');
            ( $goodsForEditModel->add($data) ) ? 
                exit(statusCode(array('order_sn'=>$data['order_sn']), 200001)) : 
                exit(statusCode(array(), 100002));
        } else {
            // exit(statusCode(array(), 400000, $goodsForEditModel->getError()));
        }
	}

    public function payForEdit() 
    {
        $agentId = session('agentId');
        $order_sn = I('request.order_sn');
        $goodsForEditModel = D('GoodsForEdit');
        $where = array(
            'agent_id' => $agentId,
            'order_sn' => $order_sn
        );
        $order = $goodsForEditModel->where($where)->find();
        if ( empty($order) ) {
            ( IS_POST ) ? exit(statusCode(array(), 100002)) : $this->error('没有该订单');
        }
        if ( IS_POST ) {
            $userId = session('userId');
            $parameter = array(
                'userId' => $userId,
                'order' => $order
            );
            $returnData = $goodsForEditModel->payForEdit($parameter);
            if ( $returnData['status'] == '400000' ) {
                exit(statusCode(array(), 400000, $returnData['message']));
            } else {
                exit(statusCode());
            }
        } else {
            $userInfo = D('User')->getUserInfo($userId, 'money');
            $this->assign('order', $order);
            $this->assign('userInfo', $userInfo);
            $this->display();
        }
    }
}