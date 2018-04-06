<?php
namespace Shop\Controller;
use Think\Controller;
use Plugins\Message\Message;
class MessageController extends BaseController {
    private $message;
    public function __construct() 
    {
        parent::__construct();
        $this->message = new Message();
    }

    /**
     * [index 消息中心]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function index() 
    {
        $userId = session('userId');
        $returnData['system'] = $this->message->getLatestMessage($userId, '0,1');
        $returnData['order'] = $this->message->getLatestMessage($userId, '2');
        $returnData['count'] = $this->message->getMessageCount($userId);
        if ( IS_POST ) {
            exit(statusCode($returnData));
        } else {
            $this->assign('returnData', $returnData);
            $this->display();
        }
    }

    /**
     * [systemMessage 系统消息]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function systemMessage() 
    {
        if ( IS_POST ) {
            $status = I('status', '0'); // 0：普通用户，1：商家
            $parameter = array(
                'userId'        => session('userId'),
                'messageType'   => ($status == '0') ? '0,1,3' : '0',
                'type'          => I('type', '0'),
                'page'          => $this->page,
                'limitStart'    => $this->limitStart,
                'limit'         => PAGE_LIMIT
            );
            $returnData = $this->message->getUserMessage($parameter);
            exit(statusCode($returnData));
        } else {
            $this->display();
        }
    }

    /**
     * [orderMessage 订单消息]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function orderMessage() 
    {
        if ( IS_POST ) {
            $parameter = array(
                'userId'    => session('userId'),
                'messageType' => '2',
                'page' => $this->page,
                'limitStart' => $this->limitStart,
                'limit' => PAGE_LIMIT
            );
            $returnData = $this->message->getUserMessage($parameter);
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [messageDetail 消息详情]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function messageDetail() 
    {
        if ( IS_POST ) {
            $id = I('id');
            $userId = session('userId');
            $data = $this->message->getMessageDetail($id, $userId);
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
     * [consultation 咨询]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function consultation() {
        $this->display();
    }
}