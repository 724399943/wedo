<?php
namespace Shop\Controller;
use Think\Controller;

class GoodsCommentController extends BaseController {
	/**
	 * [goodsComment 商品评论列表]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
	public function goodsComment() {
		if ( IS_POST ) {
			$goodsComment = D('GoodsComment');
			$parameter = array(
				'goods_id'		=> I('goods_id'),
				'page'			=> $this->page,
				'limitStart'	=> $this->limitStart,
				'limit'			=> PAGE_LIMIT
			);
			$returnData = $goodsComment->getComment($parameter);
			exit(statusCode($returnData));
		} else {
			$this->display();
		}
	}

	/**
	 * [addGoodsComment 添加商品评论]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c) 2017 Xcrozz (http://www.xcrozz.com)
	 */
	public function addGoodsComment() {
		if ( IS_POST ) {
			$userId = session('userId');
			$goodsId = I('goods_id');
			$orderSn = I('order_sn');
			$goodsComment = D('GoodsComment');
			$data = $goodsComment->create(I('post.'), 1);
			if ( !empty($data) ) {
				$result = true;
				$mysql = M();
				$mysql->startTrans();
				$agent = M('agent');
				$goods = M('goods');
				$order = M('order');
				$orderDetail = M('order_detail');
				$goodsComment = M('goods_comment');
				$where = array(
					'`o`.`user_id`'	=> $userId,
					'`o`.`is_pay`'	=> '1',
					'`o`.`delivery_status`'	=> '1',
					'`o`.`received`'	=> '1',
					'`o`.`order_sn`'	=> $orderSn,
					'`od`.`goods_id`'	=> $goodsId,
				);
				$orderData = $orderDetail->join(' AS `od` LEFT JOIN `' . C('DB_PREFIX') . 'order` AS `o` ON `od`.`order_sn` = `o`.`order_sn`')
										  ->where($where)
										  ->field('`od`.`id`, `od`.`attr_list`, `o`.`supplier_id`, `o`.`order_sn`, `o`.`id` AS `order_id`')
										  ->find();
				if ( empty($orderData) ) {
					exit(statusCode(array(), 100002));
				}
				// 保存图片
				$goodsCommentImage = M('goods_comment_image');
				$photo = I('photo', '', 'htmlspecialchars_decode');
				$photo = !empty($photo) ? json_decode($photo, true) : '';
				$imageArray = array();
				foreach ($photo as $key => $value) {
					$imageArray[] = $goodsCommentImage->add($value);
				}

				$data['agent_id'] = $orderData['supplier_id'];
				$data['user_id'] = $userId;
				$data['image_ids'] = implode(',', $imageArray);
				if ( !empty($orderData['attr_list']) ) {
					$data['attr_list'] = $orderData['attr_list'];
				}
				$user = M('user')->field('`nickname`, `headimgurl`')->find($userId);
				$data = array_merge($data, $user);
				if ( $goodsComment->add($data) === false ) {
					$result = false;
					exit(statusCode(array(), 100002));
				}

				if ( $orderDetail->save(array('id'=> $orderData['id'], 'is_comment'=> '1')) === false ) {
					$result = false;
					exit(statusCode(array(), 100002));
				}
				
				// 更新主单状态
				if ( $orderDetail->where(array('total_sn'=> $orderData['order_sn'], 'is_comment'=> '0'))->count() <= 0 ) {
					$saveData = array(
						'id' => $orderData['order_id'],
						'is_comment' => '1',
						'status' => '4'
					);
					if ( $order->save($saveData) === false ) {
						$result = false;
						exit(statusCode(array(), 100002));
					}
				}

				$goodsData = $goods->where(array('id'=> $goodsId))->field('supplier_id,goods_main_id')->find();
				$goodsArr = ( !empty($goodsData['goods_main_id']) ) ? [$goodsData['goods_main_id'], $goodsId] : [$goodsId];
				if ( $goods->where(array('id'=> array('IN', $goodsArr)))->setInc('comment_number') === false ) {
					$result = false;
					exit(statusCode(array(), 100002));
				}

				// 计算店铺评分
				$supplier_id = $goodsData['supplier_id'];
				$supplierComment = $goodsComment->where(array('agent_id'=> $supplier_id))->field('COUNT(*) AS `count`, SUM(`star`) AS `total`')->find();
				$saveStar = floor($supplierComment['total']/($supplierComment['count'] * 5) * 5);
				if ( $agent->save(array('id'=> $supplier_id, 'star'=> $saveStar)) === false ) {
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
				exit(statusCode(array(), 400000, $goodsComment->getError()));
			}
		} else {
			exit(statusCode(array(), 100001));
		}
	}
}