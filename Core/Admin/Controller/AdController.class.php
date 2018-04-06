<?php
namespace Admin\Controller;
class AdController extends BaseController {
    /**
     * [index 广告位列表]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function index() {
        $adModel = M('ad');
        $agentModel = M('agent');
        // $dbPrefix = C('DB_PREFIX');
        $count = $adModel->count();
        $page = new \Think\Page($count, 25);
        $show = $page->show();
        $counting = $page->totalRows;
        $list = $adModel->order('`sort` ASC, `id` DESC')->limit($page->firstRow. ',' .$page->listRows)->select();
        foreach ($list as $key => &$value) {
            if ( !empty($value['agent_id']) ) {
                $where['id'] = $value['agent_id'];
                $value['agent_name'] = $agentModel->where($where)->getField('`agent_name`');
            } else {
                $value['agent_name'] = '自主上传';
            }
        }
        $this->assign('show', $show);
        $this->assign('list', $list);
        $this->assign('counting',$counting);
        $this->display();
    }

    /**
     * [addAd 添加广告]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c) 2017 Xcrozz (http://www.xcrozz.com)
     */
    public function addAd() {
        if ( IS_POST ) {
            $adModel = D('Ad');
            $addData = $adModel->create(I('post.'), 1);
            if( $addData ) {
                // $desc = I('post.content', '');
                // $addData['content'] = htmlspecialchars_decode($desc);
                if( $adModel->add($addData) !== false ) {
                    $this->success('添加成功', U('Ad/index'));
                } else {
                    $this->error('添加失败');
                }
            } else {
                $this->error($adModel->getError());
            }
        } else {
            $this->display();
        }
    }
    
    /**
     * [editAd 编辑广告]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function editAd() {
        if( IS_POST ) {
            $adModel = D('Ad');
            $saveData = $adModel->create(I('post.'), 4);
            if( !empty($saveData) ) {
                // $desc = I('post.content','');
                // $saveData['content'] = htmlspecialchars_decode($desc);
                if( $adModel->save($saveData) !== false ) {
                    // $this->success('更新成功', U('Ad/index'));
                    exit(statusCode());
                } else {
                    // $this->error('更新失败');
                    exit(statusCode('', 400000, '更新失败'));
                }
            } else {
                // $this->error($adModel->getError());
                exit(statusCode('', 400000, $adModel->getError()));
            }
        } else {
            $this->display();
        }
    }

    // 图片上传使用
    public function photoUpload() {
        // 图片保存路径
        fileUpload('Ad/', function($e) {
            echo json_encode(array('error'=>'', 'url'=>trim($e['filePath'], '.')));
        });
    }
}