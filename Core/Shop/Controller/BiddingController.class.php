<?php
namespace Shop\Controller;
// 竞价控制器
class BiddingController extends BaseController {
    /**
     * [notBiddingGoods 未参与竞价商品]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function notBiddingGoods() {
        if ( IS_POST ) {
            $bidding = D('Bidding');
            $parameter = array(
                'agentId' => session('agentId'),
                'keyword' => I('keyword', '', 'urldecode'),
                'category_id' => I('category_id', ''),
                'agent_category_id' => I('agent_category_id', ''),
                'bidding_type' => I('bidding_type', '0'),
                'type' => I('type', '0'),
                'page' => $this->page,
                'limitStart' => $this->limitStart,
                'limit' => PAGE_LIMIT
            );
            $returnData = $bidding->getNotBiddingGoods($parameter);
            exit(statusCode($returnData));
        } else {
            $this->display();
        }
    }

    /**
     * [biddingRecord 店铺竞价记录]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function biddingRecord() {
        $bidding_type = I('bidding_type', '0');
        $banner_type = I('banner_type', '-1');
        $status = I('status', '-1');
        if ( IS_POST ) {
            $bidding = D('Bidding');
            $agent_name = I('agent_name', '', 'urldecode');
            $goods_name = I('goods_name', '', 'urldecode');
            $category_id = I('category_id', '');
            $agent_category_id = I('agent_category_id', '');
            $parameter = array(
                'status' => $status,
                'agent_name' => $agent_name,
                'goods_name' => $goods_name,
                'category_id' => $category_id,
                'agent_category_id' => $agent_category_id,
                'bidding_type' => $bidding_type,
                'banner_type' => $banner_type,
                'agentId' => session('agentId'),
                'recordType' => '0',
                'type' => I('type', '0'),
                'page' => $this->page,
                'limitStart' => $this->limitStart,
                'limit' => PAGE_LIMIT
            );
            if ( in_array($bidding_type, array('0', '1')) ) {
                $returnData = $bidding->getBiddingGoodsRecord($parameter);
            } else {
                $returnData = $bidding->getBiddingRecord($parameter);
            }
            exit(statusCode($returnData));
        } else {
            $this->assign('bidding_type', $bidding_type);
            $this->assign('banner_type', $banner_type);
            $this->assign('status', $status);
            $this->loadSearch();
            $this->display();
        }
    }

    /**
     * [platformBiddingRecord 平台竞价记录]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function platformBiddingRecord() {
        $bidding_type = I('bidding_type', '0');
        $banner_type = I('banner_type', '-1');
        if ( IS_POST ) {
            $bidding = D('Bidding');
            $agent_name = I('agent_name', '', 'urldecode');
            $goods_name = I('goods_name', '', 'urldecode');
            $category_id = I('category_id', '');
            $agent_category_id = I('agent_category_id', '');
            $parameter = array(
                'agent_name' => $agent_name,
                'goods_name' => $goods_name,
                'category_id' => $category_id,
                'agent_category_id' => $agent_category_id,
                'bidding_type' => $bidding_type,
                'banner_type' => $banner_type,
                'agentId' => session('agentId'),
                'recordType' => '1',
                'type' => I('type', '0'),
                'page' => $this->page,
                'limitStart' => $this->limitStart,
                'limit' => PAGE_LIMIT
            );
            if ( in_array($bidding_type, array('0', '1')) ) {
                $returnData = $bidding->getBiddingGoodsRecord($parameter);
            } else {
                $returnData = $bidding->getBiddingRecord($parameter);
            }
            exit(statusCode($returnData));
        } else {
            $this->assign('bidding_type', $bidding_type);
            $this->assign('banner_type', $banner_type);
            $this->loadSearch();
            $this->display();
        }
    }

    private function loadSearch() {
        $agentId = I('id');
        $agentId = !empty($agentId) ? $agentId : session('agentId');
        $return = array(
            'agent_name' => I('agent_name', '', 'urldecode'),
            'goods_name' => I('goods_name', '', 'urldecode'),
            'category_id' => I('category_id', ''),
            'agent_category_id' => I('agent_category_id', ''),
        );
        $categoryList = M('goods_category')->select();
        $agentCategoryList = M('agent_goods_category')->where(array('agent_id'=> $agentId))->select();
        $this->assign('return', $return);
        $this->assign('categoryList', $categoryList);
        $this->assign('agentCategoryList', $agentCategoryList);
    }

    /**
     * [biddingIndexGoods 首页商品申请竞价]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function biddingIndexGoods() {
        $this->loadSearch();
        $this->display();
    }

    /**
     * [biddingFavorableGoods 优惠商品竞价]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function biddingFavorableGoods() {
        $this->loadSearch();
        $this->display();
    }

    /**
     * [biddingAgent 商家竞价]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function biddingAgent() {
        $this->display();
    }

    /**
     * [biddingBanner 竞价广告位]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function biddingBanner() {
        $this->loadSearch();
        $this->display();
    }

    /**
     * [toBiddingIndexGoods 申请竞价首页商品]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function toBiddingIndexGoods() {
        $agentId = session('agentId');
        if ( IS_POST ) {
            $bidding = D('Bidding');
            $data = $bidding->create(I('post.'), 4);
            if ( !empty($data) ) {
                $data['agent_id'] = $agentId;
                $data['order_sn'] = $bidding->makeBiddingSn();
                // edit 
                $data['is_pay'] = '1';
                ( $bidding->add($data) ) ? 
                    exit(statusCode(array('order_sn'=> $data['order_sn']))) : 
                    exit(statusCode(array(), 100002));
            } else {
                exit(statusCode(array(), 400000, $bidding->getError()));
            }
        } else {
            $goodsId = I('get.goods_id', '');
            $goods = $this->getGoodsDetail($goodsId);
            $this->assign('goods', $goods);
            $this->display();
        }
    }

    /**
     * [toBiddingFavorableGoods 申请竞价优惠商品]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function toBiddingFavorableGoods() {
        $agentId = session('agentId');
        if ( IS_POST ) {
            $bidding = D('Bidding');
            $data = $bidding->create(I('post.'), 5);
            if ( !empty($data) ) {
                $data['agent_id'] = $agentId;
                $data['order_sn'] = $bidding->makeBiddingSn();
                // edit 
                $data['is_pay'] = '1';
                ( $bidding->add($data) ) ? 
                    exit(statusCode(array('order_sn'=> $data['order_sn']))) : 
                    exit(statusCode(array(), 100002));
            } else {
                exit(statusCode(array(), 400000, $bidding->getError()));
            }
        } else {
            $goodsId = I('get.goods_id', '');
            $goods = $this->getGoodsDetail($goodsId);
            $this->assign('goods', $goods);
            $this->display();
        }
    }

    /**
     * [toBiddingAgent 申请竞价店铺]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function toBiddingAgent() {
        $agentId = session('agentId');
        if ( IS_POST ) {
            $bidding = D('Bidding');
            $data = $bidding->create(I('post.'), 6);
            if ( !empty($data) ) {
                $data['agent_id'] = $agentId;
                $data['order_sn'] = $bidding->makeBiddingSn();
                // edit 
                $data['is_pay'] = '1';
                ( $bidding->add($data) ) ? 
                    exit(statusCode(array('order_sn'=> $data['order_sn']))) : 
                    exit(statusCode(array(), 100002));
            } else {
                exit(statusCode(array(), 400000, $bidding->getError()));
            }
        } else {
            $agentInfo = D('Agent')->getAgentInfo($agentId, '`agent_name`, `telephone`, `address`, `introduction`');
            $this->assign('agentInfo', $agentInfo);
            $this->display();
        }
    }

    /**
     * [toBiddingBanner 申请竞价广告位]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function toBiddingBanner() {
        $agentId = session('agentId');
        if ( IS_POST ) {
            $bidding = D('Bidding');
            $data = $bidding->create(I('post.'), 7);
            if ( !empty($data) ) {
                $data['agent_id'] = $agentId;
                $data['order_sn'] = $bidding->makeBiddingSn();
                // edit 
                $data['is_pay'] = '1';
                ( $bidding->add($data) ) ? 
                    exit(statusCode(array('order_sn'=> $data['order_sn']))) : 
                    exit(statusCode(array(), 100002));
            } else {
                exit(statusCode(array(), 400000, $bidding->getError()));
            }
        } else {
            $banner_type = I('banner_type', '0');
            if ( !empty($banner_type) ) {
                $goodsId = I('goods_id');
                $goods = $this->getGoodsDetail($goodsId);
                $this->assign('goods', $goods);
            } else {
                $agentInfo = D('Agent')->getAgentInfo($agentId, '`agent_name`, `telephone`, `address`, `introduction`');
                $this->assign('agentInfo', $agentInfo);
            }
            $this->assign('banner_type', $banner_type);
            $this->display();
        }
    }

    private function getGoodsDetail($goodsId) {
        if ( empty($goodsId) ) {
            exit(statusCode(array(), 100002));
        }
        $agentId = session('agentId');
        $where = array(
            'id' => $goodsId,
            'agent_id'  => $agentId
        );
        $goods = M('goods')->where($where)->field('`id`, `goods_name`, `goods_price`, `goods_image`, `introduction`')->find();
        return $goods;
    }

    /**
     * [payForBidding 支付竞价]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function payForBidding() {
        $agentId = session('agentId');
        $order_sn = I('request.order_sn');
        $bidding = D('Bidding');
        $where = array(
            'agent_id' => $agentId,
            'order_sn' => $order_sn
        );
        $order = $bidding->where($where)->find();
        if ( empty($order) ) {
            ( IS_POST ) ? exit(statusCode(array(), 100002)) : $this->error('没有该订单');
        }
        if ( IS_POST ) {
            $userId = session('userId');
            $parameter = array(
                'userId' => $userId,
                'order' => $order
            );
            $returnData = $bidding->payForBidding($parameter);
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

    /**
     * [photoUpload 图片上传]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function photoUpload() {
        // 图片保存路径
        fileUpload('Bidding/', function($e) {
            echo json_encode(array('error'=>0, 'url'=>trim($e['filePath'], '.')));
        });
    }
}