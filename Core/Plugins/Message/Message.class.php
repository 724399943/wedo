<?php
namespace Plugins\Message;
use Think\Controller;
/**
 * 站内信
 */
class Message extends Controller {
    /**
     * [Sendmessage 指定用户发送消息]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)        2016          Xcrozz (http://www.xcrozz.com)
     * @param     [type]        $parameter [title：标题, message_type：消息类型, content：内容, receiver_id：被推送者ID, user_id：主播ID]
     */
    public function Sendmessage($parameter) {
        $parameter['title']     = !empty($parameter['title']) ? $parameter['title'] : '';
        $parameter['content']   = !empty($parameter['content']) ? $parameter['content'] : '';
        $parameter['type']      = !empty($parameter['type']) ? $parameter['type'] : 0;
        $parameter['agent_id']  = !empty($parameter['agent_id']) ? $parameter['agent_id'] : 0;
        $parameter['goods_id']  = !empty($parameter['goods_id']) ? $parameter['goods_id'] : 0;
        $parameter['order_sn']  = !empty($parameter['order_sn']) ? $parameter['order_sn'] : '';
        $parameter['image']     = !empty($parameter['image']) ? $parameter['image'] : '';
        $message = M('message');
        $messageData  = array(
            'title'         => $parameter['title'],
            'content'       => $parameter['content'],
            'type'          => $parameter['type'],
            'message_type'  => $parameter['message_type'],  //消息类型
            'agent_id'      => $parameter['agent_id'],  
            'goods_id'      => $parameter['goods_id'],  
            'order_sn'      => $parameter['order_sn'],  
            'add_time'      => time(),
        );
        if ( !empty($parameter['image']) ) {
            $messageData['image'] = $parameter['image'];
        }
        if ( $message_id = $message->add($messageData) ) {
            if ($parameter['type'] != 0) {
                $relevance = M('message_relevance');
                $relevanceData  = array(
                    'message_id'    => $message_id,
                    'receiver_id'   => $parameter['receiver_id'],
                    'type'          => $parameter['type'],
                    'add_time'      => time(),
                );
                $result = ($relevance->add($relevanceData)) ? true : false;
            } else {
                $result = true;
            }
            return $result;
        } else {
            return false;
        }
    }

    /**
     * [getMessage 用户登录获取系统消息]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)     2016          Xcrozz (http://www.xcrozz.com)
     * @param     [type]        $userId [description]
     * @return    [type]                [description]
     */
    public function getMessage($userId){
        $message   = M('message');
        $relevance = M('message_relevance');
        $where = array(
            'type'          => '0',
            'receiver_id'   => $userId
        );
        $newestTime = $relevance->where($where)->field('add_time')->order('id DESC')->find();
        $map['type'] = '0';
        if ( !empty($newestTime) ) {
            $map['add_time'] = array('gt', $newestTime['add_time']);
        }

        $list = $message->where($map)->field('`id`, `message_type`, `condition`, `add_time`')->select();
        if (!empty($list)) {
            foreach ($list as $key => $value) {
                $isCanGet = true;
                switch ( $value['message_type'] ) {
                    case '1':
                    case '2':
                        if ( strpos($value['condition'], ',' . $userId . ',') === false ) {
                            $isCanGet = false;
                        }
                        break;
                }

                if ( $isCanGet == true ) {
                    $relevanceData = array(
                        'message_id'    => $value['id'],
                        'receiver_id'   => $userId,
                        'is_read'       => 0,
                        'type'          => 0,
                        'add_time'      => $value['add_time'],
                    ); 
                    $relevance->add($relevanceData);
                }
            }
        }
        return true;
    }

    /**
     * [getUserMessage 获取消息列表]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)        2016          Xcrozz (http://www.xcrozz.com)
     * @param     [type]        $parameter [userId：用户ID, message_type：消息类型, limitStart：分页起始值, limit：偏移量, page：当前页码]
     * @return    [type]                   [description]
     */
    public function getUserMessage($parameter) {
        $type = !empty($parameter['type']) ? $parameter['type'] : '0';
        $messageType = !empty($parameter['messageType']) ? $parameter['messageType'] : '0';
        $userId = !empty($parameter['userId']) ? $parameter['userId'] : '0';
        $page = !empty($parameter['page']) ? $parameter['page'] : 0;
        $limitStart = !empty($parameter['limitStart']) ? $parameter['limitStart'] : 0;
        $limit = !empty($parameter['limit']) ? $parameter['limit'] : 10;
        $dbPrefix = C('DB_PREFIX');
        $sql = "SELECT `m`.`id`, `m`.`agent_id`, `m`.`order_sn`, `m`.`goods_id`, `m`.`title`, `m`.`image`, `m`.`type`, `m`.`content`, `m`.`message_type`, `m`.`add_time`, `mr`.`receiver_id`, `mr`.`is_read` 
                FROM `". $dbPrefix ."message_relevance` AS `mr` 
                LEFT JOIN `". $dbPrefix ."message` AS `m` ON `mr`.`message_id` = `m`.`id` 
                WHERE `m`.`message_type` IN(". $messageType .") 
                AND `mr`.`receiver_id` = '". $userId ."' 
                AND `m`.`is_delete` = '0' 
                ORDER BY `mr`.`id` DESC  
                LIMIT ". $limitStart ." , ". $limit;
        $list = M()->query($sql);
        $count = 0;
        if ( !empty($type) ) {
            $sql = "SELECT COUNT(*) AS `count` 
                    FROM `". $dbPrefix ."message_relevance` AS `mr` 
                    LEFT JOIN `". $dbPrefix ."message` AS `m` ON `mr`.`message_id` = `m`.`id` 
                    WHERE `m`.`message_type` IN(". $messageType .") 
                    AND `mr`.`receiver_id` = '". $userId ."' 
                    AND `m`.`is_delete` = '0'";
            $count = M()->query($sql);
            $count = !empty($count[0]['count']) ? $count[0]['count'] : 0;
        }
        $this->processData($list);
        $returnData['list']     = $list;
        $returnData['count']    = ceil($count / $limit);
        $returnData['page']     = $page + 1;
        return $returnData;
    }

    private function processData(&$list) {
        $goods = M('goods');
        $agent = M('agent');
        $userModel = M('user');
        $webSite = trim(C('webSite'), '/');
        $language = $userModel->where(array('id'=>$list[0]['receiver_id']))->getField('language');
        foreach ($list as $key => &$value) {
            switch ( $value['message_type'] ) {
                case '1' :
                    $agentInfo = $agent->field('`agent_name`')->find($value['agent_id']);
                    $value = array_merge($value, $agentInfo);
                    break;
                case '2' :
                    if($language == 'zh-cn'){
                        $value['title'] = "您有一笔订单已发货";
                        $value['content'] = "亲爱的顾客，您的订单{$value['order_sn']}已经发货啦，详情可在订单中心查看！";
                    }else{
                        $value['title'] = "Your order has been shipped";
                        $value['content'] = "Dear customer, your order {$value['order_sn']} has been shipped and details can be viewed in the order center";
                    }
                    break;
                case '3' :
                    $agentInfo = $agent->field('`agent_name`')->find($value['agent_id']);
                    $goodsInfo = $goods->field('`goods_name`, `goods_image`, `goods_price`')->find($value['goods_id']);
                    $value = array_merge($value, $goodsInfo, $agentInfo);
                    break;
            }
            $value['content'] = str_replace('/Static/Uploads/', $webSite . '/Static/Uploads/', $value['content']);
        }
    }

    // public function getUserMessage($parameter) {
    //     $dbPrefix = C('DB_PREFIX');
    //     $sql = "SELECT COUNT(*) AS `count` 
    //             FROM `". $dbPrefix ."message_relevance` AS `mr` 
    //             LEFT JOIN `". $dbPrefix ."message` AS `m` ON `mr`.`message_id` = `m`.`id` 
    //             WHERE `m`.`message_type` IN(". $parameter['messageType'] .") 
    //             AND `mr`.`receiver_id` = '". $parameter['userId'] ."' 
    //             AND `m`.`is_delete` = '0'";
    //     $count = M()->query($sql);
    //     $count = (!empty($count[0]['count'])) ? $count[0]['count'] : 0;

    //     $sql = "SELECT `m`.`id`, `m`.`title`, `m`.`type`, `m`.`content`, `m`.`message_type`, `mr`.`receiver_id`, `mr`.`add_time`, `mr`.`is_read` 
    //             FROM `". $dbPrefix ."message_relevance` AS `mr` 
    //             LEFT JOIN `". $dbPrefix ."message` AS `m` ON `mr`.`message_id` = `m`.`id` 
    //             WHERE `m`.`message_type` IN(". $parameter['messageType'] .") 
    //             AND `mr`.`receiver_id` = '". $parameter['userId'] ."' 
    //             AND `m`.`is_delete` = '0' 
    //             ORDER BY `mr`.`id` DESC  
    //             LIMIT {$parameter['limitStart']} , {$parameter['limit']}";
    //     $messageList = M()->query(X$sql);
    //     $data['messageList']    = $messageList;
    //     $data['count']          = ceil($count / $parameter['limit']);
    //     $data['page']           = $parameter['page'] + 1;  
    //     $data['pageCount']      = $parameter['limit']; 
    //     return $data;
    // }

    /**
     * [getLatestMessage description]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function getLatestMessage($userId, $messageType) {
        $dbPrefix = C('DB_PREFIX');
        $sql = "SELECT `m`.`id`, `m`.`title`, `m`.`add_time`, `mr`.`is_read` 
                FROM `". $dbPrefix ."message_relevance` AS `mr` 
                LEFT JOIN `". $dbPrefix ."message` AS `m` ON `mr`.`message_id` = `m`.`id` 
                WHERE `m`.`message_type` IN(". $messageType .") 
                AND `mr`.`receiver_id` = '". $userId ."' 
                AND `m`.`is_delete` = '0' 
                ORDER BY `mr`.`add_time` DESC 
                LIMIT 1";
        $message = M()->query($sql);
        $message = !empty($message[0]) ? $message[0] : null;
        return $message;
    }

    /**
     * [getMessageDetail 获取消息详情]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c) 2017          Xcrozz (http://www.xcrozz.com)
     * @param     [type]        $id [description]
     * @return    [type]            [description]
     */
    public function getMessageDetail($id, $userId) {
        $message = M('message');
        $data = $message->field('`title`, `content`, `add_time`')->find($id);
        // 处理图片地址
        $webSite = trim(C('webSite'), '/');
        $data['content'] = str_replace('/Static/Uploads/', $webSite . '/Static/Uploads/', $data['content']);
        if ( !empty($userId) ) {
            $messageRelevanceModel = M('message_relevance');
            $messageData = $messageRelevanceModel->where(array('message_id'=> $id, 'receiver_id'=> $userId))->find();
            if ( !empty($messageData) && empty($messageData['is_read']) ) {
                $messageRelevanceModel->save(array('id'=> $messageData['id'], 'is_read'=> '1'));
            }
        }
        return $data;
    }

    public function getMessageCount($userId) {
        $dbPrefix = C('DB_PREFIX');
        $messageRelevanceModel = M('message_relevance');
        $where = [
            'mr.receiver_id' => $userId,
            'mr.is_read' => '0',
            'm.message_type' => ['IN', [0,1]]
        ];
        $count = $messageRelevanceModel->alias('`mr`')
                                       ->join("LEFT JOIN `{$dbPrefix}message` AS `m` ON `mr`.`message_id` = `m`.`id`")
                                       ->where($where)
                                       ->count();
        return $count;
    }
}