<?php
namespace Shop\Model;
use Think\Model;

class UserConsigneeModel extends Model {
	/**
	 * [_validate 自动验证]
     * @author kofu <[418382595@qq.com]>
	 */
	protected $_validate = array(
		// 新增
		array('consignee', 'require', '{%module_user_consignee_receiver}', 1, 'regex', 1),
		array('province', 'require', '{%module_user_consignee_region}', 1, 'regex', 1),
		array('city', 'require', '{%module_user_consignee_region}', 1, 'regex', 1),
		// array('county', 'require', '{%module_user_consignee_region}', 1, 'regex', 1),
		array('address', 'require', '{%module_user_consignee_address}', 1, 'regex', 1),
		array('telephone', 'require', '{%module_user_consignee_contact}', 1, 'regex', 1),

		// 修改
		array('consignee', 'require', '{%module_user_consignee_receiver}', 1, 'regex', 2),
		array('province', 'require', '{%module_user_consignee_region}', 1, 'regex', 2),
		array('city', 'require', '{%module_user_consignee_region}', 1, 'regex', 2),
		// array('county', 'require', '{%module_user_consignee_region}', 1, 'regex', 2),
		array('address', 'require', '{%module_user_consignee_address}', 1, 'regex', 2),
		array('telephone', 'require', '{%module_user_consignee_contact}', 1, 'regex', 2),
	);

	protected $_auto = array(
		array('add_time', 'time', 1, 'function'),
	);

	/**
	 * [getUserConsignee 获取用户地址]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)     2016          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $userId [description]
	 * @return    [type]                [description]
	 */
	public function getUserConsignee($userId) {
		$userModel = M('user');
		$defaultId = $userModel->where(array('id'=> $userId))->getField('default_id');
		if (!empty($defaultId)) {
			$consignee = $this->find($defaultId);
		} else {
			$consignee = $this->where(array('user_id'=> $userId))->order('id DESC')->limit(1)->select();
			$consignee = !empty($consignee[0]) ? $consignee[0] : '';
		}
		if (empty($consignee)) {
			return false;
		} else {
			return $consignee;
		}
	}

	/**
	 * [getUserAllConsignee 获取所有用户地址]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)         2016          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $userId     [description]
	 * @param     integer       $limitStart [description]
	 * @param     integer       $limit      [description]
	 * @return    [type]                    [description]
	 */
	public function getUserAllConsignee($userId, $limitStart = 0, $limit = 10) {
		$list = $this->where(array('user_id'=> $userId))->order('`is_default` DESC')->limit($limitStart.','.$limit)->select();
		foreach ($list as $key => &$value) {
			$value['province_name'] = getRegionName($value['province']);
			$value['city_name'] = getRegionName($value['city']);
			$value['county_name'] = getRegionName($value['county']);
		}
		return $list;
	}
}
