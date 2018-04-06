<?php
namespace Plugins\Point;
use Think\Controller;
/**
 * 积分
 */
class Point extends Controller {
    /**
     * [point description]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)     2017          Xcrozz (http://www.xcrozz.com)
     * @param     [type]        $from_id    [发起id]
     * @param     [type]        $to_id      [接受id]
     * @param     [type]        $num        [积分数量]
     * @param     integer       $type       [赠送/扣除（0：扣除  1：赠送）]
     * @param     [type]        $eventType  [事件类型（0：注册推荐人，1：买家新注册，2：买家消费，3：买家充值，4：买家评价，5：卖家完成订单，6：卖家回复评价，7：兑换商品，8：推荐有奖）]
     * @return    [type]                    [description]
     */
    public function point($parameter) {
        $fromId = $parameter['from_id'];
        $toId = $parameter['to_id'];
        $num = $parameter['num'];
        $type = $parameter['type'];
        $event_type = $parameter['event_type'];
        $order_sn = $parameter['order_sn'];
        $description = (!empty($parameter['description'])) ? $parameter['description'] : '';
        $user = M('user');
        $pointLog = M('point_log');
        if( $type == 0 ) {
            $state = $user->where(array('id'=> $toId))->setDec('point', $num);
        } else {
            $state = $user->where(array('id'=> $toId))->setInc('point', $num);
        }
        if( $state !== false ) {
            $data = array(
                'from_id'       => $fromId,
                'to_id'         => $toId,
                'type'          => $type,
                'num'           => $num,
                'event_type'    => $event_type,
                'add_time'      => time()
            );

            if ( !empty($order_sn) ) {
                $data['order_sn'] = $order_sn;
            }

            if ( !empty($description) ) {
                $data['description'] = $description;
            }

            $pointLog->add($data);
            return true;
        } else {
            return false;
        }
    }

    private function eventType($event_type, $point, $type) {
        $eventType = array(
            '0'     => ( empty($type) ? '注册推荐人+' : '注册推荐人-' ) . $point . '积分',
            '1'     => ( empty($type) ? '注册+' : '注册-' ) . $point . '积分',
            '2'     => ( empty($type) ? '购物+' : '购物-' ) . $point . '积分',
            '3'     => ( empty($type) ? '充值+' : '充值-' ) . $point . '积分',
            '4'     => ( empty($type) ? '评价+' : '评价-' ) . $point . '积分',
            '5'     => ( empty($type) ? '卖家完成订单+' : '卖家完成订单-' ) . $point . '积分',
            '6'     => ( empty($type) ? '卖家回复评价+' : '卖家回复评价-' ) . $point . '积分',
            '7'     => ( empty($type) ? '兑换商品+' : '兑换商品-' ) . $point . '积分',
            '8'     => ( empty($type) ? '推荐+' : '推荐-' ) . $point . '积分',
        );
        return $eventType[$event_type];
    }

    /**
     * [getPointLog 获取积分log]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
     * @param     [type]        $parameter [description]
     * @return    [type]                   [description]
     */
    public function getPointLog($parameter) {
        $userModel = M('user');
        $to_id = !empty($parameter['to_id']) ? $parameter['to_id'] : '';
        $type = !empty($parameter['type']) ? $parameter['type'] : 0;
        $page = !empty($parameter['page']) ? $parameter['page'] : 0;
        $limitStart = !empty($parameter['limitStart']) ? $parameter['limitStart'] : 0;
        $limit = !empty($parameter['limit']) ? $parameter['limit'] : 10;
        $eventType = !empty($parameter['eventType']) ? $parameter['eventType'] : '';
        $pointLog = M('point_log');
        $where['to_id'] = $to_id;
        if ( !empty($eventType) ) {
            $where['event_type'] = $eventType;
        }
        $count = 0;
        if ( !empty($type) ) {
            $count = $pointLog->where($where)->count();
        }
        $list = $pointLog->where($where)->order('`id` DESC')->limit($limitStart.','.$limit)->select();
        // $language = $userModel->where(array('id'=>$to_id))->getField('language');
        $language = session('language');
        foreach ($list as $key => &$value) {
            if($language == 'zh-cn') {
                $value['event'] = $this->eventType($value['event_type'], $value['num'], $value['type']);
            } else{
                $value['event'] = $value['description'];
            }
            // $value['event'] = $this->eventType($value['event_type'], $value['num']);
            // $value['event'] = $value['description'];
        }
        $returnData['list']     = $list;
        $returnData['page']     = $page + 1;
        $returnData['count']    = ceil($count / $limit);
        return $returnData;
    }
}