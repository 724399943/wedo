<?php
namespace Shop\Model;
use Think\Model;
class FriendsModel extends Model {
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
	 * [getAddressList description]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
	public function getAddressList($parameter) {
		$userId = !empty($parameter['userId']) ? $parameter['userId'] : '';
		$limitStart = !empty($parameter['limitStart']) ? $parameter['limitStart'] : 0;
		$limit = !empty($parameter['limit']) ? $parameter['limit'] : 10;
		$where['from_id'] = $userId;
		$sql = "SELECT `u`.`id`, `u`.`nickname`, `u`.`headimgurl` 
				FROM `{$this->dbPrefix}friends` AS `f` 
				LEFT JOIN `{$this->dbPrefix}user` AS `u` ON `f`.`to_id` = `u`.`id` 
				WHERE `f`.`from_id` = '{$userId}' 
				LIMIT {$limitStart} , {$limit}";
		$list = $this->query($sql);
		return $list;
	}

	public function addFriend($from_id, $to_id) {
		$where = array(
			'from_id' => $from_id,
			'to_id' => $to_id
		);
		$friendData = $this->where($where)->find();
		$result = true;
		if ( empty($friendData) ) {
			$addData = $where;
			$addData['add_time'] = time();
			$result = ( $this->add($addData) !== false ) ? $result : false;
		}
		return $result;
	}
}