<?php
namespace Admin\Controller;
use Think\Controller;
// 管理员控制器
class AuthController extends BaseController {
    /**
     * [roleList 角色列表]
     * @author TF <[2281551151@qq.com]>
     */
    public function roleList() {
        $auth_group = M('admin_auth_group');
        if( IS_POST ) {
            $groupName = I('groupName', '');
            if(empty($groupName)) {
                $this->error('分组名不能为空！');
            }
            $data = array(
                'title'     => $groupName,
                'status'    => '1',
                'add_time'  => time()
            );
            if ( $auth_group->data($data)->add() ) {
                $this->success('添加分组成功!');
            } else {
                $this->error('添加分组失败！');
            }
        } else {
            $group = $auth_group->select();
            $this->assign('group', $group);
            $this->display('roleList');
        }
    }

    /**
     * [addRole 添加角色]
     * @author TF <[2281551151@qq.com]>
     */
    public function addRole() {
        if ( IS_POST ) {
            $title = I('post.title');
            if ( empty($title) ){
                $this->error('角色名不能为空');
            }
            $data = array(
                'title'  => I('post.title'),
                'status' => I('post.status'),
            );

            $adminAuthGroup = D('admin_auth_group');
            $data = $adminAuthGroup->create(I('post.'), 1);


            if ( $adminAuthGroup->data($data)->add() ) {
                $this->success('添加成功！', U('Auth/roleList'));
            } else {
                $this->error('添加失败！', U('Auth/roleList'));
            }
        } else {
            $this->display('addRole');
        }
    }

    /**
     * [delRole 删除角色]
     * @author TF <[2281551151@qq.com]>
     */
    public function delRole() {
        $id = I('get.id', '', 'int');

        if ( empty($id) ) {
            $this->error('ID 参数丢失！');
        }

        if ( M('admin_auth_group_access')->where(array('group_id'=>$id))->count() > 0 ) {
            $this->error('请先将该组管理员删除，再进行操作！');
        }

        if ( M('admin_auth_group')->where(array('id'=>$id))->delete() ) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * [roleRename 改角色名称]
     * @author TF <[2281551151@qq.com]>
     */
    public function roleRename() {
        if ( IS_POST ) {
            $id      = I('post.id', '', 'int');
            $newName = I('post.newName');

            if ( M('admin_auth_group')->where(array('id'=>$id))->data(array('title'=>$newName))->save() !==false ) {
                $this->success('更改成功！', U('Auth/roleList'));
            } else {
                $this->error('更改失败！', U('Auth/roleList'));
            }
        } else {
            $id = I('get.id', '', 'int');
            $authInfo = M('admin_auth_group')->where(array('id'=>$id))->getField('title');
            $this->assign('authInfo', $authInfo);
            $this->display('roleRename');
        }
    }

   /**
     * [editRolePower 权限编辑]
     * @author StanleyYuen <[350204080@qq.com]>
     */
    public function editRolePower() {
        if ( IS_POST ) {
            $id = I('post.id');
            if ( empty($id) ) {
                $this->error('ID 参数丢失！');
            }

            $rules = I('post.rules');
            $rules = implode(',', $rules);

            if ( M('admin_auth_group')->where(array('id'=>$id))->data(array('rules'=>$rules))->save() !== false ) {
                $this->success('保存成功！');
            } else {
                $this->error('保存失败！');
            }
        } else {
            $id = I('get.id', '', 'int');
            $authRuleList = M('admin_auth_rule')->field('id, name, title')->select();

            $title = array(
                '管理员与权限',
                '内容管理',
                '用户管理',
                '商城管理',
                '订单管理',
                '财务管理',
                '信用管理',
                '积分管理',
                '商家管理',
                '系统设置',
            );
            
            $explode = array(
                array('Admin', 'Auth', 'Feedback'),
                array('Article'),
                array('User'),
                array('AgentCategory', 'Category', 'Goods', 'GoodsCheck', 'Bidding'),
                array('Order', 'GoodsComment'),
                array('Money'),
                array('Credit'),
                array('Point', 'PointOrder'),
                array('Agent'),
                array('System'),
            );

            $result = array();
            foreach ($title as $key => $value) {
                $result[$key]['title'] = $value;
                foreach ($authRuleList as $akey => $avalue) {
                    $controller = substr($avalue['name'], 0, strpos($avalue['name'], '-'));
                    if ( in_array($controller, $explode[$key]) ) {
                        $result[$key]['rules'][] = array(
                            'id'    => $avalue['id'],
                            'title' => $avalue['title'],
                        );
                    }
                }
            }

            $authGroup = M('admin_auth_group')->where(array('id'=>$id))->getField('rules');
            $this->assign('authGroup', explode(',', $authGroup));
            $this->assign('rules', $result);
            $this->display('editRolePower');
        }
    }
}