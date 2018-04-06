<?php
namespace Shop\Model;
use Think\Model;

class UserCollectModel extends Model {
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
		// array('goods_id', 'require', '请输入商品名称！', 1, 'regex', 1),

		// 修改
		// array('nickname', 'require', '昵称不能为空！', 1, 'regex', 2),
	);

	/**
	 * [_auto 自动完成]
     * @author kofu <[418382595@qq.com]>
	 */
	protected $_auto = array(
		array('add_time', 'time', 1, 'function'),
	);

	/**
	 * [getCollectGoods 获取收藏商品]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)         2016          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $userId     [description]
	 * @param     integer       $limitStart [description]
	 * @param     integer       $limit      [description]
	 * @return    [type]                    [description]
	 */
	public function getCollectGoods($userId, $limitStart = 0, $limit = 10) {
		$sql = "SELECT `c`.`id`, `c`.`goods_id`, `g`.`goods_name`, `g`.`goods_price`, `g`.`goods_image`, `g`.`sale_number`, `g`.`is_auth` 
				FROM `{$this->dbPrefix}user_collect` AS `c` 
				LEFT JOIN `{$this->dbPrefix}goods` AS `g` ON `c`.`goods_id` = `g`.`id` 
				WHERE `c`.`user_id` = '{$userId}' 
				AND `c`.`type` = '0' 
				ORDER BY `c`.`id` DESC 
				LIMIT {$limitStart} , {$limit}";
		$list = M()->query($sql);
		return $list;
	}

	/**
	 * [getCollectAgent 获取收藏店铺]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)         2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $userId     [description]
	 * @param     integer       $limitStart [description]
	 * @param     integer       $limit      [description]
	 * @return    [type]                    [description]
	 */
	public function getCollectAgent($parameter) {
		$userId = $parameter['userId'];
		$longitude = !empty($parameter['longitude']) ? $parameter['longitude'] : '113.37763';
		$latitude = !empty($parameter['latitude']) ? $parameter['latitude'] : '23.13275';
		$limitStart = !empty($parameter['limitStart']) ? $parameter['limitStart'] : 0;
		$limit = !empty($parameter['limit']) ? $parameter['limit'] : 10;
		$meter = C('DISTANCE_METER');
		$agentModel = D('Agent');
		$distance = $agentModel->calc($longitude, $latitude, '`longitude`', '`latitude`');
		$sql = "SELECT `c`.`id`, `c`.`agent_id`, `a`.`agent_name`, `a`.`logo`, ". $distance ." AS `distance` 
				FROM `". $this->dbPrefix ."user_collect` AS `c` 
				LEFT JOIN `". $this->dbPrefix ."agent` AS `a` ON `c`.`agent_id` = `a`.`id` 
				WHERE `c`.`user_id` = '". $userId ."' 
				AND `c`.`type` = '1' 
				AND ". $distance ." < ". $meter ." 
				ORDER BY `distance` ASC, `c`.`id` DESC 
				LIMIT ". $limitStart ." , ". $limit;
		$list = M()->query($sql);
		foreach ($list as $key => &$value) {
			$value['distance']	= round($value['distance'] / 1000, 1);
		}
		return $list;
	}
}
