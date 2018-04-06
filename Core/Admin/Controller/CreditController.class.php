<?php
namespace Admin\Controller;
class CreditController extends BaseController {
    /**
     * [creditSetting 扣减/增加信用规则设置]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function creditSetting() {
        if ( IS_POST ) {
            $config = M('config');
            $data   = I('post.config');
            foreach ($data as $key => $value) {
                $saveData = array();
                $saveData['config_value'] = $value['config_value'];
                $config->where(array('config_sign'=>$value['config_sign']))->save($saveData);
            }
            $this->success('更新成功！');
        } else {
            $configList = M('config')->select();
            $this->assign('configList', $configList);
            $this->display();
        }
    }

    /**
     * [userCredit 用户信用]
     * @author shichun <672517056@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function userCredit() {
        $userModel = M('user');
        $creditLogModel = M('user_credit_log');
        if ( IS_POST ) {
            $id = I('id', '', 'string');
            $surplus_credit = I('credit', '', 'string');
            $userCredit = $userModel->where(array('id'=> $id))->getField('`credit`');
            $change_type = ( $userCredit > $surplus_credit ) ? 1 : 0;
            $credit = ( $userCredit > $credit ) ? $userCredit - $surplus_credit : $surplus_credit - $userCredit;
            $addData = array(
                'user_id' => $id,
                'credit_type' => '0',
                'change_type' => $change_type,
                'credit' => $credit,
                'surplus_credit' => $surplus_credit,
                'add_time' => time(),
            );
            if ( $creditLogModel->add($addData) !== false ) {
                $userModel->where(array('id'=> $id))->save(array('credit'=> $surplus_credit));
                exit(statusCode());
            } else {
                exit(statusCode(array(), 400000, '修改失败'));
            }
        } else {
            $dbPrefix = C('DB_PREFIX');
            $nickname = I('get.nickname', '');
            $show = '';
            $list = '';
            $userInfo = '';
            $counting = 0;
            if ( !empty($nickname) ) {
                $userInfo = $userModel->where(array('nickname'=> array('LIKE', "%{$nickname}%")))->find();
                if ( empty($userInfo) ) {
                    $this->error('没有搜索到相关账号', U('Credit/userCredit'));
                }
                $where['user_id'] = $userInfo['id'];
                $count = $creditLogModel->where($where)->count();
                $page = new \Think\Page($count, 25);
                $page->setConfig('link', '/Admin/Credit/userCredit/p/zz/nickname/' . $nickname);
                $show = $page->show();
                $counting = $page->totalRows;
                $list = $creditLogModel->where($where)->order('`id` DESC')->limit($limitStart.','.$limit)->select();
            }

            $this->assign('list', $list);
            $this->assign('show', $show);
            $this->assign('nickname', $nickname);
            $this->assign('userInfo', $userInfo);
            $this->assign('counting', $counting);
            $this->display();
        }
    }
}