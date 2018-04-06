<?php
namespace Shop\Controller;
class ChatController extends BaseController {
    /**
     * [addressList 通讯录]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function addressList() {
        if ( IS_POST ) {
            $userId = session('userId');
            $friendsModel = D('Friends');
            $parameter = array(
                'userId' => $userId,
                'limitStart' => $this->limitStart,
                'limit' => PAGE_LIMIT
            );
            $returnData['list'] = $friendsModel->getAddressList($parameter);
            exit(statusCode($returnData));
        } else {
            $this->display();
        }
    }

    /**
     * [chatHistory 聊天历史]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function chatHistory() {
        if ( IS_POST ) {
            $userId = session('userId');
            $friendsModel = D('Friends');
            $userChatModel = D('UserChat');
            $parameter = array(
                'userId' => $userId,
                'limitStart' => $this->limitStart,
                'limit' => PAGE_LIMIT
            );
            $returnData['list'] = $friendsModel->getAddressList($parameter);
            $returnData['list'] = $userChatModel->getLatestChat($userId, $returnData['list']);
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [saveChat 保存聊天记录]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function saveChat() {
        if ( IS_POST ) {
            $userId = session('userId');
            $post = I('post.');
            // 保存通讯录
            $friendsModel = D('Friends');
            $post['to_id'] = str_replace('wedo', '', $post['to_id']);
            if ( !empty($post['to_id']) ) {
                $friendsModel->addFriend($userId, $post['to_id']);
                $friendsModel->addFriend($post['to_id'], $userId);
            }
            $userChatModel = D('UserChat');
            $addData = $userChatModel->create($post, 1);
            $addData['content'] = htmlspecialchars_decode($addData['content']);
            $addData['from_id'] = $userId;
            ( $userChatModel->add($addData) !== false ) ? 
                exit(statusCode()) : 
                exit(statusCode(array(), 100002));
        } else {
            exit(statusCode(array(), 100001));
        }
    }

    /**
     * [userChat 用户聊天记录]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function userChat() {
        if ( IS_POST ) {
            $userChatModel = D('UserChat');
            $parameter = array(
                'userId' => session('userId'),
                'to_id' => I('to_id', '', 'string'),
                'limitStart' => $this->limitStart,
                'limit' => PAGE_LIMIT
            );
            $returnData = $userChatModel->getUserChat($parameter);
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        }
    }
}