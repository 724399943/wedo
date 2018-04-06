<?php
namespace Shop\Controller;
use Think\Controller;

class GoodsCheckController extends BaseController {
	/**
	 * [goodsToAuth 商品认证]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
	public function goodsToAuth() {
        $agentId = I('id');
        $agentId = !empty($agentId) ? $agentId : session('agentId');
		if ( IS_POST ) {
            $goodsCheck = D('GoodsCheck');
            $data = $goodsCheck->create(I('post.'), 4);
            if ( !empty($data) ) {
                $data['agent_id'] = $agentId;
                $data['total'] = C('authPrice');
                $data['check_type'] = '0';
                ( $goodsCheck->add($data) ) ? 
                    exit(statusCode(array('order_sn'=> $data['order_sn']))) : 
                    exit(statusCode(array(), 100002));
            } else {
                exit(statusCode(array(), 400000, $goodsCheck->getError()));
            }
		} else {
            $return = array(
                'goods_name' => I('goods_name', '', 'urldecode'),
                'is_auth' => I('is_auth', '-1'),
                'category_id' => I('category_id', ''),
                'agent_category_id' => I('agent_category_id', ''),
            );
            $categoryList = M('goods_category')->select();
            $agentCategoryList = M('agent_goods_category')->where(array('agent_id'=> $agentId))->select();
            $this->assign('return', $return);
            $this->assign('categoryList', $categoryList);
            $this->assign('agentCategoryList', $agentCategoryList);
			$this->display();
		}
	}

    /**
     * [goodsToTop 商品置顶]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function goodsToTop() {
        $agentId = I('id');
        $agentId = !empty($agentId) ? $agentId : session('agentId');
        if ( IS_POST ) {
            $goodsCheck = D('GoodsCheck');
            $data = $goodsCheck->create(I('post.'), 5);
            if ( !empty($data) ) {
                $data['agent_id'] = $agentId;
                $data['total'] = C('topPrice');
                $data['check_type'] = '1';
                ( $goodsCheck->add($data) ) ? 
                    exit(statusCode(array('order_sn'=> $data['order_sn']))) : 
                    exit(statusCode(array(), 100002));
            } else {
                exit(statusCode(array(), 400000, $goodsCheck->getError()));
            }
        } else {
            $return = array(
                'goods_name' => I('goods_name', '', 'urldecode'),
                'is_recommend' => I('is_recommend', '-1'),
                'category_id' => I('category_id', ''),
                'agent_category_id' => I('agent_category_id', ''),
            );
            $categoryList = M('goods_category')->select();
            $agentCategoryList = M('agent_goods_category')->where(array('agent_id'=> $agentId))->select();
            $this->assign('return', $return);
            $this->assign('categoryList', $categoryList);
            $this->assign('agentCategoryList', $agentCategoryList);
            $this->display();
        }
    }

	/**
     * [payForCheck 支付商品审核]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function payForCheck() {
        $agentId = session('agentId');
        $order_sn = I('request.order_sn');
        $goodsCheck = D('GoodsCheck');
        $where = array(
            'agent_id' => $agentId,
            'order_sn' => $order_sn
        );
        $order = $goodsCheck->where($where)->find();
        if ( empty($order) ) {
            ( IS_POST ) ? exit(statusCode(array(), 100002)) : $this->error('没有该订单');
        }
        if ( IS_POST ) {
            $userId = session('userId');
            $parameter = array(
                'userId' => $userId,
                'order' => $order
            );
            $returnData = $goodsCheck->payForCheck($parameter);
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