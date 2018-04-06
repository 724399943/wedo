<?php
namespace Admin\Controller;
use Think\Controller;
// 系统控制器
class SystemController extends BaseController {
    /**
     * [setting 系统设置]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function setting() {
        if ( IS_POST ) {
            $config = M('config');
            $data   = I('post.config');
            foreach ($data as $key => $value) {
                $saveData = array();
                $saveData['config_value'] = $value['config_value'];
                $config->where(array('config_sign'=>$value['config_sign']))->data($saveData)->save();
            }
            $this->success('更新成功！');
        } else {
            $configList = M('config')->select();
            $this->assign('configList', $configList);
            $this->display('setting');
        }
    }

    /**
     * [checkSetting 认证/置顶审核期限设置]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2017 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function checkSetting() {
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
     * [photoSave 上传图片]
     * @author StanleyYuen <[350204080@qq.com]>
     */
    public function photoSave() {
        // 图片保存路径
        fileUpload('System/', function($e) {
            echo json_encode(array('error'=>'', 'src'=>trim($e['filePath'], '.')));
        });
    }
}