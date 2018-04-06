<?php
namespace Shop\Model;
use Think\Model;
class UserWithdrawModel extends Model {
	protected $_validate = array(
		// 新增
		array('money', 'require', '{%module_user_withdraw_amount}', 1, 'regex', 1),
		array('withdraw_type', 'require', '{%module_user_withdraw_methods}', 1, 'regex', 1),
		array('account', 'require', '{%module_user_account_number}', 1, 'regex', 1),
		array('truename', 'require', '{%module_user_actual_name}', 1, 'regex', 1),

	);

	protected $_auto = array(
		array('add_time', 'time', 1, 'function'),
	);

	/**
	 * [checkMoney 检测余额]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)     2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $userId [description]
	 * @param     [type]        $money  [description]
	 * @return    [type]                [description]
	 */
	public function checkMoney($userId, $money) 
	{
		$userModel = D('User');
		// 最低提现金额
        $minWithdrawMoney = C('minWithdrawMoney');
        // 最低保留金额
        $minRetainMoney = C('minRetainMoney');
        // 新注册商家赠送金额
        $registerGetMoney = C('registerGetMoney');
        $userInfo = $userModel->getUserInfo($userId, 'money');
        if ( $userInfo['money'] < $money ) {
        	return array(
        		'status' => '400000',
        		'message' => L('module_bidding_balance_issufficient')
        	);
        } elseif ( $money < $minWithdrawMoney ) {
        	return array(
        		'status' => '400000',
        		'message' => L('module_user_min_withdraw') . $minWithdrawMoney . L('_COMMON_GINGGIT_')
        	);
        } elseif ( ($userInfo['money'] - $money) < $minRetainMoney ) {
        	return array(
        		'status' => '400000',
        		'message' => L('module_user_min_retain') . $minRetainMoney . L('_COMMON_GINGGIT_')
        	);
        } elseif ( ($userInfo['money'] - $registerGetMoney) < $money ) {
        	return array(
        		'status' => '400000',
        		'message' => L('module_user_max_withdraw') . ($userInfo['money'] - $registerGetMoney) . L('_COMMON_GINGGIT_')
        	);
        }
        return array('status'=> '200000');
	}

	/**
	 * [setDecMoney 扣减金额]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)     2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $userId [description]
	 * @param     [type]        $money  [description]
	 */
	public function setDecMoney($userId, $money) {
		$userModel = D('User');
		if ( $userModel->where(array('id'=> $userId))->setDec('money', $money) !== false ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * [getWithdrawLog 提现记录]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $parameter [description]
	 * @return    [type]                   [description]
	 */
	public function getWithdrawLog($parameter) {
		$userId = !empty($parameter['userId']) ? $parameter['userId'] : 0;
		$type = !empty($parameter['type']) ? $parameter['type'] : 0;
		$page = !empty($parameter['page']) ? $parameter['page'] : 0;
		$limitStart = !empty($parameter['limitStart']) ? $parameter['limitStart'] : 0;
		$limit = !empty($parameter['limit']) ? $parameter['limit'] : 10;
		$where['user_id'] = $userId;
		$count = 0;
		if ( !empty($type) ) {
			$count = $this->where($where)->count();
		}
		$list = $this->where($where)->order('`id` DESC')->limit($limitStart.','.$limit)->select();
		$returnData['list'] = $list;
		$returnData['page'] = $page + 1;
		$returnData['count'] = ceil($count / $limit);
		return $returnData;
	}
}