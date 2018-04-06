<?php
namespace Shop\Controller;
class PointOrderController extends BaseController {
    /**
     * [pointRecord 积分订单]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function pointOrder() {
        if ( IS_POST ) {
            $pointOrder = D('PointOrder');
            $parameter = array(
                'agentId' => session('agentId'),
                'limitStart' => $this->limitStart,
                'limit' => PAGE_LIMIT
            );
            $returnData['list'] = $pointOrder->pointOrder($parameter);
            exit(statusCode($returnData));
        } else {
            $this->display();
        }
    }

    /**
     * [pointOrderDetail 积分订单详情]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function pointOrderDetail() {
        if ( IS_POST ) {
            $pointOrder = D('PointOrder');
            $parameter = array(
                'order_sn' => I('order_sn'),
                'agentId' => session('agentId'),
            );
            $returnData['list'] = $pointOrder->orderDetail($parameter);
            exit(statusCode($returnData));
        } else {
            $this->display();
        }
    }
}