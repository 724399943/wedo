<?php
namespace Admin\Controller;
use Plugins\Huanxin\Easemob;
// 消息中心控制器
class MessageController extends BaseController {
    /**
     * [sendMessage 发送消息]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function sendMessage(){
        if (IS_POST) {
            $user = M('user');
            $messageModel = D('Message');
            $messageData  = $messageModel->create(I('post.'), 1);
            if ( empty($messageData) ) {
                $this->error($messageModel->getError());
            } else {
                if ( empty($messageData['condition']) ) {
                    $this->error('请选择推送用户');
                }
                $messageData['content'] = htmlspecialchars_decode($messageData['content']);
                $messageData['condition'] = implode(',', $messageData['condition']);

                if ( $id = $messageModel->add($messageData) ) {
                    $userModel = M('user');
                    if ( strpos($messageData['condition'], '0') !== false || strpos($messageData['condition'], '1') !== false ) {
                        $list = $userModel->getField('id', true);
                    } elseif ( strpos($messageData['condition'], '0') == false || strpos($messageData['condition'], '1') !== false ) {
                        $list = $userModel->where(array('status'=> '1'))->getField('id', true);
                    } elseif ( strpos($messageData['condition'], '0') !== false || strpos($messageData['condition'], '1') == false ) {
                        $list = $userModel->where(array('status'=> '0'))->getField('id', true);
                    }
                    foreach ($list as &$value) {
                        $value = 'wedo'.$value;
                    }
                    // 推送环信
                    $options = imConf();
                    $Easemob = new Easemob($options);
                    $ext = new \stdClass();
                    $ext->type = '8';
                    $ext->id = $id;
                    $Easemob->sendText('wedoAdmin', 'users', $list, $messageData['title'], $ext);

                    $this->success('推送成功！', U('Message/messageList'));
                } else {
                    $this->error('推送失败！');
                }
            }   
        } else {
            $this->display();
        }
    }

    /**
     * [messageList 消息列表]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function messageList(){
        $message = M('message');
        $title = I('get.title', '', 'urldecode');
        $link_parameter = '';
        if ( !empty($title) ) {
            $where['title'] = array('LIKE', "%{$title}%");
            $link_parameter .= '/title/' . $title;
        }
        $where['is_delete'] = '0';
        $count = $message->where($where)->count();
        $page = new \Think\Page($count, 25);
        $page->setConfig('link', '/Admin/Message/messageList/p/zz' . $link_parameter);
        $show = $page->show();
        $counting = $page->totalRows;
        $list = $message->where($where)->limit($page->firstRow , $page->listRows)->order('`id` DESC')->select();

        $this->assign('title', $title);
        $this->assign('list', $list);
        $this->assign('show', $show);
        $this->assign('counting', $counting);
        $this->display();
    }

    /**
     * [messageDetail 查看消息详情]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function messageDetail() {
        $id = I('get.id');
        if ( empty($id) ) {
            $this->error('没有该消息详情');
        }
        $messageModel = M('message');
        $vo = $messageModel->find($id);
        $this->assign('vo', $vo);
        $this->display();
    }

    /**
     * [delMessage 删除消息]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function delMessage() {
        $id = I('get.id');
        if ( empty($id) ) {
            $this->error('没有该消息详情');
        }
        $messageModel = M('message');
        ( $messageModel->where(array('id'=> $id))->save(array('is_delete'=> '1')) !== false ) ? 
            exit($this->success('删除成功')) : 
            exit($this->error('删除失败'));
    }

    /**
     * [messageCheckList 商家信息推送审核]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function messageCheckList() {
        $messageCheck = M('message_check');
        $agent_name = I('get.agent_name', '', 'urldecode');
        $status = I('get.status', '-1');
        $whereStr = "`mc`.`is_pay` = '1'";
        $link_parameter = '';
        $dbPrefix = C('DB_PREFIX');
        if ( $status != '-1' ) {
            $whereStr .= " AND `mc`.`status` = '{$status}'";
            $link_parameter .= '/status/' . $status;
        }
        if ( !empty($agent_name) ) {
            $whereStr .= " AND `a`.`agent_name` LIKE '%{$agent_name}%'";
            $link_parameter .= '/agent_name/' . $agent_name;
        }
        $where['is_pay'] = '1';
        $count = $messageCheck->join("AS `mc` LEFT JOIN `{$dbPrefix}agent` AS `a` ON `a`.`id` = `mc`.`agent_id`")
                              ->where($whereStr)
                              ->count();
        $page   = new \Think\Page($count, 20);
        $page->setConfig('link', '/Admin/Message/messageCheckList/p/zz' . $link_parameter);
        $show   = $page->show();
        $counting = $page->totalRows;
        $list = $messageCheck->join("AS `mc` LEFT JOIN `{$dbPrefix}agent` AS `a` ON `a`.`id` = `mc`.`agent_id`")
                             ->where($whereStr)
                             ->field('`a`.`agent_name`, `mc`.*')
                             ->limit($page->firstRow , $page->listRows)
                             ->order('`id` DESC')->select();
        foreach ($list as $key => &$value) {
            $value['goods_name'] = getGoodsname($value['goods_id']);
        }
        $return = array(
            'status' => $status,
            'agent_name' => $agent_name
        );
        $this->assign('list', $list);
        $this->assign('show', $show);
        $this->assign('return', $return);
        $this->assign('counting', $counting);
        $this->display();
    }

    /**
     * [checkMessage 审核推送信息]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function checkMessage() {
        $ids = I('ids', '');
        $status = I('status', '1');
        if ( empty($ids) || empty($status) ) {
            exit(statusCode(array(), 400000, '请选择要审核的数据'));
        }
        $messageCheckModel = M('message_check');
        $where = array(
            'id' => array('IN', trim($ids, ',')),
            'status' => array('EQ', '0')
        );
        $messageList = $messageCheckModel->where($where)->select();
        if ( $messageCheckModel->where($where)->save(array('status'=> $status)) !== false ) {
            // 推送商品消息
            if ( $status == '1' ) {
                $messageModel = M('message');
                $userCollectModel = M('user_collect');
                foreach ($messageList as $key => $value) {
                    $where = array(
                        'agent_id'=> $value['agent_id'],
                        'type' => '1'
                    );
                    $fansData = $userCollectModel->where($where)->select();
                    $condition = ',' . implode(',', array_column($fansData, 'user_id')) . ',';
                    $addData = array(
                        'agent_id' => $value['agent_id'],
                        'content' => $value['content'],
                        'image' => $value['image'],
                        'goods_id' => $value['goods_id'],
                        'type' => '0',
                        'message_type' => '1',
                        'condition' => $condition,
                        'add_time' => time()
                    );
                    $messageId = $messageModel->add($addData);

                    $list = array();
                    foreach ($fansData as &$val) {
                        $list[] = 'wedo'.$val['user_id'];
                    }
                    // 推送环信
                    $options = imConf();
                    $Easemob = new Easemob($options);
                    $ext = new \stdClass();
                    $ext->type = '8';
                    $ext->id = $messageId;
                    $Easemob->sendText('wedoAdmin', 'users', $list, 'You receive a merchant message', $ext);
                }
            }
            exit(statusCode());
        } else {
            exit(statusCode(array(), 400000, '审核失败'));
        }
    }

    // 图片上传使用
    public function photoUpload() {
        // 图片保存路径
        fileUpload('Message/', function($e) {
            echo json_encode(array('error'=>0, 'url'=>trim($e['filePath'], '.')));
        });
    }
}