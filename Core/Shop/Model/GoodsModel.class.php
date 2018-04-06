<?php
namespace Shop\Model;
use Think\Model;

class GoodsModel extends Model {
	private $dbPrefix;
	public function __construct() {
		parent::__construct();
		$this->dbPrefix = C('DB_PREFIX');
	}
	/**
	 * [_validate 自动验证]
     * @author kofu <[418382595@qq.com]>
	 */
	protected $_validate = array(
		// 新增
		array('goods_name', 'require', '{%module_goods_product_name}', 1, 'regex', 1),
		array('introduction', 'require', '{%module_goods_product_subtitle}', 1, 'regex', 1),
		array('category_id', 'require', '{%module_goods_select_platform_category}', 1, 'regex', 1),
		array('agent_category_id', 'require', '{%module_goods_select_merchant_category}', 1, 'regex', 1),
		array('express_type', 'require', '{%module_goods_select_delivery_method}', 1, 'regex', 1),
		array('keyword', 'require', '{%module_goods_product_keyword}', 1, 'regex', 1),
		array('keyword', checkKeywordSymbol, '{%module_goods_use_product_keyword}', 1, 'callback', 1),
		array('keyword', checkKeywordLength, '{%module_goods_max_product_keyword}', 1, 'callback', 1),
		// array('images', 'require', '请选择商品图片', 1, 'regex', 1),

		// 修改
		array('goods_name', 'require', '{%module_goods_product_name}', 1, 'regex', 2),
		array('introduction', 'require', '{%module_goods_product_subtitle}', 1, 'regex', 2),
		array('category_id', 'require', '{%module_goods_select_platform_category}', 1, 'regex', 2),
		array('agent_category_id', 'require', '{%module_goods_select_merchant_category}', 1, 'regex', 2),
		array('express_type', 'require', '{%module_goods_select_delivery_method}', 1, 'regex', 2),
		array('keyword', 'require', '{%module_goods_product_keyword}', 1, 'regex', 2),
		array('keyword', checkKeywordSymbol, '{%module_goods_use_product_keyword}', 1, 'callback', 2),
		array('keyword', checkKeywordLength, '{%module_goods_max_product_keyword}', 1, 'callback', 2),
		// array('images', 'require', '请选择商品图片', 1, 'regex', 2),
	);

	protected $_auto = array(
		array('add_time', 'time', 1, 'function'),
	);

	/**
	 * [checkKeywordSymbol 检测关键词符号]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)    2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $value [description]
	 * @return    [type]               [description]
	 */
	protected function checkKeywordSymbol($value) {
		if ( strpos($value, ';') !== false ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * [checkKeywordLength 检测关键词长度]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)    2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $value [description]
	 * @return    [type]               [description]
	 */
	protected function checkKeywordLength($value) {
		$length = count(explode(';', $value));
		if ( $length > 3 ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * [getGoodsDetail 获取商品详情]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)      2016          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $goodsId [description]
	 * @param     [type]        $userId  [description]
	 * @return    [type]                 [description]
	 */
	public function getGoodsDetail($parameter) {
		$goodsId = $parameter['goodsId'];
		$userId = $parameter['userId'];
		$longitude = $parameter['longitude'];
		$latitude = $parameter['latitude'];
		// $isTemp = $parameter['isTemp'];
		$meter = C('DISTANCE_METER');

		// 添加浏览记录
        // if ( $isTemp == '0' && $this->isBrowsing($userId, $goodsId) !== false ) {
        // if ( $this->isBrowsing($userId, $goodsId) !== false ) {
    		$this->where(array('id'=> $goodsId))->setInc('browsing_number');
    		// $this->addBrowsingHistory($goodsId, $userId, $isTemp);
        // }

		$goodsInfo = $this->field('`id`, `category_id`, `agent_category_id`, `agent_id`, `goods_name`, `goods_image`, `introduction`, `goods_price`, `goods_images_id`, `browsing_number`, `sale_number`, `comment_number`, `attr_list`, `is_auth`, `is_on_sale`, `relevance_id`, `goods_ext_id`, `express_type`, `favorable_price`, `is_favorable`, `favorable_end_time`, `favorable_start_time`')->find($goodsId);
        if ( !empty($goodsInfo) ) {
        	// 店铺信息
        	$agentModel = D('Agent');
        	$distance = $agentModel->calc($longitude, $latitude, '`longitude`', '`latitude`');
        	$agentInfo = $agentModel->field('`id`, `user_id`, `logo`, `agent_phone`, `agent_name`, `star`, '. $distance .'AS `distance`')->where("{$distance} < {$meter}")->find($goodsInfo['agent_id']);
        	$agentInfo['distance']	= round($agentInfo['distance'] / 1000, 1);

        	// 商品banner图
            $goodsImages = $this->getGoodsImages($goodsInfo['goods_images_id']);

            // 商品详情内容
            $webSite = trim(C('webSite'), '/');
            $goodsDesc = $this->getGoodsExtension($goodsInfo['goods_ext_id']);
            $goodsDesc = str_replace('/Static/Uploads/', $webSite . '/Static/Uploads/', $goodsDesc);

            // 是否收藏过该商品
            $where = array(
            	'user_id'	=> $userId,
            	'goods_id'	=> $goodsId,
            	'type'		=> '0'
            );
            $collect_id = M('user_collect')->where($where)->getField('`id` AS `collect_id`');

            //获取商品属性sku
			$attr_list = trim($goodsInfo['attr_list'], ',');
			if ( str_replace(',', '', $goodsInfo['attr_list']) != '' ) {
				$sql = "SELECT `gan`.`attr_name`, `gav`.`attr_value`
						FROM `{$this->dbPrefix}goods_attr_value` AS `gav`
						LEFT JOIN `{$this->dbPrefix}goods_attr_name` AS `gan` ON `gav`.`attr_name_id` = `gan`.`id`
						WHERE `gav`.`id` IN ({$attr_list}) AND `gan`.`is_relation` = '1'";
				$attrList = M()->query($sql);
			}

            // 商品评价
            $goodsComment = D('GoodsComment');
            // $commentCount = $goodsComment->where(array('goods_id'=> $goodsId))->count();
            $commentCount = $goodsInfo['comment_number'];
            $commentList = $goodsComment->getComment(array('goods_id'=> $goodsId, 'limit'=> '1'));
            $commentList = $commentList['list'];

            // 类似商品
            $similarGoods = $this->getGoodsList(array('category_id'=> $category_id));
            $similarGoods = $similarGoods['list'];

            $time = time();
            $goodsInfo['is_favorable'] = ( $goodsInfo['favorable_start_time'] <= $time && $goodsInfo['favorable_end_time'] >= $time ) ? '1' : '0';
        }
        $data['agentInfo'] 		= !empty($agentInfo) ? $agentInfo : NULL;
        $data['goodsInfo'] 		= !empty($goodsInfo) ? $goodsInfo : NULL;
        $data['goodsImages'] 	= !empty($goodsImages) ? $goodsImages : NULL;
        $data['goodsDesc'] 		= !empty($goodsDesc) ? $goodsDesc : NULL;
        $data['commentCount'] 	= !empty($commentCount) ? $commentCount : '0';
        $data['commentList'] 	= !empty($commentList) ? $commentList : NULL;
        $data['similarGoods'] 	= !empty($similarGoods) ? $similarGoods : NULL;
        $data['is_collect'] 	= !empty($collect_id) ? '1' : '0';
        $data['collect_id'] 	= !empty($collect_id) ? $collect_id : '0';
        $data['attrList'] 		= !empty($attrList) ? $attrList : NULL;
        return $data;
	}

	/**
	 * [addBrowsingHistory 添加商品浏览历史]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)      2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $goodsId [description]
	 * @param     [type]        $userId  [description]
	 * @param     [type]        $isTemp  [description]
	 */
	public function addBrowsingHistory($goodsId, $userId, $isTemp) {
        // $cid = M('goods')->where(array('id'=>$gid))->getField('category_id');
        $browsingHistoryModel = M('browsing_history');
        $where = array(
            'user_id'   => $userId,
            'is_temp'   => $isTemp,
            'goods_id'  => $goodsId
        );
        $browsingHistory = $browsingHistoryModel->where($where)->order('add_time DESC')->find();
        if( empty($browsingHistory) ){
            $data = $where;
            $data['add_time'] = time();
            $data['browsing_number'] = 1;
            // $data['category_id'] = $cid;
            $browsingHistoryModel->add($data);
        } else {
            $now = date('Y-m-d');
            $add_time  = date('Y-m-d', $browsingHistory['add_time']);
            
            if($now == $add_time){
                // $data['category_id'] = $cid;
                $data['browsing_number'] = $browsingHistory['browsing_number'] + 1;
                $browsingHistoryModel->where($where)->save($data);
            } else {
                $data = $where;
                // $data['category_id'] = $cid;
                $data['browsing_number'] = 1;
                $data['add_time'] = time();
                $browsingHistoryModel->add($data);
            }
        }
	}

	/**
	 * [isBrowsing 是否浏览过商品]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)      2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $userId  [description]
	 * @param     [type]        $goodsId [description]
	 * @return    boolean                [description]
	 */
	private function isBrowsing($userId, $goodsId) {
		$where = array(
			'user_id' => $userId,
			'goods_id' => $goodsId
		);
		$browsingHistoryModel = M('browsing_history');
		$browsingHistory = $browsingHistoryModel->where($where)->find();
		if ( !empty($browsingHistory) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * [getGoodsImages 获取商品相册]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
	public function getGoodsImages($goods_images_id) {
		$goods_images_id = trim($goods_images_id, ',');
		$goodsImagesModel = M('goods_images');
		$images = $goodsImagesModel->where(array('id'=> array('IN', $goods_images_id)))->field('`goods_image`')->select();
		$images = array_column($images, 'goods_image');
		if (!empty($images)) {
			return $images;
		} else {
			return array();
		}
	}

	/**
	 * [getGoodsExtension 商品详情内容]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
	public function getGoodsExtension($goods_ext_id) {
		$goodsExtension = M('goods_extension');
		$goodsDesc = $goodsExtension->where(array('id'=> $goods_ext_id))->getField('`goods_desc`');
		$goodsDesc = htmlspecialchars_decode($goodsDesc);
		return $goodsDesc;
	}

	/**
	 * [getGoodsList 获取商品列表]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $parameter [description]
	 * @return    [type]                   [description]
	 */
	public function getGoodsList($parameter) {
		$agent_id = !empty($parameter['agent_id']) ? $parameter['agent_id'] : '';
		$category_id = !empty($parameter['category_id']) ? $parameter['category_id'] : '';
		$agent_category_id = !empty($parameter['agent_category_id']) ? $parameter['agent_category_id'] : '';
		$keyword = !empty($parameter['keyword']) ? $parameter['keyword'] : '';
		$goods_name = !empty($parameter['goods_name']) ? $parameter['goods_name'] : '';
		$express_type = ($parameter['express_type'] != '') ? $parameter['express_type'] : '-1';
		$is_on_sale = ($parameter['is_on_sale'] != '') ? $parameter['is_on_sale'] : '-1';
		$is_recommend = ($parameter['is_recommend'] != '') ? $parameter['is_recommend'] : '-1';
		$is_auth = ($parameter['is_auth'] != '') ? $parameter['is_auth'] : '-1';
		$is_favorable = ($parameter['is_favorable'] != '') ? $parameter['is_favorable'] : '-1';
		$date = !empty($parameter['date']) ? $parameter['date'] : '';
		$comment_sort = ($parameter['comment_sort'] != '') ? $parameter['comment_sort'] : '-1';
		$sale_sort = ($parameter['sale_sort'] != '') ? $parameter['sale_sort'] : '-1';
		$price_sort = ($parameter['price_sort'] != '') ? $parameter['price_sort'] : '-1';
		$number_sort = ($parameter['number_sort'] != '') ? $parameter['number_sort'] : '-1';
		$time_sort = ($parameter['time_sort'] != '') ? $parameter['time_sort'] : '-1';
		$goods_type = !empty($parameter['goods_type']) ? $parameter['goods_type'] : '0';
		$sort = !empty($parameter['sort']) ? $parameter['sort'] : 0;
		$type = !empty($parameter['type']) ? $parameter['type'] : 0;
		$page = !empty($parameter['page']) ? $parameter['page'] : 0;
		$limitStart = !empty($parameter['limitStart']) ? $parameter['limitStart'] : 0;
		$limit = !empty($parameter['limit']) ? $parameter['limit'] : 10;
		$where = !empty($parameter['where']) ? $parameter['where'] : array();
		// 商品类型（0：普通商品，1：积分商品）
		$where['goods_type'] = $goods_type;
		$where['is_delete'] = '0';
		$where['is_lock'] = '0';
		$where['goods_main_id'] = '0';
		//商品
		if ( !empty($goods_name) ) {
			$where['goods_name'] = array('LIKE', "%{$goods_name}%");
		}
		// 店铺筛选
		if ( !empty($agent_id) ) {
			$where['agent_id'] = $agent_id;
		}
		// 搜索词
		if ( !empty($keyword) ) {
			$where['keyword'] = array('LIKE', "%{$keyword}%");
		}
		// 平台商品分类
		if ( !empty($category_id) ) {
			$where['category_id'] = $category_id;
		}
		// 店铺商品分类
		if ( !empty($agent_category_id) ) {
			$where['agent_category_id'] = $agent_category_id;
		}
		// 是否推荐（即置顶 -1：关闭）
		if ( $is_recommend != '-1' ) {
			$where['is_recommend'] = $is_recommend;
		}
		// 是否认证（-1：关闭）
		if ( $is_auth != '-1' ) {
			$where['is_auth'] = $is_auth;
		}
		// 是否上架（-1：关闭）
		if ( $is_on_sale != '-1' ) {
			$where['is_on_sale'] = $is_on_sale;
		}
		// 是否竞价优惠商品
		if ( $is_favorable != '-1' ) {
			$time = time();
			$where['favorable_start_time'] = array('ELT', $time);
			$where['favorable_end_time'] = array('EGT', $time);
			$order .= '`favorable_price` DESC, ';
		}
		// 日期筛选
		if ( !empty($date) ) {
			$startTime = strtotime($date);
			$endTime = $startTime + 86400;
			$where['add_time'] = array('BETWEEN', array($startTime, $endTime));
		}
		// 
		if ( $express_type != '-1' ) {
			$where['express_type'] = array('LIKE', "%{$express_type}%");
		}
		$order = ( empty($sort) ) ? 'is_recommend DESC, is_auth DESC, ' : '';
		// 人气排序 （-1：关闭，0：升序，1：降序）
		if ( $comment_sort != '-1' ) {
			// $order .= ($comment_sort == '1') ? '`comment_number` DESC, ' : '`comment_number` ASC, ';
			$order .= '`comment_number` DESC, ';
		}
		// 销量排序 （-1：关闭，0：升序，1：降序）
		if ( $sale_sort != '-1' ) {
			// $order .= ($sale_sort == '1') ? '`sale_number` DESC, ' : '`sale_number` ASC, ';
			$order .= '`sale_number` DESC, ';
		}
		// 价格排序 （-1：关闭，0：升序，1：降序）
		if ( $price_sort != '-1' ) {
			// $order .= ($price_sort == '1') ? '`goods_price` DESC, ' : '`goods_price` ASC, ';
			$order .= '`goods_price` ASC, ';
		}
		// 库存排序
		if ( $number_sort != '-1' ) {
			$order .= ($number_sort == '1') ? '`goods_number` DESC, ' : '`goods_number` ASC, ';
		}
		//时间排序
		if( $time_sort != '-1') {
			$order .= ($time_sort == '1') ? '`add_time` DESC, ' : '`add_time` ASC, ';
		}
		$order .= ( $comment_sort != '-1' || $sale_sort != '-1' || $price_sort != '-1' ) ? '`id` DESC' : '`bidding_money` DESC, `id` DESC';
		$field = '`agent_id`, `category_id`, `agent_category_id`, `is_recommend`, `is_auth`, `id`, `goods_name`, `introduction`, `goods_image`, `goods_number`, `goods_price`, `sale_number`, `is_on_sale`, `is_favorable`, `favorable_start_time`, `favorable_end_time`, `favorable_price`, `add_time`, `comment_number`';
		$count = 0;
		if ( !empty($type) ) {
			$count = $this->where($where)->count();
		}
		$list = $this->where($where)->field($field)->order($order)->limit($limitStart.','.$limit)->select();
		$time = time();
		foreach ($list as &$value) {
			$value['is_favorable'] = ( $value['favorable_start_time'] <= $time && $value['favorable_end_time'] >= $time ) ? '1' : '0';
		}
		$returnData['list'] 	= $list;
		$returnData['page'] 	= $page + 1;
		$returnData['count'] 	= ceil($count / $limit);
		return $returnData;
	}

	public function processData($list) {
		$userCollect = M('user_collect');
		foreach ($list as $key => &$value) {
			$where = array(
				'type' => '0',
				'goods_id' => $value['id']
			);
			$value['collect_number'] = $userCollect->where($where)->count();
			$value['category_name'] = getCategoryName($value['category_id']);
			$value['agent_category_name'] = getAgentCategoryName($value['agent_category_id']);
		}
		return $list;
	}

	public function processLocation($list) {
		$agentList = array();
		$agentModel = M('agent');
		foreach ($list as $key => &$value) {
			$agentId = $value['agent_id'];
			if ( !array_key_exists($agentId, $agentList) ) {
				$agentData = $agentModel->where(array('id'=> $agentId))->field('`longitude`, `latitude`')->find($agentId);
				$agentList[$agentId]['longitude'] = $agentData['longitude'];
				$agentList[$agentId]['latitude'] = $agentData['latitude'];
			}
			$value['longitude'] = $agentList[$agentId]['longitude'];
			$value['latitude'] = $agentList[$agentId]['latitude'];
		}
		return $list;
	}


	public function getGoodsInfo($goodsId) {
        // $firstData = M('goods_category')->where(array('pid'=>'0'))->select();
        // 商品基本信息
        $goodsInfo = $this->find($goodsId);
        $goodsInfo['attr_list'] = $goodsInfo['attr_list'];
        // 属性值
        $goodsAttrNameModel  = M('goods_attr_name');
        $goodsAttrValueModel = M('goods_attr_value');
        $attrData = $goodsAttrNameModel->where(array('category_id'=> $goodsInfo['agent_category_id']))->select();
        if ( !empty($attrData) ) {
            $attrId = array_column($attrData, 'id');
            $attrValueData = $goodsAttrValueModel->where(array('attr_name_id'=>array('in', $attrId)))->select();
            foreach ($attrValueData as $gavkey => $gavvalue) {
                foreach ($attrData as $ankey => $anvalue) {
                    if ( $gavvalue['attr_name_id'] == $anvalue['id'] ) {
                        $attrData[$ankey]['attrValue'][] = $gavvalue;
                    }
                }
            }
        }
        // 分类
        // $categoryInfo = $this->recursiveFindCategory($goodsInfo['category_id']);
        // 商品图片
        $goodsImages = $this->getGoodsImages($goodsInfo['goods_images_id']);
        // 商品详情
        $goodsDesc = $this->getGoodsExtension($goodsInfo['goods_ext_id']);
        // SKU
        $relevanceData = M('goods_relevance')->find($goodsInfo['relevance_id']);
        $relevanceGoods = array();
        if( !empty($relevanceData) ) {
            $relevanceData['relevance_id'] = explode(',', $relevanceData['relevance_id']);
            $attrArr = explode(',', $relevanceData['relevance_attr']);

            foreach ($relevanceData['relevance_id'] as $key => $value) {
                $relevanceGoods[$value] = $this->field('`goods_price`,`goods_number`')->find($value);
                $relevanceGoods[$value]['attr'] = $attrArr[$key];
            }
            $relevanceData['relevance_id'] = $relevanceGoods;
            $relevanceData['relevance_attr'] = preg_split('/[-,]/is', $relevanceData['relevance_attr']);
            $relevanceData['relevance_attr'] = array_merge(array_unique($relevanceData['relevance_attr']), array());
        } else {
            $relevanceData = array();
        }

        $returnData = array(
        	// 'firstData' 	=> $firstData,
            'goodsImages' 	=> $goodsImages,
            'goodsInfo' 	=> $goodsInfo,
            'goodsDesc' 	=> $goodsDesc,
            // 'categoryInfo' 	=> $categoryInfo,
            'attrData' 		=> $attrData,
            'relevanceData' => json_encode($relevanceData),
        );
        return $returnData;
	}

	public function getCategoryAttr($categoryId, $attrArray = '') {
		$goodsAttrNameModel  = M('goods_attr_name');
        $goodsAttrValueModel = M('goods_attr_value');
		$attrData = $goodsAttrNameModel->where(array('category_id'=> $categoryId, 'is_relation'=> '1'))->field('`id`, `attr_name`')->select();

        if ( !empty($attrData) ) {
            $attrId = array_column($attrData, 'id');
            $goodsAttrValueList = $goodsAttrValueModel->where(array('attr_name_id'=>array('in', $attrId)))->select();
            foreach ($attrData as $key => &$value) {
                foreach ($goodsAttrValueList as $gvalue) {
                    if ( $gvalue['attr_name_id'] == $value['id'] ) {
                    	if ( !empty($attrArray) && strstr($attrArray, ','.$gvalue['id'].',') !== false ) {
	                        $value['attrValue'][] = $gvalue;
                    	} elseif ( empty($attrArray) ) {
                    		$value['attrValue'][] = $gvalue;
                    	}
                    }
                }
                if (empty($attrData[$key]['attrValue'])) {
                    unset($attrData[$key]);
                }
            }
        } else {
            $attrData = array();
        }
        return $attrData;
	}

	/**
	 * [addGoodsImages 添加商品图片]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)     2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $images [description]
	 */
	public function addGoodsImages($images) {
		$goodsImagesModel = M('goods_images');
		$goods_images_id = array();
		foreach ($images as $key => $value) {
            $temp['goods_image'] = $value;
            $goods_images_id[] = $goodsImagesModel->add($temp);
        }
        
        return array(
        	'goods_image' => $images[0],
        	'goods_images_id' => implode(',', $goods_images_id)
        );
	}

	/**
	 * [editGoodsImages 编辑商品图片]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $images    [description]
	 * @param     [type]        $goodsData [description]
	 * @return    [type]                   [description]
	 */
	public function editGoodsImages($images, $goodsData) {
		$goodsImagesModel = M('goods_images');
		$where['id'] = array('IN', $goodsData['goods_images_id']);
		$imagesList = $goodsImagesModel->where($where)->select();
		$goods_images_id = array();
		// 处理旧图
		foreach ($imagesList as $key => $value) {
			if ( !in_array($value['goods_image'], $images) ) {
				$goodsImagesModel->delete($value['id']);
			} else {
				$goods_images_id[] = $value['id'];
			}
		}
		// 处理新图
		$imagesData = array_column($imagesList, 'goods_image');
		foreach ($images as $key => $value) {
			if ( !in_array($value, $imagesData) ) {
				$temp['goods_image'] = $value;
            	$goods_images_id[] = $goodsImagesModel->add($temp);
			}
		}
		return array(
        	'goods_image' => $images[0],
        	'goods_images_id' => implode(',', $goods_images_id)
        );
	}

	/**
	 * [addGoodsExtension 添加商品详情]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)          2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $description [description]
	 */
	public function addGoodsExtension($description) {
		$description = '<div class="contentDetail">'.$description.'</div><style>.contentDetail img{width:100%}</style>';
		$goodsExtensionModel = M('goods_extension');
		$descriptionData = array(
            'goods_desc' => $description,
            'add_time'   => time(),
        );
        $extensionId = $goodsExtensionModel->add($descriptionData);
        return $extensionId;
	}

	/**
	 * [editGoodsExtension 编辑商品详情]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)          2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $description [description]
	 * @param     [type]        $goodsData   [description]
	 * @return    [type]                     [description]
	 */
	public function editGoodsExtension($description, $goodsData) {
		$goodsExtensionModel = M('goods_extension');
		$map['id'] = $goodsData['goods_ext_id'];
		if( $goodsExtensionModel->where($map)->count() > 0 ){
			$description = str_replace('<div class="contentDetail">', '', $description);
			$description = str_replace('</div>', '', $description);
			$description = str_replace('<style>.contentDetail img{width:100%}</style>', '', $description);
			$description = '<div class="contentDetail">'.$description.'</div><style>.contentDetail img{width:100%}</style>';
			$descData['goods_desc'] = $description;
            $goodsExtensionModel->where($map)->data($descData)->save();
            $extensionId = $goodsData['goods_ext_id'];
        } else {
            $extensionId = $this->addGoodsExtension($description);
        }
		return $extensionId;
	}

	public function checkGoodsImages($images, $goodsData)
	{
		$goodsImagesModel = M('goods_images');
		$where['id'] = array('IN', $goodsData['goods_images_id']);
		$imagesList = $goodsImagesModel->where($where)->select();
		if ( count($imagesList) != count($images) ) {
			return false;
		}
		$goods_images_id = array();
		foreach ($imagesList as $key => $value) {
			if ( !in_array($value['goods_image'], $images) ) {
				$goods_images_id[] = $value['id'];
			}
		}
		if ( !empty($goods_images_id) ) {
			return false;
		}
		
		return true;
	}

	/**
	 * [addGoodsRelevance 添加商品关联]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c) 2017 Xcrozz (http://www.xcrozz.com)
	 */
	public function addGoodsRelevance($goodsIdArr, $relevanceAttr) {
		$goodsRelevanceModel = M('goods_relevance');
		$allGoodsId = implode(',', $goodsIdArr);
        $relevanceAttr = implode(',', $relevanceAttr);
        $goodsRelevanceData = array(
            'relevance_id' => $allGoodsId,
            'relevance_attr'=> $relevanceAttr,
            'add_time' => time(),
        );
        $relevanceId = $goodsRelevanceModel->data($goodsRelevanceData)->add();
        $this->where(array('id'=>array('IN', $allGoodsId)))->save(array('relevance_id'=>$relevanceId));
	}

	/**
	 * [editGoodsRelevance 编辑商品关联]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)            2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $goodsIdArr    [description]
	 * @param     [type]        $relevanceAttr [description]
	 */
	public function editGoodsRelevance($goodsIdArr, $relevanceAttr, $goodsData) {
		$goodsRelevanceModel = M('goods_relevance');
		$allGoodsId = implode(',', $goodsIdArr);
        $relevanceAttr = implode(',', $relevanceAttr);
        $goodsRelevanceData = array(
            'relevance_id' => $allGoodsId,
            'relevance_attr'=> $relevanceAttr,
            'add_time' => time(),
        );
        $map['id'] = $goodsData['relevance_id'];
		if( $goodsRelevanceModel->where($map)->count() > 0 ){
            $goodsRelevanceModel->where($map)->data($goodsRelevanceData)->save();
        } else {
            $relevanceId = $goodsRelevanceModel->add($goodsRelevanceData);
            $this->where(array('id'=>array('IN', $allGoodsId)))->save(array('relevance_id'=> $relevanceId));
        }
   	}
	/**
	 * [deleteGoods 删除店铺商品]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)      2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $goodsId 	[description]
	 * @param     [type]        $agentId  	[description]
	 * @return    [type]                 	[description]
	 */
	public function deleteGoods($goodsId, $agentId) {
		$where 	= array(
			'id'        => array('IN', $goodsId),
            'agent_id'	=> $agentId
		);
		if ( $this->where($where)->save(array('is_delete'=> '1')) !== false ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * [setOnSale 设置店铺商品上下架]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)         2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $goodsId    [description]
	 * @param     [type]        $agentId    [description]
	 * @param     [type]        $is_on_sale [description]
	 */
	public function setOnSale($goodsId, $agentId, $is_on_sale) {
		$where 	= array(
			'id'        => array('IN', $goodsId),
            'agent_id'  => $agentId
		);
		$saveData['is_on_sale'] = $is_on_sale;
		if ( $this->where($where)->save($saveData) !== false ) {
			$this->where(array('goods_main_id'=> $goodsId))->save($saveData);
			return true;
		} else {
			return false;
		}
	}

	public function getPointGoods($parameter) {
		$goods_name = !empty($parameter['goods_name']) ? $parameter['goods_name'] : '';
		$type = !empty($parameter['type']) ? $parameter['type'] : '0';
		$pageType = !empty($parameter['pageType']) ? $parameter['pageType'] : '0';
		$agentId = !empty($parameter['agentId']) ? $parameter['agentId'] : '';
		$page = !empty($parameter['page']) ? $parameter['page'] : 0;
		$limitStart = !empty($parameter['limitStart']) ? $parameter['limitStart'] : 0;
		$limit = !empty($parameter['limit']) ? $parameter['limit'] : 10;
		$count = 0;
		switch ( $type ) {
			case '0':
				$where = array(
					'agent_id' => $agentId,
					'goods_type' => '0',
					'goods_main_id' => '0',
					'is_delete' => '0'
				);
				if ( !empty($goods_name) ) {
					$where['goods_name'] = array('LIKE', "%{$goods_name}%");
				}
				$goodsData = M('goods_to_point')->where(array('agent_id'=> $agentId, 'status'=> '0'))->select();
				if ( !empty($goodsData) ) {
					$goodsIds = implode(',' , array_column($goodsData, 'goods_id'));
					$where['id'] = array('NOTIN', $goodsIds);
				}
				if ( !empty($pageType) ) {
					$count = $this->where($where)->count();
				}
				$list = $this->where($where)->order('`id` DESC')->field('`id` AS `goods_id`, `goods_name`, `introduction`, `goods_image`, `goods_number`, `sale_number`, `goods_price`')->limit($limitStart.','.$limit)->select();
				break;
			case '1':
				$sql = $this->pointSelectSql($agentId, '0', $limitStart, $limit, $goods_name);
				$list = M()->query($sql);
				if ( !empty($pageType) ) {
					$sql = $this->pointCountSql($agentId, '0', $goods_name);
					$count = M()->query($sql);
					$count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
				}
				break;
			case '2':
				$sql = $this->pointSelectSql($agentId, '1', $limitStart, $limit, $goods_name);
				$list = M()->query($sql);
				if ( !empty($pageType) ) {
					$sql = $this->pointCountSql($agentId, '1', $goods_name);
					$count = M()->query($sql);
					$count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
				}
				break;
			case '3':
				$sql = $this->pointSelectSql($agentId, '-1', $limitStart, $limit, $goods_name);
				$list = M()->query($sql);
				if ( !empty($pageType) ) {
					$sql = $this->pointCountSql($agentId, '-1', $goods_name);
					$count = M()->query($sql);
					$count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
				}
				break;
		}
		$returnData['list'] 	= $list;
		$returnData['page'] 	= $page + 1;
		$returnData['count'] 	= ceil($count / $limit);
		return $returnData;
	}

	private function pointSelectSql($agentId, $status, $limitStart, $limit, $goods_name) {
		$whereStr = '';
		$whereStr .= ( $status != '-1' ) ? " AND `gtc`.`status` = '{$status}'" : '';
		$whereStr .= !empty($goods_name) ? " AND `g`.`goods_name` LIKE '%{$goods_name}%'" : '';
		$sql = "SELECT `gtc`.`id`, `g`.`goods_name`, `g`.`introduction`, `g`.`goods_image`, `g`.`goods_number`, `g`.`sale_number`, `g`.`goods_price`, `gtc`.`goods_id`, `gtc`.`status` 
				FROM `{$this->dbPrefix}goods_to_point` AS `gtc` 
				LEFT JOIN `{$this->dbPrefix}goods` AS `g` ON `gtc`.`goods_id` = `g`.`id` 
				WHERE `gtc`.`agent_id` = '{$agentId}' 
				{$whereStr}
				AND `g`.`goods_type` = '0' 
				AND `g`.`goods_main_id` = '0' 
				AND `g`.`is_delete` = '0' 
				ORDER BY `gtc`.`id` DESC 
				LIMIT {$limitStart} , {$limit}";
		return $sql;
	}

	private function pointCountSql($agentId, $status, $goods_name) {
		$whereStr = '';
		$whereStr .= ( $status != '-1' ) ? " AND `gtc`.`status` = '{$status}'" : '';
		$whereStr .= !empty($goods_name) ? " AND `g`.`goods_name` LIKE '%{$goods_name}%'" : '';
		$sql = "SELECT COUNT(*) AS `count` 
				FROM `{$this->dbPrefix}goods_to_point` AS `gtc` 
				LEFT JOIN `{$this->dbPrefix}goods` AS `g` ON `gtc`.`goods_id` = `g`.`id` 
				WHERE `gtc`.`agent_id` = '{$agentId}' 
				{$whereStr}
				AND `g`.`goods_type` = '0' 
				AND `g`.`goods_main_id` = '0' 
				AND `g`.`is_delete` = '0' ";
		return $sql;
	}

	/**
	 * [checkGoodsCollect 检测商品是否收藏]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)     2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $userId [description]
	 * @param     [type]        $list   [description]
	 * @return    [type]                [description]
	 */
	public function checkGoodsCollect($userId, $list) 
	{
		$userCollectModel = M('user_collect');
		foreach ($list as $key => &$value) {
			$where = array(
            	'user_id'	=> $userId,
            	'goods_id'	=> $value['id'],
            	'type'		=> '0'
            );
            $collect_id = M('user_collect')->where($where)->getField('`id` AS `collect_id`');
            $value['is_collect'] = !empty($collect_id) ? '1' : '0';
        	$value['collect_id'] = !empty($collect_id) ? $collect_id : '0';
		}
		return $list;
	}

	public function checkGoodsCollect2($userId, $list) 
	{
		$userCollectModel = M('user_collect');
		foreach ($list as $key => &$value) {
			$where = array(
            	'user_id'	=> $userId,
            	'goods_id'	=> $value['goods_id'],
            	'type'		=> '0'
            );
            $collect_id = M('user_collect')->where($where)->getField('`id` AS `collect_id`');
            $value['is_collect'] = !empty($collect_id) ? '1' : '0';
        	$value['collect_id'] = !empty($collect_id) ? $collect_id : '0';
		}
		return $list;
	}
}
