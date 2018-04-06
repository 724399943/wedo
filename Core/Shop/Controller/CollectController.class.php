<?php
namespace Shop\Controller;
class CollectController extends BaseController {
    /**
     * [toCollect 加入收藏]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function toCollect() 
    {
        if (IS_POST) {
            $id = I('id');
            $type = I('type', '0'); // 收藏类型（0：商品，1：店铺） 
            $userId = session('userId');
            $userCollect = D('UserCollect');
            $data = $userCollect->create(I('post.'), 1);
            $where['user_id'] = $userId;
            // 检测商品/店铺是否存在
            if ( $type == '1' ) {
                $where['agent_id'] = $id;
                $agent = M('agent');
                if ( $agent->where(array('id'=> $id))->count() <= 0 ) {
                    exit(statusCode(array(), 400000, L('_PC_USER_MERCHANT_EXISTS_')));
                }
                $data['agent_id'] = $id;
            } else {
                $where['goods_id'] = $id;
                $goods = M('goods');
                if ( $goods->where(array('id'=> $id))->count() <= 0 ) {
                    exit(statusCode(array(), 400000, L('_PC_USER_GOODS_EXISTS_')));
                }
                $data['goods_id'] = $id;
            }
            // 检测商品/店铺是否已收藏过
            if ( $userCollect->where($where)->count() > 0 ) {
                ( $type == '1' ) ? 
                    exit(statusCode(array(), 400000, L('_PC_USER_MERCHANT_COLLECTED_'))) : 
                    exit(statusCode(array(), 400000, L('_PC_USER_GOODS_COLLECTED_')));
            }
            $data['user_id'] = $userId;
            unset($data['id']);
            $collect_id = $userCollect->add($data);
            (!empty($collect_id)) ? exit(statusCode(array('collect_id'=> $collect_id))) : exit(statusCode(array(), 100002));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [moreCollect 多商品收藏]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function moreCollect() {
        if (IS_POST) {
            $ids = I('ids');
            $idsData = explode(',', $ids);
            $userId = session('userId');
            $goodsModel = M('goods');
            $userCollectModel = D('UserCollect');
            $addData = array();
            $result = true;
            $time = time();
            // 检测商品是否存在以及是否收藏过
            foreach ($idsData as $key => $value) {
                if ( $goodsModel->where(array('id'=> $value))->count() <= 0 ) {
                    exit(statusCode(array(), 400000, L('_PC_USER_GOODS_EXISTS_')));
                }
                $where = array(
                    'user_id'   => $userId,
                    'goods_id'  => $value,
                    'type'      => '0'
                );
                if ( $userCollectModel->where($where)->count() == 0 ) {
                    $addData[] = array(
                        'user_id' => $userId,
                        'goods_id' => $value,
                        'type' => '0',
                        'add_time' => $time
                    );
                }
            }
            if ( empty($addData) ) {
                exit(statusCode(array(), 400000, L('_PC_USER_GOODS_COLLECTED_')));
            }
            if ( $userCollectModel->addAll($addData) !== false ) {
                exit(statusCode());
            } else {
                exit(statusCode(array(), 100002));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [collectGoods 商品收藏]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function collectGoods() 
    {
        $goods_name =  I('request.goods_name', '', 'urldecode');
        $express_type = I('request.express_type', '-1');
        if (IS_POST) {
            $userId = session('userId');
            $collectModel = D('UserCollect');
            $goodsModel = D('Goods');
            $returnData['list'] = $collectModel->getCollectGoods($userId, $this->limitStart, PAGE_LIMIT);
            $returnData['list'] = $goodsModel->checkGoodsCollect2($userId, $returnData['list']);
            exit(statusCode($returnData));
        } else {
            $return = array(
                'goods_name' => $goods_name,
                'express_type' => $express_type,
            );
            $this->assign('return', $return);
            $this->display();
        }
    }

    /**
     * [collectAgent 店铺收藏]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function collectAgent() 
    {
       if (IS_POST) {
            $userId = session('userId');
            $collectModel = D('UserCollect');
            $parameter = array(
                'userId' => $userId,
                'longitude' => I('longitude', '113.37763'),
                'latitude' => I('latitude', '23.13275'),
                'limitStart' => $this->limitStart,
                'limit' => PAGE_LIMIT
            );
            $returnData['list'] = $collectModel->getCollectAgent($parameter);
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        } 
    }

    /**
     * [delCollect 删除收藏]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function delCollect() 
    {
        if ( IS_POST ) {
            $ids = I('ids');
            $userId = session('userId');
            $collectModel = D('UserCollect');
            if ($collectModel->where(array('id'=> array('IN', $ids), 'user_id'=> $userId))->delete()) {
                exit(statusCode());
            } else {
                exit(statusCode(array(), 100002));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }
}