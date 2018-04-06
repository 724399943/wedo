<?php
namespace Shop\Controller;
use Think\Controller;

Class IndexController extends BaseController{
    private $showNumber = 13;
    public function index() {
        cookie('think_language', 'en-us', 3600);
        header('Location:' . U('Login/login'));
    }

    /**
     * [recommendGoods 首页商品推荐]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function recommendGoods() {
        if ( IS_POST ) {
            $goodsModel = D('Goods');
            $parameter = array(
                'is_recommend'  => '1',
                'is_on_sale'    => '1',
                'sort'          => '1',
                'page'          => $this->page,
                'limitStart'    => $this->limitStart,
                'limit'         => PAGE_LIMIT
            );
            $returnData = $goodsModel->getGoodsList($parameter);
            // 当没有竞价的时候，默认为按商品、商家最新发布的时间展示
            if ( empty($returnData['list']) ) {
                $parameter = array(
                    'is_on_sale'    => '1',
                    'sort'          => '0',
                    'page'          => $this->page,
                    'limitStart'    => $this->limitStart,
                    'limit'         => PAGE_LIMIT
                );
                $returnData = $goodsModel->getGoodsList($parameter);
            } else {
                $count = count($returnData['list']);
                $limit = $this->showNumber - $count;
                if ( $limit > 0 ) {
                    $goods = array_column($returnData['list'], 'id');
                    $where['id'] = array('NOTIN', $goods);
                    $parameter = array(
                        'is_on_sale'    => '1',
                        'sort'          => '0',
                        'page'          => $this->page,
                        'limitStart'    => 0,
                        'limit'         => $limit,
                        'where'         => $where,
                    );
                    $list = $goodsModel->getGoodsList($parameter);
                    $returnData['list'] = array_merge($returnData['list'], $list['list']);
                }
            }
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [recommendAgent 首页推荐店铺]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function recommendAgent() {
        if ( IS_POST ) {
            $agent = D('Agent');
            $parameter = array(
                'is_recommend'  => '1',
                'is_hot'        => I('is_hot', '-1'),
                'is_nearby'     => I('is_nearby', '-1'),
                'distance_sort' => '1',
                'longitude'     => I('longitude', '113.37763'),
                'latitude'      => I('latitude', '23.13275'),
                'meter'         => I('meter', 2147483647),
                'limitStart'    => $this->limitStart,
                'limit'         => PAGE_LIMIT,
            );
            $returnData['list'] = $agent->getAgentList($parameter);
            // 当没有竞价的时候，默认为按商品、商家最新发布的时间展示
            if ( empty($returnData['list']) ) {
                $parameter = array(
                    'is_recommend'  => '-1',
                    'is_hot'        => I('is_hot', '-1'),
                    'is_nearby'     => I('is_nearby', '-1'),
                    // 'distance_sort' => '1',
                    'id_sort'       => '1',
                    'longitude'     => I('longitude', '113.37763'),
                    'latitude'      => I('latitude', '23.13275'),
                    'meter'         => I('meter', 2147483647),
                    'limitStart'    => $this->limitStart,
                    'limit'         => PAGE_LIMIT,
                );
                $returnData['list'] = $agent->getAgentList($parameter);
            } else {
                $count = count($returnData['list']);
                $limit = $this->showNumber - $count;
                if ( $limit > 0 ) {
                    $parameter = array(
                        'is_recommend'  => '-1',
                        'is_hot'        => I('is_hot', '-1'),
                        'is_nearby'     => I('is_nearby', '-1'),
                        // 'distance_sort' => '1',
                        'id_sort'       => '1',
                        'longitude'     => I('longitude', '113.37763'),
                        'latitude'      => I('latitude', '23.13275'),
                        'meter'         => I('meter', 2147483647),
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
     * [goodsCategory 首页热门分类]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function goodsCategory() {
        if ( IS_POST ) {
            // $indexGoodsCategory = S('indexGoodsCategory');
            // if ( empty($indexGoodsCategory) ) {
                $indexGoodsCategory = M('goods_category')->field('`id`, `category_name`, `pid`, `category_path`, `app_icon`')->order('`sort` ASC')->select();
                // $indexGoodsCategory = $this->recursiveCategory(0, $indexGoodsCategory);
                // S('indexGoodsCategory', $indexGoodsCategory, 3600);
            // }

            exit(statusCode(array('list'=> $indexGoodsCategory)));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [favorableGoods 首页优惠商品]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function favorableGoods() {
        if ( IS_POST ) {
            $goodsModel = D('Goods');
            $parameter = array(
                'is_favorable'  => '1',
                'is_on_sale'    => '1',
                'page'          => $this->page,
                'sort'          => '1',
                'limitStart'    => $this->limitStart,
                'limit'         => PAGE_LIMIT
            );
            $returnData = $goodsModel->getGoodsList($parameter);
            // 当没有竞价的时候，默认为按商品、商家最新发布的时间展示
            if ( empty($returnData['list']) ) {
                $parameter = array(
                    'is_on_sale'    => '1',
                    'sort'          => '0',
                    'page'          => $this->page,
                    'limitStart'    => $this->limitStart,
                    'limit'         => PAGE_LIMIT
                );
                $returnData = $goodsModel->getGoodsList($parameter);
            } else {
                $count = count($returnData['list']);
                $limit = $this->showNumber - $count;
                $goods = array_column($returnData['list'], 'id');
                $where['id'] = array('NOTIN', $goods);
                if ( $limit > 0 ) {
                    $parameter = array(
                        'is_on_sale'    => '1',
                        'sort'          => '0',
                        'page'          => $this->page,
                        'limitStart'    => 0,
                        'limit'         => $limit,
                        'where'         => $where,
                    );
                    $list = $goodsModel->getGoodsList($parameter);
                    $returnData['list'] = array_merge($returnData['list'], $list['list']);
                }
            }
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        } 
    }

    /**
     * [indexBanner 首页banner图]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function indexBanner() {
        if ( IS_POST ) {
            $adModel = D('Ad');
            $returnData['list'] = $adModel->getAdList();
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [bannerDetail banner详情]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function bannerDetail() {
        if ( IS_POST ) {
            $id = I('id');
            $adModel = D('Ad');
            $returnData['list'] = $adModel->getAdDetail($id);
            exit(statusCode($returnData));
        } else {
            $this->display();       
        }
    }
}