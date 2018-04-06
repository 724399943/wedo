<?php
namespace Admin\Controller;
class FeedbackController extends BaseController {
    /**
     * [feedbackList 意见反馈列表]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function feedbackList() {
        $userFeedbackModel = M('user_feedback');
        $count = $userFeedbackModel->count();
        $page = new \Think\Page($count, 25);
        $show = $page->show();
        $counting = $page->totalRows;
        $list = $userFeedbackModel->limit($page->firstRow . ',' . $page->listRows)->order('`id` DESC')->select();
        foreach ($list as $key => &$value) {
            $value['nickname'] = getNickName($value['user_id']);
        }
        $this->assign('list', $list);
        $this->assign('show', $show);
        $this->assign('counting', $counting);
        $this->display();
    }

    /**
     * [delFeedback 删除意见反馈]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function delFeedback() {
        $ids = I('get.ids');
        if ( empty($ids) ) {
            $this->error('请选择要删除的数据');
        }
        $userFeedbackModel = M('user_feedback');
        $where['id'] = array('IN', trim($ids, ','));
        ( $userFeedbackModel->where($where)->delete() !== false ) ?    
            $this->success('删除成功') : 
            $this->error('删除失败');
    }
}