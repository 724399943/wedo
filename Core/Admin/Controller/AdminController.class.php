<?php
namespace Admin\Controller;
// 管理员控制器
class AdminController extends BaseController {
    private $adminModel;
    public function __construct() {
        parent::__construct();
        $this->adminModel = D('Admin');
    }

    /**
     * [adminList 管理员列表]
     * @author StanleyYuen <[350204080@qq.com]>
     */
    public function adminList() {
        $admin = M('admin');
        $count = $admin->count();
        $page  = new \Think\Page($count, 25);
        $show  = $page->show();
        $counting=$page->totalRows;
        $dbPrefix = C('DB_PREFIX');
        $sql 	  = "SELECT a.id, a.admin_account, a.is_lock, a.add_time, ag.title " . 
        	   		"FROM {$dbPrefix}admin AS a " . 
        	   		"LEFT JOIN {$dbPrefix}admin_auth_group_access AS aga ON a.id = aga.uid " . 
        	   		"LEFT JOIN {$dbPrefix}admin_auth_group AS ag ON aga.group_id = ag.id " . 
                    "ORDER BY a.id DESC " . 
                    "LIMIT {$page->firstRow}, {$page->listRows}";

        $adminList = $admin->query($sql);

        $this->assign('show', $show);
        $this->assign('adminList', $adminList);
        $this->assign('counting',$counting);
        $this->display('adminList');
    }
    // public function adminList() {
        
    // }
   /**
     * [addAdmin 添加管理员]
     * @author StanleyYuen <[350204080@qq.com]>
     */
    public function addAdmin() {
        if ( IS_POST ) {
            $mysql = M();
            $mysql->startTrans();
            $addData = $this->adminModel->create(I('post.'), 1);
            if ( !empty($addData) ) {
                $addData['admin_password'] = encrypt($addData['admin_password']);
                $uid = $this->adminModel->data($addData)->add();
                if ( $uid ) {
                    $group_id = I('group_id', '', 'string');
                    $groupData  = array(
                        'uid' => $uid,
                        'group_id' => $group_id
                    );
                    if ( !M('admin_auth_group_access')->data($groupData)->add() ) {
                        $mysql->rollback();
                    }

                    if ( $mysql->commit() ) {
                        $this->success('添加成功', U('Admin/adminList'));
                    } else {
                        $this->error('添加失败', U('Admin/adminList'));
                    }
                } else {
                    $mysql->rollback();
                    $this->error('添加失败', U('Admin/adminList'));
                }
            } else {
                $mysql->rollback();
                $this->error($this->adminModel->getError());
            }
        } else {
            $group = M('admin_auth_group')->select();
            $this->assign('group', $group);
            $this->display();
        }
    }

    /**
     * [delAdmin 删除管理员]
     * @author StanleyYuen <[350204080@qq.com]>
     */
    public function delAdmin() {
        $id = I('get.id', '', 'int');

        if ( empty($id) ) {
            $this->error('ID 参数丢失！');
        }

        if ( $id == '1' ) {
            $this->error('超级管理员不能被删除！');
        }

        $mysql    = M();
        $mysql->startTrans();

        if ( M('admin')->where(array('id'=>$id))->delete() ) {
            if ( M('admin_auth_group_access')->where(array('uid'=>$id))->delete() ) {
                $mysql->commit();
                $this->success('删除成功！', U('Admin/adminList'));
            } else {
                $mysql->rollback();
                $this->error('删除失败！', U('Admin/adminList'));
            }
        } else {
            $mysql->rollback();
            $this->error('删除失败！', U('Admin/adminList'));
        }
    }

    /**
     * [editAdmin 编辑管理员]
     * @author StanleyYuen <[350204080@qq.com]>
     */
    public function editAdmin() {
        if ( IS_POST ) {
            $id    = I('post.id', '', 'int');

            if ( empty($id) ) {
                $this->error('ID 参数丢失！');
            }


            $admin = D('admin');
            $data  = $admin->create(I('post.'), 2);
            if ( $id == '1' && $data['is_lock'] == '1' ) {
                $this->error('超级管理员不能被锁定登录！');
            }
            if ( empty($data) ) {
                $this->error($admin->getError());
            } else {
                unset($data['admin_account']);
                if (empty($data['admin_password'])) {
                    $this->error('密码不能为空！');
                }
                $password = $admin->where(array('id'=>$id))->getField('admin_password');
                if ( $data['admin_password'] == $password || encrypt($data['admin_password']) == $password) {
                    unset($data['admin_password']);
                } else {
                    $data['admin_password'] = encrypt($data['admin_password']);
                }
                $admin->where(array('id'=>$id))->data($data)->save();

                // 修改所属分组
                $groupId = I('post.group', '', 'int');
                M('admin_auth_group_access')->where(array('uid'=>$id))->data(array('group_id'=>$groupId))->save();

                $this->success('更新成功！', U('Admin/adminList'));
            }
        } else {
            $id = I('get.id', '', 'int');

            if ( empty($id) ) {
                $this->error('ID 参数丢失！');
            }

            $dbPrefix = C('DB_PREFIX');
            $sql = "SELECT a.*, aga.group_id " . 
                   "FROM {$dbPrefix}admin AS a " .
                   "LEFT JOIN {$dbPrefix}admin_auth_group_access AS aga ON a.id = aga.uid " . 
                   "WHERE a.id = {$id}";

            $adminInfo = M()->query($sql);
            $adminInfo[0]['count'] = substr($adminInfo[0]['admin_password'], 0,$adminInfo[0]['password_length']);
            $group     = M('admin_auth_group')->select();
            $this->assign('group', $group);
            $this->assign('adminInfo', $adminInfo[0]);
            $this->display('editAdmin');
        }
    }
}