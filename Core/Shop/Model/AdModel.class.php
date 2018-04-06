<?php
namespace Shop\Model;
use Think\Model;
class AdModel extends Model {
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
		// array('agent_name', 'require', '请填写公司名称！', 1, 'regex', 1),

		// 修改
		// array('agent_name', 'require', '请填写公司名称！', 1, 'regex', 2),
	);

	protected $_auto = array(
		// array('add_time', 'time', 1, 'function'),
	);

	/**
	 * [getAdList 获取广告位列表]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
	public function getAdList() {
		$field = '`id`, `image`, `type`, `content`, `url`';
		$sort = '`bidding_money` DESC, `sort` ASC, `id` DESC';
		$time = time();
		$where = "agent_id = '0' OR (agent_id != '0' AND '{$time}' BETWEEN start_time AND end_time)";
		$data = $this->where($where)->field($field)->order($sort)->limit(5)->select();
		return $data;
	}

	/**
	 * [getAdDetail 获取广告详情]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c) 2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $id [description]
	 * @return    [type]            [description]
	 */
	public function getAdDetail($id) {
		$data = $this->where(array('id'=> $id))->field('`content`')->find();
		return $data;
	}
}