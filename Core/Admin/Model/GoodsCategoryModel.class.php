<?php
namespace Admin\Model;
use Think\Model;
class GoodsCategoryModel extends Model{
	//自动验证
    protected $_validate = array(
        array('category_name','require','请输入分类名称',1,'regex',1),
        array('category_name','','分类名称请勿重复',1,'unique',1),
        array('app_icon','require','请上传分类图片',1,'regex',1),

        array('category_name','require','请输入分类名称',1,'regex',2),
        // array('category_name','','分类名称请勿重复',1,'unique',2),
        array('app_icon','require','请上传分类图片',1,'regex',2),
    );
}