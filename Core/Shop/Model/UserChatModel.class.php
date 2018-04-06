<?php
namespace Shop\Model;
use Think\Model;
class UserChatModel extends Model {
	/**
	 * [_validate 自动验证]
     * @author kofu <[418382595@qq.com]>
	 */
	protected $_validate = array(
		// 新增
		array('to_id', 'require', '请选择聊天用户！', 1, 'regex', 1),
		array('content', 'require', '请输入聊天内容！', 1, 'regex', 1),

		// 修改
		// array('title', 'require', '请输入求购标题！', 1, 'regex', 2),
	);

	/**
	 * [_auto 自动完成]
     * @author kofu <[418382595@qq.com]>
	 */
	protected $_auto = array(
		array('add_time', 'time', 1, 'function'),
	);

	/**
	 * [getUserChat 获取聊天记录]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $parameter [description]
	 * @return    [type]                   [description]
	 */
	public function getUserChat($parameter) {
		$userId = !empty($parameter['userId']) ? $parameter['userId'] : '';
		$to_id = !empty($parameter['to_id']) ? $parameter['to_id'] : '';
		$limitStart = !empty($parameter['limitStart']) ? $parameter['limitStart'] : 0;
		$limit = !empty($parameter['limit']) ? $parameter['limit'] : 10;
		$where['_string'] = " (`from_id` = '{$userId}' AND `to_id` = '{$to_id}') OR (`from_id` = '{$to_id}' AND `to_id` = '{$userId}') ";
		$returnData['list'] = $this->where($where)->field('`id`, `from_id`, `content`, `add_time`')->order('`add_time` DESC')->limit($limitStart.','.$limit)->select();
		$returnData['list'] = $this->processData($returnData['list'], $userId);
		// asort($returnData['list']);
		return $returnData;
	}

	public function processData($list, $userId) {
		$fromUser = array();
		$userModel = D('User');
		$agentModel = M('agent');
		foreach ($list as $key => &$value) {
			$value['content'] = json_decode($value['content'], true);
			if ( $value['from_id'] != $userId && empty($fromUser) ) {
				$fromUser = $userModel->getUserInfo($value['from_id'], '`headimgurl`, `nickname`');
				$fromUser['headimgurl'] = !empty($fromUser['headimgurl']) ? $fromUser['headimgurl'] : '/Static/Public/Wechat/images/default.png';
			}elseif( $value['from_id'] == $userId){
				$value['curUser'] = session('userInfo');
			}
			$value['fromUser'] = $fromUser;
		}
		// foreach ($list as $key => &$value) {
		// 	$value['content'] = json_decode($value['content'], true);
		// 	if ( $value['from_id'] != $userId && empty($fromUser) ) {
				// $fromUser = $userModel->getUserInfo($value['from_id'], '`headimgurl`, `nickname`');
		// 		$where['user_id'] = $value['from_id'];
		// 		$fromUser = $agentModel->where($where)->field('`agent_name`, `logo`')->find();
		// 		$value['agent_name'] = $fromUser['agent_name'];
		// 		$value['nickname'] = $fromUser['agent_name'];
		// 		$value['headimgurl'] = !empty($fromUser['logo']) ? $fromUser['logo'] : '/Static/Public/Wechat/images/default.png';
		// 	} 
		// 	// elseif ( $value['from_id'] == $userId ) {
		// 	// 	$value['curUser'] = session('userInfo');
		// 	// }
		// 	$value['fromUser'] = $fromUser;
		// }
		return $list;
	}

	/**
	 * [getLatestChat 获取最新聊天记录]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)     2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $userId [description]
	 * @param     [type]        $list   [description]
	 * @return    [type]                [description]
	 */
	public function getLatestChat($userId, $list) {
		foreach ($list as $key => &$value) {
			$where['_string'] = " (`from_id` = '{$userId}' AND `to_id` = '{$value['id']}') OR (`from_id` = '{$value['id']}' AND `to_id` = '{$userId}') ";
			$chatData = $this->where($where)->order('`id` DESC')->field('`content`, `add_time`')->find();
			$value['content'] = !empty($chatData['content']) ? json_decode($chatData['content']) : "";
			$value['add_time'] = !empty($chatData['add_time']) ? $chatData['add_time'] : "";
		}
		return $list;
	}
}
