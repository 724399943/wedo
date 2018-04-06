<?php
namespace Shop\Model;
use Think\Model;
use Plugins\Money\Money;

class GoodsForEditModel extends Model {
	/**
	 * [_validate 自动验证]
     * @author kofu <[418382595@qq.com]>
	 */
	protected $_validate = array(

	);

	/**
	 * [_auto 自动完成]
     * @author kofu <[418382595@qq.com]>
	 */
	protected $_auto = array(
        array('add_time', 'time', 1, 'function'),
        array('order_sn', 'makeEditSn', 1, 'callback'),
	);

    /**
     * [payForEdit 支付商品编辑审核]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
     * @param     [type]        $parameter [description]
     * @return    [type]                   [description]
     */
	public function payForEdit($parameter) {
		$userId = $parameter['userId'];
		$order = $parameter['order'];
        $userMoney = M('user')->where(array('id'=> $userId))->getField('money');
        if ( $userMoney < $order['total'] ) {
        	return array(
        		'status' => '400000',
        		'message' => L('module_bidding_balance_issufficient'),
        	);
        }

        $result = true;
        $this->startTrans();
        $money = new Money();
        $parameter = array(
        	'from_id' => 0,
			'to_id' => $userId,
			'money' => $order['total'],
			'type' => 1,
			'eventType' => 10
        );
        if ( $money->money($parameter) === false ) {
        	$result = false;
        	return array(
        		'status' => '400000',
        		'message' => '扣除余额失败',
        	);
        }

        $order['is_pay'] = 1;
        $order['status'] = 1;
        if ( $this->save($order) === false ) {
        	$result = false;
        	return array(
        		'status' => '400000',
        		'message' => '更新订单失败',
        	);	
        }

        $this->saveGoodsData(json_decode($order['goods_data'], true), $order['agent_id']);

        if ( $result === true ) {
        	$this->commit();
        	return array(
        		'status' => '200000'
        	);
        } else {
        	$this->rollback();
        }
	}

    public function saveGoodsData($data, $agentId) {
        $goodsModel = D('Goods');
        if (!empty($data)) {
            $SKUattr = $data['SKUattr'];
            $SKUprice = $data['SKUprice'];
            $SKUnumber = $data['SKUnumber'];
            $goodsId = $data['id'];
            $data['agent_id'] = $agentId;
            $data['supplier_id'] = $agentId;
            // 获取商品信息
            $goodsData = $goodsModel->find($goodsId);
            
            $data['relevance_id'] = $goodsData['relevance_id'];
            // 保存商品图片
            $images = $data['images'];
            if (!empty($images)) {
                $goodsImages = $goodsModel->editGoodsImages($images, $goodsData);
                $data['goods_image'] = $goodsImages['goods_image'];
                $data['goods_images_id'] = $goodsImages['goods_images_id'];
            } else {
                // exit(statusCode(array(), 400000, L('_PC_GOODS_UPLOAD_PRODUCT_PICTURE_')));
            }
            // 添加商品详情
            $description = $data['description'];
            $extensionId = $goodsModel->editGoodsExtension($description, $goodsData);
            $data['goods_ext_id'] = $extensionId;
            // 处理SKU
            if ($SKUattr) {
                $skuGoods = $goodsModel->where(array('goods_main_id'=> $goodsId))->select();
                //往第一键插入数组
                array_unshift($skuGoods, $goodsData);
                if ( !empty($skuGoods) ) {
                    $goodsArr = array();
                    $skuCount = count($SKUattr);
                    $goodsModel->where(array('goods_main_id'=>$goodsId))->save(array('is_delete'=>'1'));
                    $goodsModel->save(array('id'=> $goodsId, 'is_delete'=> '1'));
                    $goodsMainId = $goodsId;
                    foreach ($SKUattr as $key => $value) {
                        $isRecover = false;
                        $thisGoods = '';
                        $value = trim($value,',');
                        $data['attr_list'] = ','. $value .',';
                        $relevanceAttr[] = str_replace(',', '-', $value);
                        foreach ($skuGoods as $v) {
                            if( strpos($v['attr_list'], $data['attr_list']) !== false ) {
                                $isRecover = true;
                                $thisGoods = $v['id'];
                                break;
                            }
                        }
                        $editData = array();
                        $editData['goods_price'] = $SKUprice[$key];
                        $editData['goods_number'] = $SKUnumber[$key];
                        $editData = array_merge($data, $editData);
                        $editData = $goodsModel->create($editData);
                        // 是否恢复已删除数据
                        if ($isRecover === false) {
                            unset($editData['id']);
                            $editData['add_time'] = time();
                            $goodsArr[$key] = $goodsModel->add($editData);
                        } else {
                            $goodsArr[$key] = $thisGoods;

                            $editData['id'] = $thisGoods;
                            $editData['is_delete'] = 0;
                            $goodsModel->save($editData);
                        }
                        $goodsMainId = ( strpos($data['attr_list'], $goodsData['attr_list']) !== false) ? $goodsMainId : 0; 
                    }
                    $goodsCount = count($goodsArr);
                    for( $i=0; $i<$goodsCount; $i++ ) {
                        $saveData['id'] = $goodsArr[$i];
                        if ( !empty($goodsMainId) ) {
                            $saveData['goods_main_id'] = ( $saveData['id'] == $goodsMainId ) ? 0 : $goodsMainId;
                        } else {
                            $saveData['goods_main_id'] = ( $i == 0 ) ? 0 : $goodsArr[0];
                        }
                        $goodsModel->save($saveData);
                    }
                    // 编辑商品关联
                    $goodsModel->editGoodsRelevance($goodsArr, $relevanceAttr, $goodsData);
                }
            } else {
                $data['is_delete'] = 1;
                $data = $goodsModel->create($data);
                $goodsModel->where(array('goods_main_id'=> $goodsId))->save(array('is_delete'=>'1'));
                $goodsModel->save($data);
                // ( $goodsModel->save($data) !== false ) ?
                //     exit(statusCode()) :
                //     exit(statusCode(array(), 100002));
            }
        } else {
            // exit(statusCode(array(), 100002));
        }
    }

    /**
     * [makeEditSn 生成订单号]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function makeEditSn() {
        // //订单生成规则
        $orderId = $this->field('max(id) as `maxId`')->find(); //得到订单的最大自增ID
        $tempStr = rand(1000, 9999) . $orderId['maxId'];        //生成随机数
        $masterOrder = C('CHANNEL') . date('ymd') . str_pad($tempStr, 10, "0", STR_PAD_LEFT);          //合并成订单号

        //如果有重复的订单号的话，则回滚
        if($this->where(array('order_sn'=> $masterOrder))->count() > 0) {
            return $this->makeOrderSn();
        }
        return $masterOrder;
    }
}
