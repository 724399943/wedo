<?php
namespace Shop\Controller;
use Think\Controller;
use Plugins\Message\Message;
class MessageCheckController extends BaseController {
    private $messageCheck;
    public function __construct() 
    {
        parent::__construct();
        $this->messageCheck = D('MessageCheck');
    }

    /**
     * [issuedMessage 已发布的消息]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function issuedMessage() 
    {
        if ( IS_POST ) {
            $userId = session('userId');
            $parameter = array(
                'userId' => $userId,
                'type' => I('type', '0'),
                'page' => $this->page,
                'limitStart' => $this->limitStart,
                'limit' => PAGE_LIMIT
            );
            $returnData = $this->messageCheck->getIssuedMessage($parameter);
            exit(statusCode($returnData));
        } else {
            $this->display();
        }   
    }

    /**
     * [issuingMessage 发布消息]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function issuingMessage() 
    {
        if ( IS_POST ) {
            $data = $this->messageCheck->create(I('post.'), 1);
            if ( !empty($data) ) {
                $addData = array(
                    'total' => C('issuingPrice'),
                    'user_id' => session('userId'),
                    'agent_id' => session('agentId'),
                    'order_sn' => $this->messageCheck->makeOrderSn(),
                );
                $data = array_merge($data, $addData);
                ( $this->messageCheck->add($data) ) ? 
                    exit(statusCode(array('order_sn'=> $data['order_sn']))) : 
                    exit(statusCode(array(), 100002));
            } else {
                exit(statusCode(array(), 400000, $this->messageCheck->getError()));
            }
        } else {
            $this->display();
        }
    }

    /**
     * [chooseGoods 选择商品]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function chooseGoods() {
        $this->display();
    }

    /**
     * [payForIssuing 支付发布消息]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function payForIssuing() {
        $userId = session('userId');
        $order_sn = I('request.order_sn');
        $where = array(
            'user_id' => $userId,
            'order_sn' => $order_sn
        );
        $order = $this->messageCheck->where($where)->find();
        if ( empty($order) ) {
            ( IS_POST ) ? exit(statusCode(array(), 100002)) : $this->error('没有该订单');
        }
        if ( IS_POST ) {
            $parameter = array(
                'userId' => $userId,
                'order' => $order
            );
            $returnData = $this->messageCheck->payForIssuing($parameter);
            if ( $returnData['status'] == '400000' ) {
                exit(statusCode(array(), 400000, $returnData['message']));
            } else {
                exit(statusCode());
            }
        } else {
            $userInfo = D('User')->getUserInfo($userId, 'money');
            $this->assign('order', $order);
            $this->assign('userInfo', $userInfo);
            $this->display();
        }
    }

    /**
     * [issuingDetail 发布消息详情]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function issuingDetail() {
        if ( IS_POST ) {
            $id = I('id');
            $data = $this->messageCheck->getIssuingDetail($id);
            if ( !empty($data) ) {
                $returnData['list'] = $data;
                exit(statusCode($returnData));
            } else {
                exit(statusCode(array(), 100002));
            }
        } else {
            $this->display();
        }
    }

    /**
     * [photoUpload 图片上传]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function photoUpload() {
        // 图片保存路径
        fileUpload('MessageCheck/', function($e) {
            echo json_encode(array('error'=>0, 'url'=>trim($e['filePath'], '.')));
        });
    }
}