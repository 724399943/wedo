<?php
namespace Shop\Controller;
use Plugins\Money\Money;
use Plugins\Point\Point;

class AgentController extends BaseController {
    /**
     * [searchAgent 搜索店铺]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function searchAgent() {
        if ( IS_POST ) {
            $agent = D('Agent');
            $parameter['keyword']       = I('keyword', '', 'urldecode');
            $parameter['longitude']     = I('longitude', '113.37763'); //经度
            $parameter['latitude']      = I('latitude', '23.13275'); //纬度
            $parameter['meter']         = I('meter', C('DISTANCE_METER')); //米
            $parameter['limitStart']    = $this->limitStart;
            $parameter['limit'] = PAGE_LIMIT;
            $returnData['list'] = $agent->getAgentList($parameter);
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [nearbyAgent 附近店铺]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function nearbyAgent() {
        if ( IS_POST ) {
            $agent = D('Agent');
            $parameter['longitude']     = I('longitude', '113.37763'); //经度
            $parameter['latitude']      = I('latitude', '23.13275'); //纬度
            $parameter['meter']         = I('meter', C('DISTANCE_METER')); //米
            $parameter['limitStart']    = $this->limitStart;
            $parameter['limit']         = PAGE_LIMIT;
            $returnData['list'] = $agent->getAgentList($parameter);
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [agentList 店铺列表]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function agentList() {
        if (IS_POST) {
            $agent = D('Agent');
            $parameter['category_id']   = I('category_id');
            $parameter['is_hot']        = I('is_hot', '-1');
            $parameter['is_nearby']     = I('is_nearby', '-1');
            $parameter['longitude']     = I('longitude', '113.37763'); //经度
            $parameter['latitude']      = I('latitude', '23.13275'); //纬度
            $parameter['meter']         = I('meter', C('DISTANCE_METER')); //米
            $parameter['limitStart']    = $this->limitStart;
            $parameter['limit']         = PAGE_LIMIT;
            $returnData['list'] = $agent->getAgentList($parameter);
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        }
    } 

    /**
     * [recommendAgent 推荐店铺]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function recommendAgent() {
        if (IS_POST) {
            $agent = D('Agent');
            $parameter['is_recommend']  = '1';
            $parameter['is_hot']        = I('is_hot', '-1');
            $parameter['is_nearby']     = I('is_nearby', '-1');
            $parameter['longitude']     = I('longitude', '113.37763'); //经度
            $parameter['latitude']      = I('latitude', '23.13275'); //纬度
            $parameter['meter']         = I('meter', C('DISTANCE_METER')); //米
            $parameter['limitStart']    = $this->limitStart;
            $parameter['limit']         = PAGE_LIMIT;
            $returnData['list'] = $agent->getAgentList($parameter);
            if ( empty($returnData['list']) ) {
                 $parameter = array(
                    'is_recommend'  => '-1',
                    'id_sort'       => '1',
                    'is_hot'        => I('is_hot', '-1'),
                    'is_nearby'     => I('is_nearby', '-1'),
                    'longitude'     => I('longitude', '113.37763'),
                    'latitude'      => I('latitude', '23.13275'),
                    'meter'         => I('meter', C('DISTANCE_METER')),
                    'limitStart'    => $this->limitStart,
                    'limit'         => PAGE_LIMIT,
                );
                $returnData['list'] = $agent->getAgentList($parameter);
            } else {
                $count = count($returnData['list']);
                $limit = PAGE_LIMIT - $count;
                if ( $limit > 0 ) {
                    $parameter = array(
                        'is_recommend'  => '-1',
                        'id_sort'       => '1',
                        'is_hot'        => I('is_hot', '-1'),
                        'is_nearby'     => I('is_nearby', '-1'),
                        'longitude'     => I('longitude', '113.37763'),
                        'latitude'      => I('latitude', '23.13275'),
                        'meter'         => I('meter', C('DISTANCE_METER')),
                        'limitStart'    => 0,
                        'limit'         => $limit,
                    );
                    $list['list'] = $agent->getAgentList($parameter);
                    $returnData['list'] = array_merge($returnData['list'], $list['list']);
                }
            }
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [agentDetail 店铺详情]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function agentDetail() {
        if (IS_POST) {
            $id = I('id');
            $agent = D('Agent');
            $userId = session('userId');
            $returnData['list'] = $agent->getAgentDetail($id, $userId);
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [agentDesc 店铺简介]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function agentDesc() {
        if (IS_POST) {
            $id = I('id');
            $agent = D('Agent');
            $userId = session('userId');
            $returnData['list'] = $agent->getAgentDesc($id, $userId);
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [agentGoodsComment 店铺所有商品评价]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function agentGoodsComment() {
        if ( IS_POST ) {
            $agentId = I('id');
            $agentId = !empty($agentId) ? $agentId : session('agentId');
            $goodsComment = D('GoodsComment');
            $parameter = array(
                'goods_name'    => I('goods_name', '', 'urldecode'),
                'express_type'  => I('express_type', '-1'),
                'type'          => I('type', '0'),
                'star'          => I('star', '-1'),
                'is_reply'      => I('is_reply', '-1'),
                'agent_id'      => $agentId,
                'page'          => $this->page,
                'limitStart'    => $this->limitStart,
                'limit'         => PAGE_LIMIT
            );
            $returnData = $goodsComment->getComment($parameter);
            exit(statusCode($returnData));
        } else {
            $return = array(
                'goods_name' => I('get.goods_name', '', 'urldecode'),
                'is_reply' => I('get.is_reply', '-1'),
                'star' => I('get.star', '-1'),
                'express_type' => I('get.express_type', '-1'),
            );
            $this->assign('return', $return);
            $this->display();
        }
    }

    /**
     * [replyComment 店家回复]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function replyComment() 
    {
        if ( IS_POST ) {
            $data = array(
                'id' => I('id'),
                'reply_contain' => I('reply_contain')
            );
            if ( empty($data['reply_contain']) ) exit(statusCode(array(), 400000, L('_WAP_AGENT_CONTENT_')));
            $goodsComment = M('goods_comment');
            $commentData = $goodsComment->where(array('id'=> $data['id']))->field('reply_contain,order_sn')->find();
            if ( !empty($commentData['reply_contain']) ) 
            {
                exit(statusCode(array(), 400000, L('_PC_GOODS_REPLIED_THE_COMMENT_')));
            }
            $data['reply_time'] = time();
            if ( $goodsComment->save($data) !== false ) {
                // 卖家完成订单获取积分
                $point = new Point;
                $replyCommentPoint = C('replyCommentPoint');
                $parameter = array(
                    'from_id' => '0',
                    'to_id' => $userId,
                    'num' => $replyCommentPoint,
                    'type' => '1',
                    'event_type' => '6',
                    'order_sn' => $commentData['order_sn'],
                    'description' => "Review+{$replyCommentPoint}point(s)",
                );

                if ( $point->point($parameter) === false ) {
                    $result = false;
                }
                exit(statusCode());
            } else {
                exit(statusCode(array(), 100002));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [agentGoods 获取店铺商品]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function agentGoods() {
        $agentId = I('id');
        $agentId = !empty($agentId) ? $agentId : session('agentId');
        if (IS_POST) {
            $goodsModel = D('Goods');
            $parameter = array(
                'agent_id'          => $agentId,
                'category_id'       => I('category_id', ''),
                'agent_category_id' => I('agent_category_id', ''),
                'goods_name'        => I('goods_name', '', 'urldecode'),
                'keyword'           => I('keyword', '', 'urldecode'),
                'goods_type'        => I('goods_type', '0'),
                'is_on_sale'        => I('is_on_sale', '-1'),
                'is_auth'           => I('is_auth', '-1'),
                'is_recommend'      => I('is_recommend', '-1'),
                'date'              => I('date', ''),
                'sale_sort'         => I('post.sale_sort','-1'),
                'price_sort'        => I('price_sort', '-1'),
                'number_sort'       => I('post.number_sort','-1'),
                'time_sort'         => I('post.time_sort','-1'),
                'sort'              => '1',
                'type'              => I('type', '0'),
                'page'              => $this->page,
                'limitStart'        => $this->limitStart,
                'limit'             => PAGE_LIMIT,
            );
            $returnData = $goodsModel->getGoodsList($parameter);
            $returnData['sale_sort'] = $sale_sort;
            $returnData['list'] = $goodsModel->processData($returnData['list']);
            exit(statusCode($returnData));
        } else {
            $type = I('get.type', '0');
            $return = array(
                'goods_name'    => I('get.goods_name','','urldecode'),
                'keyword'    => I('get.keyword','','urldecode'),
                'goods_name' => I('get.goods_name', '', 'urldecode'),
                'is_on_sale' => I('get.is_on_sale', '-1'),
                'is_auth' => I('get.is_auth', '-1'),
                'agent_category_id' => I('get.agent_category_id', ''),
                'sale_sort'  => I('get.sale_sort'),
                'number_sort' => I('get.number_sort'),
                'time_sort' =>  I('get.time_sort'),
            );
            $categoryList = M('agent_goods_category')->where(array('agent_id'=> $agentId))->select();
            $this->assign('type', $type);
            $this->assign('return', $return);
            $this->assign('categoryList', $categoryList);
            $this->display();
        }
    }

    /**
     * [searchAgentGoods 搜索店铺商品]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function searchAgentGoods() {
        if (IS_POST) {
            $agentId = I('id');
            $keyword = I('keyword', '', 'urldecode');
            $agentId = !empty($agentId) ? $agentId : session('agentId');
            $goods = D('Goods');
            $parameter = array(
                'agent_id'      => $agentId,
                'goods_name'    => $keyword,
                'page'          => $this->page,
                'limitStart'    => $this->limitStart,
                'limit'         => PAGE_LIMIT
            );
            $returnData = $goods->getGoodsList($parameter);
            exit(statusCode($returnData));
        } else {
            $this->display();
        }
    }

    /**
     * [editAgent 编辑店铺]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function editAgent() {
        if (IS_POST) {
            $agentId = session('agentId');
            $agent = D('Agent');
            $data = $agent->create(I('post.'), 2);
            if ( !empty($data) ) {
                // 更新店铺分类关联
                $agentCategoryRelevance = D('AgentCategoryRelevance');
                $categoryids = I('categoryids');
                if ( $agentCategoryRelevance->updateToTable($agentId, $categoryids) === false ) {
                    $result = false;
                    exit(statusCode(array(), 400000, '更新店铺分类失败'));
                }

                // 更新店铺图片 --- not end
                $agentImages = D('AgentImages');


                $data['id'] = $agentId;
                $data['license'] = json_encode(explode(',', $data['license']));
                if ( $agent->save($data) !== false ) {
                    exit(statusCode());
                } else {
                    exit(statusCode(array(), 400000, '编辑失败！'));
                }
            } else {
                exit(statusCode(array(), 400000, $agent->getError()));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [completeAgent 店铺补充资料]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function completeAgent() {
        if ( IS_POST ) {
            $agent = D('Agent');
            $postData = I('post.');
            $addData = $agent->create($postData, 1);
            if ( !empty($addData) ) {
                // $data['license'] = json_encode(explode(',', $data['license']));
                $result = true;
                $agent->startTrans();
                $addData['agent_name'] = $addData['company_name'];
                $agentId = $agent->add($addData);
                if ( $agentId === false ) {
                    $result = false;
                    exit(statusCode(array(), 100002));
                }

                // 添加店铺分类关联
                $agentCategoryRelevance = D('AgentCategoryRelevance');
                if ( $agentCategoryRelevance->insertToTable($agentId, $postData['categoryids']) === false ) {
                    $result = false;
                    exit(statusCode(array(), 100002));
                }

                if ( $result === true ) {
                    $agentQRcode = $this->createAgentQRcode();
                    $agent->commit();
                    exit(statusCode(array('agentQRcode'=> $agentQRcode)));
                } else {
                    $agent->rollback();
                }
            } else {
                exit(statusCode(array(), 400000, $agent->getError()));
            }
        } else {
            $this->display();
        }
    }

     /**
     * [completeSuccess 补充资料成功]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function completeSuccess(){
        $this->display();
    }

    /**
     * [aptitudeData 资质资料]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function aptitudeData() {
        $agentModel = D('Agent');
        $agentId = session('agentId');
        if ( IS_POST ) {
            $field = '`company_name`, `registr_id`, `address`, `manager`, `email`, `license`';
            $returnData['list'] = $agentModel->getAgentInfo($agentId, $field);
            exit(statusCode($returnData)); 
        } else {
            $this->display();
        }
    }

    /**
     * [basicData 基本资料]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function basicData() {
        $agentModel = D('Agent');
        $agentId = session('agentId');
        if ( IS_POST ) {
            // 0：获取信息，1：更新
            $type = I('type', '0');
            $agentCategoryRelevance = D('AgentCategoryRelevance');
            if ( $type == '0' ) {
                $field = '`agent_name`, `address`, `telephone`, `agent_phone`, `logo`, `introduction`, `email`, `longitude`, `latitude`, `agent_keyword`';
                $returnData['list'] = $agentModel->getAgentInfo($agentId, $field);
                $returnData['list']['categoryids'] = trim($agentCategoryRelevance->getCategoryIds($agentId), ',');
                exit(statusCode($returnData));   
            } else {
                $postData = I('post.');
                $saveData = $agentModel->create($postData, 4);
                if ( !empty($saveData) ) {
                    $result = true;
                    $agentModel->startTrans();
                    $saveData['id'] = $agentId;
                    $saveData['introduction'] = '<div class="contentDetail">'.$saveData['introduction'].'</div><style>.contentDetail img{width:100%}</style>';
                    if ( $agentModel->save($saveData) === false ) {
                        $result = false;
                        exit(statusCode(array(), 100002));
                    }

                    // 更新店铺分类关联
                    if ( $agentCategoryRelevance->updateToTable($agentId, $postData['categoryids']) === false ) {
                        $result = false;
                        exit(statusCode(array(), 100002));
                    }

                    if ( $result === true ) {
                        $agentModel->commit();
                        exit(statusCode());
                    } else {
                        $agentModel->rollback();
                    }
                } else {
                    exit(statusCode(array(), 400000, $agentModel->getError()));
                }
            }
        } else {
            $data = $agentModel->getAgentInfo($agentId, 'status');
            $this->assign('status', $data['status']);
            $this->display();
        }
    }

    /**
     * [editBasicData 修改基本资料]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function editBasicData() {
        $agentModel = D('Agent');
        $agentId = session('agentId');
        $data = $agentModel->getAgentInfo($agentId, 'status');
        $this->assign('status', $data['status']);
        $this->display();
    }

    /**
     * [goodsCenter 商家商品管理首页]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function goodsCenter() {
        $this->display();
    }

    /**
     * [agentCenter 商家个人中心]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function agentCenter() {
        if ( IS_POST ) {
            $userId = session('userId');
            $agentId = session('agentId');
            $user = D('User')->getUserInfo($userId, '`headimgurl`, `nickname`, `money`');
            $agent = D('Agent')->getAgentInfo($agentId, '`agent_name`');
            $user = array_merge($user, $agent);
            $returnData = array(
                'list' => $user,
                'agentQRcode' => trim($this->createAgentQRcode(), '.'),
                'agentReceiptQRcode' => trim($this->createAgentReceiptQRcode(), '.')
            );
            exit(statusCode($returnData));
        } else {
            $this->display();
        }
    }

    /**
     * [agentGoodsCategory 分类管理]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function agentGoodsCategory() {
        $this->assign('agentId', session('agentId'));
        $this->display();
    }

    /**
     * [addAgentGoodsCategory 添加店铺商品分类]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c) 2017 Xcrozz (http://www.xcrozz.com)
     */
    public function addAgentGoodsCategory() {
        if ( IS_POST ) {
            $agentGoodsCategory = D('AgentGoodsCategory');
            $data = $agentGoodsCategory->create(I('post.'), 1);
            if ( !empty($data) ) {
                $data['agent_id'] = session('agentId');
                ( $agentGoodsCategory->add($data) !== false ) ? 
                    exit(statusCode()) : 
                    exit(statusCode(array(), 100002));
            } else {
                exit(statusCode(array(), 400000, $agentGoodsCategory->getError()));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [editAgentGoodsCategory 编辑店铺商品分类]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function editAgentGoodsCategory() {
        if ( IS_POST ) {
            $agentGoodsCategory = D('AgentGoodsCategory');
            $data = $agentGoodsCategory->create(I('post.'), 2);
            if ( !empty($data) ) {
                ( $agentGoodsCategory->save($data) !== false ) ? 
                    exit(statusCode()) : 
                    exit(statusCode(array(), 100002));
            } else {
                exit(statusCode(array(), 400000, $agentGoodsCategory->getError()));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [delAgentGoodsCategory 删除店铺商品分类]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function delAgentGoodsCategory() {
        if ( IS_POST ) {
            $id = I('id');
            $goods = M('goods');
            $agentGoodsCategory = D('AgentGoodsCategory');
            if ( strpos($id, ',') !== false ) {
                $idData = explode(',', trim($id, ','));
                foreach ($idData as $value) {
                    if ( $goods->where(array('agent_category_id'=> $value))->count() > 0 ) {
                        exit(statusCode(array(), 400000, L('_PC_GOODS_CANT_DELETED_CLASSIFICATION_')));
                    }
                }
                if ( $agentGoodsCategory->where(array('id'=> array('IN', $idData)))->delete() ) {
                    exit(statusCode());
                } else {
                    exit(statusCode(array(), 100002));
                }
            } else {
                if ( $goods->where(array('agent_category_id'=> $id))->count() > 0 ) {
                    exit(statusCode(array(), 400000, L('_PC_GOODS_CANT_DELETED_CLASSIFICATION_')));
                }
                if ( $agentGoodsCategory->delete($id) ) {
                    exit(statusCode());
                } else {
                    exit(statusCode(array(), 100002));
                }
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [settlementManagement 结算管理]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function settlementManagement() {
        if ( IS_POST ) {
            $userId = session('userId');
            $user = M('user')->find($userId);
            $returnData['list'] = $user;
            exit(statusCode($returnData));
        } else {
            $this->display();
        }
    }

    /**
     * [settlementLog 结算明细]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function settlementLog() {
        $event_type = I('request.event_type', '-1');
        $type = I('request.type', '-1');
        $startTime = I('request.startTime', '');
        $endTime = I('request.endTime', '');
        if ( IS_POST ) {
            $userId = session('userId');
            $agentId = session('agentId');
            $parameter = array(
                'to_id'         => $userId,
                'isMerchant'    => $agentId,
                'agentName'     => empty($agentId) ? '': session('userInfo.nickname'),
                'event_type'    => $event_type,
                'date'          => I('date', ''),
                'type'          => $type,
                'startTime'     => $startTime,
                'endTime'       => $endTime,
                'page'          => $this->page,
                'limitStart'    => $this->limitStart,
                'limit'         => PAGE_LIMIT
            );
            $money = new Money();
            $total = $money->countMoney(array('to_id'=> $userId, 'event_type'=> '0'));
            $returnData = $money->getMoneyLog($parameter);
            $returnData['total'] = $total;
            exit(statusCode($returnData));
        } else {
            if ( empty($startTime) && empty($endTime) ) {
                $startTime = strtotime('-1 years');
                $endTime = strtotime('+1 days');
            }
            $return = array(
                'event_type' => $event_type,
                'type' => $type,
                'startTime' => $startTime,
                'endTime' => $endTime,
            );
            $this->assign('return', $return);
            $this->display();
        }
    }
    
    /**
     * [setting 设置]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function setting() {
        $this->display();
    }

    /**
     * [myPointGoods 我的积分商品]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function myPointGoods() {
        $goods_name = I('request.goods_name', '', 'urldecode');
        $type = I('request.type', '0');
        if ( IS_POST ) {
            $goods = D('Goods');
            $parameter = array(
                'goods_name' => $goods_name,
                'type' => $type,
                'agentId' => session('agentId'),
                'pageType' => I('page_type', '0'),
                'page' => $this->page,
                'limitStart' => $this->limitStart,
                'limit' => PAGE_LIMIT
            );
            $returnData = $goods->getPointGoods($parameter);
            exit(statusCode($returnData));
        } else {
            $return = array(
                'goods_name' => $goods_name,
                'type' => $type,
            );
            $this->assign('return', $return);
            $this->display();
        }
    }

    /**
     * [goodsToPoint 申请积分商品]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function goodsToPoint() {
        if ( IS_POST ) {
            $goodsToPoint = D('GoodsToPoint');
            $data = $goodsToPoint->create(I('post.'), 1);
            if ( !empty($data) ) {
                $where = array(
                    'agent_id' => session('agentId'),
                    'goods_id' => $data['goods_id'],
                );
                if ( $goodsToPoint->where($where)->count() > 0 ) {
                    exit(statusCode(array(), 400000, L('_PC_POINT_APPLY_FOR_INTEFRAL_GOODS_')));
                }

                $addData = array(
                    'agent_id' => session('agentId'),
                    'user_id' => session('userId'),
                );
                $data = array_merge($data, $addData);
                
                // ---edit---
                $data['status'] = '1';
                $goodsModel = D('Goods');
                $goodsModel->where(['id'=>$data['goods_id']])->save(['goods_type'=>'1']);
                $goodsModel->where(['goods_main_id'=>$data['goods_id']])->save(['goods_type'=>'1']);
                // ---edit---
                
                ( $goodsToPoint->add($data) ) ? 
                    exit(statusCode()) : 
                    exit(statusCode(array(), 100002));
            } else {
                exit(statusCode(array(), 400000, $goodsToPoint->getError()));
            }
        } else {
            $this->display();
        }
    }

    /**
     * [createAgentQRcode 生成店铺二维码]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function createAgentQRcode() {
        set_time_limit(0);
        vendor('phpqrcode.phpqrcode');
        $QRcode = new \QRcode();
        $agentId = session('agentId');
        $uploadPath = C('UPLOAD_PATH') . 'AgentQRcode/';
        if ( !file_exists($uploadPath) ) {
            mkdir($uploadPath, 0777);
        }
        $agentQRcode = $uploadPath . 'agentQRcode_'. $agentId .'.png';
        if ( !file_exists($agentQRcode) ) {
            $text   = "mitchell://agentDetail=" . $agentId;
            $level  = 'H';
            $size   = 6;
            $margin = 2;
            $QRcode->png($text, $agentQRcode, $level, $size, $margin); //生成二维码
        }
        return $agentQRcode;
    }

    /**
     * [createAgentReceiptQRcode 生成店铺收款二维码]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function createAgentReceiptQRcode() {
        set_time_limit(0);
        vendor('phpqrcode.phpqrcode');
        $QRcode = new \QRcode();
        $agentId = session('agentId');
        $uploadPath = C('UPLOAD_PATH') . 'AgentReceiptQRcode/';
        if ( !file_exists($uploadPath) ) {
            mkdir($uploadPath, 0777);
        }
        $agentReceiptQRcode = $uploadPath . 'agentReceiptQRcode_'. $agentId .'.png';
        if ( !file_exists($agentReceiptQRcode) ) {
            $text   = "mitchell://receipt=" . $agentId;
            $level  = 'H';
            $size   = 6;
            $margin = 2;
            $QRcode->png($text, $agentReceiptQRcode, $level, $size, $margin); //生成二维码
        }
        return $agentReceiptQRcode;
    }

    /**
     * [changePassword 修改密码]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function changePassword() {
        if ( IS_POST ) {
            $password = I('password');
            $newPassword = I('new_password');
            $userId = session('userId');
            $userModel = D('User');
            $data = $userModel->create(I('post.'), 8);
            if ( !empty($data) ) {
                $currentPassword = $userModel->where(array('id'=> $userId))->getField('password');
                if ( $currentPassword != encrypt($password) ) {
                    exit(statusCode(array(), 400000, L('_PC_USER_PASSWORD_WRONG_')));
                }

                $saveData = array(
                    'id' => $userId,
                    'password' => encrypt($newPassword)
                );
                if ( $userModel->save($saveData) !== false ) {
                    exit(statusCode());
                } else {
                    exit(statusCode(array(), 100002));
                }
            } else {
                exit(statusCode(array(), 400000, $userModel->getError()));
            }
        } else {
            $this->display();
        }
    }

    /**
     * [goodsModel 商品模型]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function goodsModel() {
        if ( IS_POST ) {
            $id = I('post.id', '', 'int');
            if ( empty($id) ) {
                $this->error('参数丢失！');
            }

            $goodsAttrName  = M('goods_attr_name');
            $goodsAttrValue = M('goods_attr_value');

            $attrId        = I('post.attr_id');
            $attrName      = I('post.attr_name');
            // $isFilter      = I('post.is_filter');
            // $isRelation    = I('post.is_relation');

            $attrValue     = I('post.attr_value');
            foreach ($attrValue as $vkey => $vvalue) {
                $goodsAttrValue->where(array('attr_name_id'=>$vkey, 'id'=>array('not in', array_keys($vvalue))))->delete();
                foreach ($vvalue as $kkey => $kvalue) {
                    $goodsAttrValue->where(array('attr_name_id'=>$vkey, 'id'=>$kkey))->data(array('attr_value'=>$kvalue))->save();
                }
            }

            $newAttrValue  = I('post.newValue');
            foreach ($newAttrValue as $newKey => $newValue) {
                foreach ($newValue as $nvvalue) {
                    if ( empty($nvvalue) ) {
                        continue;
                    }

                    $data = array(
                        'attr_name_id'  => $newKey,
                        'attr_value'    => $nvvalue,
                    );
                    $goodsAttrValue->data($data)->add();
                }
            }

            $deleteIds = $goodsAttrName->where(array('category_id'=>$id, 'id'=>array('not in', $attrId)))->getField('id');
            if ( !empty($deleteIds) ) {
                $goodsAttrName->where(array('category_id'=>$id, 'id'=>array('not in', $attrId)))->delete();
                $goodsAttrValue->where(array('attr_name_id'=>array('in', $deleteIds)))->delete();
            }

            foreach ($attrId as $key => $value) {
                $data = array();
                // if ( !empty($isFilter[$value]) ) {
                //     $data['is_filter'] = 1;
                // }

                // if ( !empty($isRelation[$value]) ) {
                    $data['is_relation'] = 1;
                // }

                if ( !empty($attrName[$value]) ) {
                    $data['attr_name'] = $attrName[$value];
                }

                if ( !empty($data) ) {
                    $goodsAttrName->where(array('category_id'=>$id, 'id'=>$value))->data($data)->save();
                }
            }

            $this->success('保存成功');
        } else {
            $id = I('get.id', '', 'int');
            if ( empty($id) ) {
                $this->error('参数丢失！');
            } 

            $goodsAttrName  = M('goods_attr_name');
            $goodsAttrValue = M('goods_attr_value');
            $attrName       = $goodsAttrName->where(array('category_id'=>$id))->select();
            if ( !empty($attrName) ) {
                foreach ($attrName as &$attrValue) {
                    $attrValue['attrValue'] = $goodsAttrValue->where(array('attr_name_id'=>$attrValue['id']))->select();
                }
            }

            $this->assign('attrInfo', $attrName);
            $this->display();
        }
    }

    /**
     * [addModelAttr 添加分类属性]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c) 2017 Xcrozz (http://www.xcrozz.com)
     */
    public function addModelAttr() {
        if ( IS_POST ) {
            $id = I('post.id', '', 'int');
            if (empty($id)) {
                $this->error('参数丢失！');
            }

            $attrName   = I('post.attr_name');
            $attrValue  = I('post.attr_value');
            // $isFilter   = I('post.is_filter');
            // $isRelation = I('post.is_relation');

            $goodsAttrName  = M('goods_attr_name');
            $goodsAttrValue = M('goods_attr_value');

            foreach ($attrName as $namekey => $namevalue) {
                if ( empty($namevalue) ) {
                    continue;
                }

                $data = array(
                    // 'is_filter'   => empty($isFilter[$namekey]) ? 0 : 1,
                    // 'is_relation' => empty($isRelation[$namekey]) ? 0 : 1,
                    'is_relation' => 1,
                    'category_id' => $id,
                    'attr_name'   => $namevalue,
                );

                $attrNameId = $goodsAttrName->data($data)->add();
                if ( !empty($attrNameId) ) {
                    $dataList = array();
                    if ( !empty($attrValue[$namekey]) ) {
                        $attrValueRow = explode("\n", $attrValue[$namekey]);
                        foreach ($attrValueRow as $key => $value) {
                            $dataList[] = array(
                                'attr_name_id' => $attrNameId,
                                'attr_value'   => $value,
                            );
                        }
                        $goodsAttrValue->addAll($dataList);
                    }
                }
            }

            $this->success('添加成功', U('Agent/goodsModel', array('id'=>$id)));
        } else {
            $this->display();
        }
    }

    /**
     * [location 定位]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function location() {
        $this->display();
    }

    /**
     * [photoUpload 上传图片]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function photoUpload() {
        // 图片保存路径
        fileUpload('Agent/', function($e) {
            echo json_encode(array('error'=>0, 'url'=>trim($e['filePath'], '.')));
        });
    }
}