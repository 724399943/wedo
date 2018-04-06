<?php
namespace Shop\Controller;
use Think\Controller;
class RegionController extends Controller {
    /**
     * [getChildZone 得到下级地区]
     * @author StanleyYuen <[350204080@qq.com]>
     */
    public function getChildZone() {
        if ( IS_POST ) {
            $id = I('id', '0', 'string');
            $region = M('region');
            $returnData['list'] = $region->where(array('pid'=> $id))->select();
            exit(statusCode($returnData));
        }
    }

    /**
     * [getRegion 获取省列表]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function getRegion() {
        if ( IS_POST ) {
            $region = M('region');
            $returnData['list'] = $region->where(array('pid'=> '1'))->select();
            exit(statusCode($returnData));
        } else {
            exit(statusCode(array(), 100001));
        }
    }
}