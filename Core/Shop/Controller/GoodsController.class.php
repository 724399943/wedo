<?php
namespace Shop\Controller;
use Think\Controller;

class GoodsController extends BaseController {
    /**
     * [goodsList 商品列表]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2015 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function goodsList() {
        if (IS_POST) {
            $goodsModel = D('Goods');
            $parameter = array(
                'category_id'   => I('category_id'),
                'is_auth'       => I('is_auth', '-1'),
                'comment_sort'  => I('comment_sort', '-1'),
                'sale_sort'     => I('sale_sort', '-1'),
                'price_sort'    => I('price_sort', '-1'),
                'is_on_sale'    => '1',
                'sort'          => '1',
                'page'          => $this->page,
                'limitStart'    => $this->limitStart,
                'limit'         => PAGE_LIMIT
            );
            $returnData = $goodsModel->getGoodsList($parameter);
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [recommendGoods 推荐商品]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function recommendGoods() {
        if (IS_POST) {
            $goodsModel = D('Goods');
            $parameter = array(
                'is_recommend'  => '1',
                'is_on_sale'    => '1',
                'is_auth'       => I('is_auth', '-1'),
                'comment_sort'  => I('comment_sort', '-1'),
                'sale_sort'     => I('sale_sort', '-1'),
                'price_sort'    => I('price_sort', '-1'),
                'sort'          => '1',
                'page'          => $this->page,
                'limitStart'    => $this->limitStart,
                'limit'         => PAGE_LIMIT
            );
            $returnData = $goodsModel->getGoodsList($parameter);
            // 当没有竞价的时候，默认为按商品、商家最新发布的时间展示
            if ( empty($returnData['list']) ) {
                $parameter = array(
                    'is_recommend'  => '-1',
                    'is_on_sale'    => '1',
                    'is_auth'       => I('is_auth', '-1'),
                    'comment_sort'  => I('comment_sort', '-1'),
                    'sale_sort'     => I('sale_sort', '-1'),
                    'price_sort'    => I('price_sort', '-1'),
                    'sort'          => '0',
                    'page'          => $this->page,
                    'limitStart'    => $this->limitStart,
                    'limit'         => PAGE_LIMIT
                );
                $returnData = $goodsModel->getGoodsList($parameter);
            } else {
                // $count = count($returnData['list']);
                // $limit = PAGE_LIMIT - $count;
                // $goods = array_column($returnData['list'], 'id');
                // $where['id'] = array('NOTIN', $goods);
                // if ( $limit > 0 ) {
                //     $parameter = array(
                //         'is_recommend'  => I('is_recommend', '-1'),
                //         'is_on_sale'    => '1',
                //         'is_auth'       => I('is_auth', '-1'),
                //         'comment_sort'  => I('comment_sort', '-1'),
                //         'sale_sort'     => I('sale_sort', '-1'),
                //         'price_sort'    => I('price_sort', '-1'),
                //         'sort'          => '1',
                //         'page'          => $this->page,
                //         'limitStart'    => 0,
                //         'limit'         => $limit,
                //         'where'         => $where,
                //     );
                //     $list = $goodsModel->getGoodsList($parameter);
                //     $returnData['list'] = array_merge($returnData['list'], $list['list']);
                // }
            }
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        }   
    }

    /**
     * [favorableGoods 优惠商品]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function favorableGoods() {
        if (IS_POST) {
            $goodsModel = D('Goods');
            $parameter = array(
                'is_favorable'  => '1',
                'is_on_sale'    => '1',
                'is_auth'       => I('is_auth', '-1'),
                'comment_sort'  => I('comment_sort', '-1'),
                'sale_sort'     => I('sale_sort', '-1'),
                'price_sort'    => I('price_sort', '-1'),
                'sort'          => '1',
                'page'          => $this->page,
                'limitStart'    => $this->limitStart,
                'limit'         => PAGE_LIMIT
            );
            $returnData = $goodsModel->getGoodsList($parameter);
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        }   
    }

    /**
     * [searchGoods 搜索商品]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function searchGoods() {
        if (IS_POST) {
            $goodsModel = D('Goods');
            $parameter = array(
                'goods_name'    => I('keyword', '', 'urldecode'),
                'is_on_sale'    => '1',
                'page'          => $this->page,
                'limitStart'    => $this->limitStart,
                'limit'         => PAGE_LIMIT
            );
            $returnData = $goodsModel->getGoodsList($parameter);
            if ( empty($returnData['list']) ) {
                $parameter = array(
                    'keyword'       => $parameter['goods_name'],
                    'is_on_sale'    => '1',
                    'page'          => $this->page,
                    'limitStart'    => $this->limitStart,
                    'limit'         => PAGE_LIMIT
                );
                $returnData = $goodsModel->getGoodsList($parameter);
            }
            $returnData['list'] = $goodsModel->processLocation($returnData['list']);
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [addGoods 发布商品]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c) 2016 Xcrozz (http://www.xcrozz.com)
     */
    public function addGoods() {
        $agentId = session('agentId');
        if ( IS_POST ) {
            $userId = session('userId');
            $agentModel = M('agent');
            $goodsModel = D('Goods');
            $data = $goodsModel->create(I('post.'), 1);
            $SKUattr = I('post.SKUattr', '');
            $SKUprice = I('post.SKUprice', '');
            $SKUnumber = I('post.SKUnumber','');
            if (!empty($data)) {
                $data['goods_name'] = htmlspecialchars_decode($data['goods_name']);
                $data['agent_id'] = $agentId;
                $data['supplier_id'] = $agentId;
                $data['is_on_sale'] = '1';
                // 保存商品图片
                $images = I('images', '', 'urldecode');
                if (!empty($images)) {
                    $goodsImages = $goodsModel->addGoodsImages($images);
                    $data['goods_image'] = $goodsImages['goods_image'];
                    $data['goods_images_id'] = $goodsImages['goods_images_id'];
                } else {
                    exit(statusCode(array(), 400000, L('_PC_GOODS_UPLOAD_PRODUCT_PICTURE_')));
                }
                // 添加商品详情
                $description = I('description', '', 'htmlspecialchars_decode');
                $extensionId = $goodsModel->addGoodsExtension($description);
                $data['goods_ext_id'] = $extensionId;
                // 处理SKU
                if ($SKUattr) {
                    $dataArr = array();
                    foreach ($SKUattr as $key => $value) {
                        $addData = array();
                        $value = trim($value,',');
                        $data['attr_list'] = ','. $value .',';
                        $relevanceAttr[] = str_replace(',', '-', $value);
                        $addData['goods_price'] = $SKUprice[$key];
                        $addData['goods_number'] = $SKUnumber[$key];
                        $addData = array_merge($data, $addData);
                        $dataArr[] = $addData;
                    }
                    $dataCount = count($dataArr);
                    $goodsId = $goodsModel->addAll($dataArr);
                    if ( $goodsId ) {
                        for( $i=0; $i<$dataCount; $i++ ){
                            $goodsIdArr[] = $goodsId + $i;
                        }
                        foreach ($goodsIdArr as $key => $value) {
                            if ($goodsIdArr[$key] != $goodsId) {
                                $goodsModel->where(array('id'=> $goodsIdArr[$key]))->save(array('goods_main_id'=> $goodsId));
                            }
                        }
                        // 添加商品关联
                        $goodsModel->addGoodsRelevance($goodsIdArr, $relevanceAttr);

                        // 增加店铺商品数量
                        $agentModel->where(array('id'=> $agentId))->setInc('goods_number');

                        // 推送商品信息
                        // $messageModel = M('message');
                        // $userCollectModel = M('user_collect');
                        // $where = array(
                        //     'agent_id'=> $agentId,
                        //     'type' => '1'
                        // );
                        // $fansData = $userCollectModel->where($where)->select();
                        // $condition = ',' . implode(',', array_column($fansData, 'user_id')) . ',';
                        // $addData = array(
                        //     'agent_id' => $agentId,
                        //     'goods_id' => $goodsId,
                        //     'type' => '0',
                        //     'message_type' => '3',
                        //     'condition' => $condition,
                        //     'add_time' => time()
                        // );
                        // $messageModel->add($addData);
                        
                        exit(statusCode());
                    } else {
                        exit(statusCode(array(), 100002));
                    }
                } else {
                    // 增加店铺商品数量
                    $agentModel->where(array('id'=> $agentId))->setInc('goods_number');
                    ( $goodsModel->add($data) ) ?
                        exit(statusCode()) :
                        exit(statusCode(array(), 100002));
                }
            } else {
                exit(statusCode(array(), 400000, $goodsModel->getError()));
            }
        } else {
            // 平台分类
            $categoryList = M('goods_category')->field('`id`, `category_name`')->order('`sort` DESC')->select();
            // 店内分类
            $agentCategoryList = M('agent_goods_category')->where(array('agent_id'=> $agentId))->field('`id`, `category_name`')->select();
            $this->assign('categoryList', $categoryList);
            $this->assign('agentCategoryList', $agentCategoryList);
            $this->display();
        }
    }

    /**
     * [editGoods 编辑商品]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function editGoods() {
        $agentId = session('agentId');
        if ( IS_POST ) {
            $userId = session('userId');
            $goodsModel = D('Goods');
            $data = $goodsModel->create(I('post.'), 2);
            $SKUattr = I('post.SKUattr', '');
            $SKUprice = I('post.SKUprice', '');
            $SKUnumber = I('post.SKUnumber','');
            if (!empty($data)) {
                $goodsId = $data['id'];
                $data['agent_id'] = $agentId;
                $data['supplier_id'] = $agentId;
                $data['goods_name'] = htmlspecialchars_decode($data['goods_name']);
                $data['introduction'] = htmlspecialchars_decode($data['introduction']);
                // 获取商品信息
                $goodsData = $goodsModel->find($goodsId);
                
                $images = I('images', '', 'urldecode');
                $description = I('description', '', 'htmlspecialchars_decode');
                // $checkGoodsImages = $goodsModel->checkGoodsImages($images, $goodsData);
                // $goodsForEdit = false;
                // if ( $goodsData['edit'] != 'true' && !empty(array_diff($data, $goodsData)) ) {
                //     $goodsForEdit = true;
                // } elseif ( $goodsData['edit'] != 'true' && $checkGoodsImages === false ) {
                //     $goodsForEdit = true;
                // }
                // 是否需要支付
                if ( $goodsData['edit'] != 'true' ) {
                    ( new \Shop\Controller\GoodsForEditController )->goodsForEdit();
                }

                $data['edit'] = 'false';
                $data['relevance_id'] = $goodsData['relevance_id'];
                // 保存商品图片
                if (!empty($images)) {
                    $goodsImages = $goodsModel->editGoodsImages($images, $goodsData);
                    $data['goods_image'] = $goodsImages['goods_image'];
                    $data['goods_images_id'] = $goodsImages['goods_images_id'];
                } else {
                    exit(statusCode(array(), 400000, L('_PC_GOODS_UPLOAD_PRODUCT_PICTURE_')));
                }
                // 添加商品详情
                $extensionId = $goodsModel->editGoodsExtension($description, $goodsData);
                $data['goods_ext_id'] = $extensionId;
                // 处理SKU
                if ($SKUattr) {
                    $skuGoods = $goodsModel->where(array('goods_main_id'=> $goodsId))->select();
                    //往第一键插入数组
                    array_unshift($skuGoods, $goodsData);
                    if ( !empty($skuGoods) ) {
                        $goodsArr = array();
                        $skuCount = count($SKUattr);
                        $goodsModel->where(array('goods_main_id'=>$goodsId))->save(array('is_delete'=>'1'));
                        $goodsModel->save(array('id'=> $goodsId, 'is_delete'=> '1'));
                        $goodsMainId = $goodsId;
                        foreach ($SKUattr as $key => $value) {
                            $isRecover = false;
                            $thisGoods = '';
                            $value = trim($value,',');
                            $data['attr_list'] = ','. $value .',';
                            $relevanceAttr[] = str_replace(',', '-', $value);
                            foreach ($skuGoods as $v) {
                                if( strpos($v['attr_list'], $data['attr_list']) !== false ) {
                                    $isRecover = true;
                                    $thisGoods = $v['id'];
                                    break;
                                }
                            }
                            $editData = array();
                            $editData['goods_price'] = $SKUprice[$key];
                            $editData['goods_number'] = $SKUnumber[$key];
                            $editData = array_merge($data, $editData);
                            // 是否恢复已删除数据
                            if ($isRecover === false) {
                                unset($editData['id']);
                                $editData['add_time'] = time();
                                $goodsArr[$key] = $goodsModel->add($editData);
                            } else {
                                $goodsArr[$key] = $thisGoods;

                                $editData['id'] = $thisGoods;
                                $editData['is_delete'] = 0;
                                $goodsModel->save($editData);
                            }
                            $goodsMainId = ( strpos($data['attr_list'], $goodsData['attr_list']) !== false) ? $goodsMainId : 0; 
                        }
                        $goodsCount  = count($goodsArr);
                        for( $i=0; $i<$goodsCount; $i++ ) {
                            $saveData['id'] = $goodsArr[$i];
                            if ( !empty($goodsMainId) ) {
                                $saveData['goods_main_id'] = ( $saveData['id'] == $goodsMainId ) ? 0 : $goodsMainId;
                            } else {
                                $saveData['goods_main_id'] = ( $i == 0 ) ? 0 : $goodsArr[0];
                            }
                            $goodsModel->save($saveData);
                        }
                        // 编辑商品关联
                        $goodsModel->editGoodsRelevance($goodsArr, $relevanceAttr, $goodsData);
                        exit(statusCode());
                    }
                } else {
                    $data['is_delete'] = 1;
                    $data['edit'] = 'false';
                    $goodsModel->where(array('goods_main_id'=> $goodsId))->save(array('is_delete'=>'1'));
                    ( $goodsModel->save($data) !== false ) ?
                        exit(statusCode()) :
                        exit(statusCode(array(), 100002));
                }
            } else {
                exit(statusCode(array(), 400000, $goodsModel->getError()));
            }
        } else {
            if ( $this->wechatAgent === false ) {
                $goodsId = I('id');
                $data = D('Goods')->getGoodsInfo($goodsId);
                // 平台分类
                $categoryList = M('goods_category')->field('`id`, `category_name`')->order('`sort` DESC')->select();
                // 店内分类
                $agentCategoryList = M('agent_goods_category')->where(array('agent_id'=> $agentId))->field('`id`, `category_name`')->select();
                $this->assign('data', $data);
                $this->assign('attrData', $data['attrData']);
                $this->assign('goodsDesc', $data['goodsDesc']);
                $this->assign('relevanceData', $data['relevanceData']);
                $this->assign('goodsInfo', $data['goodsInfo']);
                $this->assign('goodsImages', $data['goodsImages']);
                $this->assign('categoryList', $categoryList);
                $this->assign('agentCategoryList', $agentCategoryList);
            }
            // dump($data);die;
            $this->display();
        }
    }

    /**
     * [setOnSale 设置商品上下架]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c) 2016 Xcrozz (http://www.xcrozz.com)
     */
    public function setOnSale() {
        if (IS_POST) {
            $goodsId = I('goods_id');
            $agentId = session('agentId');
            $is_on_sale = I('is_on_sale', '1');
            $goods = D('Goods');
            if ( $goods->setOnSale($goodsId, $agentId, $is_on_sale) !== false ) {
                exit(statusCode());
            } else {
                exit(statusCode(array(), 100002));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [goodsExtension 商品详情内容]
     * @author wulong <1191540273@qq.com>
     * @copyright Copyright (c)           2015 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function goodsExtension(){
        $goodsId = I('request.id','');
        $goods = D('Goods');
        $goodsDesc = $goods->getGoodsExtension($goodsId);
        if (IS_POST) {
            $goodsDesc['goods_desc'] = htmlspecialchars_decode($goodsDesc['goods_desc']);
            exit(statusCode(array('goodsDesc'=>$goodsDesc)));
        } else {
            $this->assign('goodsDesc', $goodsDesc);
            $this->display();
        }
    }

    /**
     * [deleteGoods 删除商品]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function deleteGoods() 
    {
        if (IS_POST) {
            $goods = D('Goods');
            $goodsId = I('goods_id');
            $agentId = session('agentId');
            if ( $goods->deleteGoods($goodsId, $agentId) !== false ) {
                exit(statusCode());
            } else {
                exit(statusCode(array(), 100002));
            }
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [goodsDetail 商品详情]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function goodsDetail() 
    {
        if (IS_POST) {
            $goods      = D('Goods');
            $parameter  = array(
                'goodsId'   => I('goods_id'),
                'userId'    => session('userId'),
                'longitude' => I('longitude', '113.37763'),
                'latitude'  => I('latitude', '23.13275'),
                'isTemp'    => session('is_temp')
            );
            $returnData = $goods->getGoodsDetail($parameter);
            exit(statusCode($returnData));
        } else {
            $this->display();
        }
    }

    /**
     * [goodsDetail 商品详情]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function agentGoodsDetail() 
    {
        if (IS_POST) {
            $goods      = D('Goods');
            $parameter  = array(
                'goodsId'   => I('goods_id'),
                'userId'    => session('userId'),
                'longitude' => I('longitude', '113.37763'),
                'latitude'  => I('latitude', '23.13275'),
                'isTemp'    => session('is_temp')
            );
            $returnData = $goods->getGoodsDetail($parameter);
            exit(statusCode($returnData));
        } else {
            $this->display();
        }
    }

    /**
     * [goodsInfo 获取商品信息]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function goodsInfo() 
    {
        $goodsId = I('id');
        $goodsModel = D('Goods');
        $list = $goodsModel->getGoodsInfo($goodsId);
        if ( !empty($list) ) {
            exit(statusCode(array('list'=> $list)));
        } else {
            exit(statusCode(array(), 100002));
        }
    }

    /**
     * [getSkuAttr 获取sku]
     * @author wulong <1191540273@qq.com>
     * @copyright Copyright (c)           2015 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function getSkuAttr() 
    {
        $goodsAttrName  = M('goods_attr_name');
        $goodsAttrValue = M('goods_attr_value');
        $goodsRelevance = M('goods_relevance');
        $goodsModel = D('Goods');
        $userId = session('user_id');
        if (IS_POST) {
            $relevanceId = I('request.relevance_id','');
            $categoryId  = I('request.category_id','');

            //找到关联商品属性表
            $relevanceData = $goodsRelevance->find($relevanceId);
            $goodsArr = explode(',', $relevanceData['relevance_id']);
            $attrArr  = explode(',', $relevanceData['relevance_attr']);

            $attrArray = ',';
            foreach ($attrArr as $key => $value) {
                $attrArray .= str_replace('-', ',', $value).',';
            }
            $attrArray = explode(',', trim($attrArray,','));
            $attrArray = ','.implode(',',array_unique($attrArray)).',';
            
            //获取相关分类的商品model
            $attrNameInfo = $goodsModel->getCategoryAttr($categoryId, $attrArray);
            sort($attrNameInfo);
            $attrOn = array_combine($goodsArr, $attrArr);

            //获取商品库存
            $time = time();
            $goodsOn = array(); 
            $shopping = M('goods_shopping_cart');
            foreach ($attrOn as $key => &$value) {
                $goodsDetail = $goodsModel->where(array('id'=>$key))->field('id,goods_number,goods_price,goods_image,goods_name,attr_list,favorable_price,is_favorable,favorable_end_time,favorable_start_time')->find();
                $goodsDetail['is_favorable'] = ( $goodsDetail['favorable_start_time'] <= $time && $goodsDetail['favorable_end_time'] >= $time ) ? '1' : '0';

                $shopNum = $shopping->where(array('user_id'=>$userId, 'goods_id'=>$key))->getField('SUM(goods_number)');
                $surplus = $goodsDetail['goods_number']- $shopNum;
                
                $goodsDetail['surplus'] = $surplus;
                $skuId['skuid'] = $value;
                $skuId['skuArr']= explode('-',$value);
                $goodsOn[] = array_merge($goodsDetail,$skuId);
            }

            $result = array(
                'attrNameInfo'  => $attrNameInfo,
                // 'relevanceData' => $relevanceData,
                'goodsOn'       => $goodsOn,
            );
            exit(statusCode($result));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [categoryAttr 获取分类属性]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function categoryAttr($category_id) {
        $categoryId = empty($category_id) ? I('post.category_id', '') : $category_id;
        $goodsModel = D('Goods');
        $attrData = $goodsModel->getCategoryAttr($categoryId);
        if ( IS_POST ) {
            exit(statusCode(array('list'=> $attrData)));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [addBrowsingHistory 添加商品浏览历史]
     * @author wulong <1191540273@qq.com>
     * @copyright Copyright (c)           2015 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function addBrowsingHistory(){
        if(IS_POST){
            $goodsId = I('goods_id');
            $userId = session('userId');
            $isTemp = session('is_temp');
            // $cid = M('goods')->where(array('id'=>$gid))->getField('category_id');
            if( empty($goodsId) ){
                exit(statusCode(array(), 100002));
            }
            exit(statusCode());
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [photoUpload 上传商品图片]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function photoUpload() {
        // 图片保存路径
        fileUpload('Goods/', function($e) {
            echo json_encode(array('error'=>0, 'url'=>trim($e['filePath'], '.')));
        });
    }
}