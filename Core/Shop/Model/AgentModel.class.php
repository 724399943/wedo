<?php
namespace Shop\Model;
use Think\Model;
class AgentModel extends Model {
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
		array('company_name', 'require', '{%module_agent_company_name}', 1, 'regex', 1),
		array('registr_id', 'require', '{%module_agent_license_number}', 1, 'regex', 1),
		array('address', 'require', '{%module_agent_company_address}', 1, 'regex', 1),
		array('manager', 'require', '{%module_agent_contact_person_name}', 1, 'regex', 1),
		array('email', 'require', '{%module_agent_contact_person_email}', 1, 'regex', 1),
		array('telephone', 'require', '{%module_agent_contact_number}', 1, 'regex', 1),
		// array('agent_phone', 'require', '{%module_agent_merchant_phone_number}', 1, 'regex', 1),
		array('categoryids', 'require', '{%module_agent_merchant_business}', 1, 'regex', 1),
		array('license', 'require', '{%module_agent_upload_business_documents}', 1, 'regex', 1),

		// 修改
		array('company_name', 'require', '{%module_agent_company_name}', 1, 'regex', 2),
		array('registr_id', 'require', '{%module_agent_license_number}', 1, 'regex', 2),
		array('address', 'require', '{%module_agent_company_address}', 1, 'regex', 2),
		array('manager', 'require', '{%module_agent_contact_person_name}', 1, 'regex', 2),
		array('email', 'require', '{%module_agent_contact_person_email}', 1, 'regex', 2),
		array('telephone', 'require', '{%module_agent_contact_number}', 1, 'regex', 2),
		array('agent_phone', 'require', '{%module_agent_merchant_phone_number}', 1, 'regex', 2),
		array('categoryids', 'require', '{%module_agent_merchant_business}', 1, 'regex', 2),
		array('license', 'require', '{%module_agent_upload_business_documents}', 1, 'regex', 2),

		// 修改基本资料
		array('agent_name', 'require', '{%module_agent_merchant_name}', 1, 'regex', 4),
		array('address', 'require', '{%module_agent_company_address}', 1, 'regex', 4),
		array('email', 'require', '{%module_agent_contact_person_email}', 1, 'regex', 4),
		array('telephone', 'require', '{%module_agent_contact_number}', 1, 'regex', 4),
		array('agent_phone', 'require', '{%module_agent_merchant_phone_number}', 1, 'regex', 4),
		array('categoryids', 'require', '{%module_agent_merchant_business}', 1, 'regex', 4),
		array('logo', 'require', '{%module_agent_merchant_images}', 1, 'regex', 4),
		array('agent_keyword', 'require', '{%module_agent_merchant_keywords}', 1, 'regex', 4),
		array('agent_keyword', 'checkKeywordSymbol', '{%module_agent_merchant_keyword}', 1, 'callback', 4),
		array('introduction', 'require', '{%module_agent_merchant_introduction}', 1, 'regex', 4),
	);

	public function checkKeywordSymbol($value) {
		if ( strpos($value, ';') !== false ) {
			return true;
		} else {
			return false;
		}
	}

	public function calc($lng1, $lat1, $lng2, $lat2) {
        // 第一点经纬度： lng1 lat1
        // 第二点经纬度： lng2 lat2
        return "ROUND(6378.138*2*ASIN(SQRT(POW(SIN(({$lat1}*PI()/180-{$lat2}*PI()/180)/2),2)+COS({$lat1}*PI()/180)*COS({$lat2}*PI()/180)*POW(SIN(({$lng1}*PI()/180-{$lng2}*PI()/180)/2),2)))*1000)";
        // return "(2*ATAN2(SQRT(SIN(($lat1-$lat2)*PI()/180/2)  
        // *SIN(($lat1-$lat2)*PI()/180/2)+  
        // COS($lat2*PI()/180)*COS($lat1*PI()/180)  
        // *SIN(($lng1-$lng2)*PI()/180/2)  
        // *SIN(($lng1-$lng2)*PI()/180/2)),  
        // SQRT(1-SIN(($lat1-$lat2)*PI()/180/2)  
        // *SIN(($lat1-$lat2)*PI()/180/2)  
        // +COS($lat2*PI()/180)*COS($lat1*PI()/180)  
        // *SIN(($lng1-$lng2)*PI()/180/2)  
        // *SIN(($lng1-$lng2)*PI()/180/2))))*6378140";
    }

	protected $_auto = array(
		array('add_time', 'time', 1, 'function'),
		array('introduction', 'htmlspecialchars_decode', 4, 'function'),
	);

	/**
	 * [getAgentlist 获得店铺列表]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $parameter [description]
	 * @return    [type]                   [description]
	 */
	public function getAgentlist($parameter) {
		$category_id 	= $parameter['category_id'] ? $parameter['category_id'] : '';
		$keyword 		= $parameter['keyword'] ? $parameter['keyword'] : '';
		$is_recommend 	= $parameter['is_recommend'] != '' ? $parameter['is_recommend'] : '-1';
		$is_hot 		= $parameter['is_hot'] != '' ? $parameter['is_hot'] : '-1';
		$is_nearby 		= $parameter['is_nearby'] != '' ? $parameter['is_nearby'] : '-1';
		$distance_sort 	= !empty($parameter['distance_sort']) ? $parameter['distance_sort'] : '-1';
		$id_sort 		= !empty($parameter['id_sort']) ? $parameter['id_sort'] : '-1';
		$limitStart 	= $parameter['limitStart'] ? $parameter['limitStart'] : 0;
		$limit 			= $parameter['limit'] ? $parameter['limit'] : 10;
		$longitude 		= $parameter['longitude'];
		$latitude 		= $parameter['latitude'];
		$meter 			= $parameter['meter'];
		$whereStr 		= " AND `is_on_sale` = '0'";
		$field = '`id`, `logo`, `star`, `agent_name`, `address`, `longitude`, `latitude`';
		$order = '';

		// 搜索关键词
		if ( !empty($keyword) ) {
			$whereStr .= " AND (`agent_name` LIKE '%{$keyword}%' OR `agent_keyword` LIKE '%{$keyword}%')";
		}

		// 是否推荐
		if ( $is_recommend != '-1' ) {
			$time = time();
			$whereStr .= " AND `start_show_time` < ". $time . " AND `end_show_time` > ". $time;
			$order .= '`bidding_money` DESC,';
		}

		// 是否人气最高
		if ( $is_hot != '-1' ) {
			$order .= '`star` DESC,';
		}

		// 经纬度
		if ( $longitude && $latitude && $meter ) {
			// 距离
			$distance = $this->calc($longitude, $latitude, '`longitude`', '`latitude`');
			$whereStr .= " AND {$distance} < {$meter}";
			$field .= ", {$distance} AS `distance` ";
			if ( $distance_sort == '-1' ) {
				$order .= '`distance` ASC,';
			}
		}

		$order .= ($id_sort != '-1') ? '`id` DESC' :'`sort` DESC, `id` DESC';
		// 分类筛选
		if ( !empty($category_id) ) {
			$sql = "SELECT GROUP_CONCAT(`agent_id`) AS `ids`
					FROM `{$this->dbPrefix}agent_category_relevance` 
					WHERE `category_id` = '{$category_id}'";
			$ids = M()->query($sql);
			$ids = !empty($ids[0]['ids']) ? $ids[0]['ids'] : '';
			if (!empty($ids)) {
				$sql = "SELECT {$field} 
						FROM `{$this->dbPrefix}agent` 
						WHERE `id` IN({$ids}) {$whereStr} 
						ORDER BY {$order} 
						LIMIT {$limitStart} , {$limit}";
				$list = M()->query($sql);
			}
		} else {
			$sql = "SELECT {$field} 
					FROM `{$this->dbPrefix}agent` 
					WHERE 1{$whereStr} 
					ORDER BY {$order} 
					LIMIT {$limitStart} , {$limit}";
			$list = M()->query($sql);
		}

		foreach ($list as $key => &$value) {
			$value['distance']	= round($value['distance'] / 1000, 1);
			$value['agent_name'] = htmlspecialchars_decode($value['agent_name']);
		}

		$list = !empty($list) ? $list : array();
		return $list;
	}

	/**
	 * [getAgentDetail 获取店铺详情]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
	public function getAgentDetail($id, $userId) {
		$goodsComment = M('goods_comment');
		$this->where(array('id'=> $id))->setInc('browsing_number');
		// $browsingHistory = M('browsing_history');
		$agentDetail = $this->field('`id`, `user_id`, `agent_name`, `logo`, `address`, `longitude`, `latitude`, `star`, `agent_phone`, `browsing_number`')->find($id);
		$where['agent_id'] = $id;
		$commentCount = $goodsComment->where($where)->count();
		// $browingCount = $browsingHistory->where($where)->field('SUM(`browsing_number`) AS `count`')->find();
		// $browingCount = !empty($browingCount['count']) ? $browingCount['count'] : '0';
		$browingCount = $agentDetail['browsing_number'];
		// 是否收藏过该店铺
        $where = array(
        	'user_id'	=> $userId,
        	'agent_id'	=> $id,
        	'type'		=> '1'
        );
        $collect_id = M('user_collect')->where($where)->getField('`id` AS `collect_id`');
        // 是否关注该店铺
        unset($where['type']);
        $attention_id = M('user_attention')->where($where)->getField('`id` AS `attention_id`');
		$returnData['agentDetail'] 	= $agentDetail;
		$returnData['commentCount'] = $commentCount;
		$returnData['browingCount'] = $browingCount;
		$returnData['is_collect'] 	= !empty($collect_id) ? '1' : '0';
        $returnData['collect_id'] 	= !empty($collect_id) ? $collect_id : '0';
		$returnData['is_attention'] = !empty($attention_id) ? '1' : '0';
		return $returnData;
	}

	/**
	 * [getAgentDesc 获取店铺简介]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c) 2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $id [description]
	 * @return    [type]            [description]
	 */
	public function getAgentDesc($id, $userId) {
		$agentImages = M('agent_images');
		$data = $this->field('`introduction`, `agent_image_ids`')->find($id);
		$where['id'] = array('IN', $data['agent_image_ids']);
		$imagesList = $agentImages->where($where)->select();
		// 是否收藏过该店铺
        $where = array(
        	'user_id'	=> $userId,
        	'agent_id'	=> $id,
        	'type'		=> '1'
        );
        $collect_id = M('user_collect')->where($where)->getField('`id` AS `collect_id`');
        // 是否关注该店铺
        unset($where['type']);
        $attention_id = M('user_attention')->where($where)->getField('`id` AS `attention_id`');

        // 处理图片地址
        $webSite = trim(C('webSite'), '/');
        $introduction = $data['introduction'];
        $introduction = str_replace('/Static/Uploads/', $webSite . '/Static/Uploads/', $introduction);
		$returnData['introduction'] = $introduction;
		$returnData['imagesList'] 	= $imagesList;
		$returnData['is_collect'] 	= !empty($collect_id) ? '1' : '0';
        $returnData['collect_id'] 	= !empty($collect_id) ? $collect_id : '0';
        $returnData['is_attention'] = !empty($attention_id) ? '1' : '0';
		return $returnData;
	}

	/**
	 * [getAgentInfo 获取店铺信息]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)      2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $agentId [description]
	 * @param     [type]        $field   [description]
	 * @return    [type]                 [description]
	 */
	public function getAgentInfo($agentId, $field) {
		$where['id'] = $agentId;
		$list = $this->where($where)->field($field)->find();
		return $list;
	}
}