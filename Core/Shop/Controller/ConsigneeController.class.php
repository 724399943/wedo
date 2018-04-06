<?php
namespace Shop\Controller;
use Think\Controller;

class ConsigneeController extends BaseController {	
	/**
	 * [userAllConsignee 用户所有地址]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
	public function userAllConsignee() 
	{
		if ( IS_POST ) {
			$userId = session('userId');
			$userConsignee = D('UserConsignee');
            $returnData['list'] = $userConsignee->getUserAllConsignee($userId, $this->limitStart, PAGE_LIMIT);
            exit(statusCode($returnData));
		} else {
			exit(statusCode(array(), 100001));
		}
	}

	/**
	 * [getConsignee 获取地址信息]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
	public function getConsignee() 
	{
		if ( IS_POST ) {
			$id = I('id');
			$userId = session('userId');
			$userConsignee = D('UserConsignee');
			$where = array(
				'id'		=> $id,
				'user_id'	=> $userId
			);
			$returnData['info'] = $userConsignee->where($where)->find();
			exit(statusCode($returnData));
		} else {
			exit(statusCode(array(), 100001));
		}
	}

	/**
	 * [addConsignee 添加地址]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c) 2016 Xcrozz (http://www.xcrozz.com)
	 */
	public function addConsignee() 
	{
		if ( IS_POST ) {
			$userId = session('userId');
			$userConsignee = D('UserConsignee');
			$data = $userConsignee->create(I('post.'), 1);
			if (!empty($data)) {
				// 如是第一次添加该地址，则默认第一个地址为默认收货地址
				if ($userConsignee->where(array('user_id'=> $userId))->count() == 0) {
					$data['is_default'] = '1';
				}
				$data['user_id'] = $userId;
				if ($userConsignee->add($data)) {
					exit(statusCode());
				} else {
					exit(statusCode(array(), 100002));
				}
			} else {
				exit(statusCode(array(), 400000, $userConsignee->getError()));
			}
		} else {
			exit(statusCode(array(), 100001));
		}
	}

	/**
	 * [editConsignee 编辑地址]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
	public function editConsignee() 
	{
		if ( IS_POST ) {
			$userModel = M('user');
			$userId = session('userId');
			$userConsignee = D('UserConsignee');
			$data = $userConsignee->create(I('post.'), 2);
			if (!empty($data)) {
				// if ($data['is_default'] == '1') {
				// 	$userConsignee->where(array('user_id'=> $userId))->save(array('is_default'=> '0'));
				// }
				if ($userConsignee->save($data) !== false) {
					// 如将该地址设置为默认地址，则更新用户表的默认地址id
					if ($data['is_default'] == '1') {
						$userModel->where(array('id'=> $userId))->save(array('default_id'=> $data['id']));
					}
					exit(statusCode());
				} else {
					exit(statusCode(array(), 100002));
				}
			} else {
				exit(statusCode(array(), 400000, $userConsignee->getError()));
			}
		} else {
			exit(statusCode(array(), 100001));
		}
	}

	/**
	 * [setToDefault 设为默认地址]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c) 2017 Xcrozz (http://www.xcrozz.com)
	 */
	public function setToDefault() 
	{
		if ( IS_POST ) {
			$id = I('id');
			$userModel = M('user');
			$userId = session('userId');
			$userConsignee = D('UserConsignee');
			$where = array(
				'id' => $id,
				'user_id' => $userId
			);
			if ( $userConsignee->where($where)->count() > 0 ) {
				$userConsignee->where(array('user_id'=> $userId))->save(array('is_default'=> '0'));
				$userConsignee->save(array(
					'id' => $id,
					'is_default' => '1'
				));
				// 如将该地址设置为默认地址，则更新用户表的默认地址id
				$userModel->save(array(
					'id' => $userId,
					'default_id'=> $id
				));
				exit(statusCode());
			} else {
				exit(statusCode(array(), 100002));
			}
		} else {
			exit(statusCode(array(), 100001));
		}
	}

	/**
	 * [delConsignee 删除地址]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
	public function delConsignee() 
	{
		if (IS_POST) {
			$id = I('id');
			$user = M('user');
			$userId = session('userId');
			$userConsignee = D('UserConsignee');
			$where = array(
				'id' => $id,
				'user_id' => $userId
			);
			$consignee = $userConsignee->where($where)->field('`is_default`')->find();
			if ( empty($consignee) ) {
				exit(statusCode(array(), 100002));
			}
			$result = true;
			$mysql = M();
			$mysql->startTrans();
			if ( $consignee['is_default'] == '1' ) {
				$map = array(
					'user_id' => $userId,
					'id' => array('NEQ', $id)
				);
				$latestConsignee = $userConsignee->where($map)->order('`id` DESC')->select();
				if ( !empty($latestConsignee) ) {
					if ( $userConsignee->save(array('id'=> $latestConsignee['0']['id'], 'is_default'=> '1')) === false ) {
						$result = false;
						exit(statusCode(array(), 100002));
					}

					if ( $user->save(array('id'=> $userId, 'default_id'=> $latestConsignee['0']['id'])) === false ) {
						$result = false;
						exit(statusCode(array(), 100002));	
					}
				}
			}
			if ( $userConsignee->where(array('id'=> $id, 'user_id'=> $userId))->delete() === false ) {
				$result = false;
				exit(statusCode(array(), 100002));
			}

			if ( $result === true ) {
				$mysql->commit();
				exit(statusCode());
			} else {
				$mysql->rollback();
			}
		} else {
			exit(statusCode(array(), 100001));
		}
	}
}