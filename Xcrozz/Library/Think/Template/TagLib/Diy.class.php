<?php
namespace Think\Template\TagLib;
use Think\Template\TagLib;

// 自定义扩展标签
class Diy extends TagLib {
    // 标签定义
    protected $tags   =  array(
        'ad'        =>  array('attr'=>'name, sign', 'close'=>0),
        'adgroup'   =>  array('attr'=>'name, sign', 'close'=>0),
        'goodslist' =>  array('attr'=>'name, ids', 'close'=>0),
        'brandlist' =>  array('attr'=>'name, ids', 'close'=>0),
        'tagList' =>  array('attr'=>'name', 'close'=>0),
        'linkList' => array('attr'=>'name', 'close'=>0)
    );

    /**
    * [_ad 广告位]
    * @author StanleyYuen <[350204080@qq.com]>
    */
    public function _ad($tag, $content) {
        $name     = $this->autoBuildVar($tag['name']);
        if ( empty($tag['sign']) ) {
            return;
        }
        
        $value    = 'getAdBox("' . $tag['sign'] . '")';
        $parseStr = '<?php ' . $name . ' = ' . $value . '; ?>';
        return $parseStr;
    }

    /**
    * [_adgroup 分组广告位]
    * @author StanleyYuen <[350204080@qq.com]>
    */
    public function _adgroup($tag, $content) {
        $name     = $this->autoBuildVar($tag['name']);
        if ( empty($tag['sign']) ) {
            return;
        }
        
        $value    = 'getAdGroup("' . $tag['sign'] . '")';
        $parseStr = '<?php ' . $name . ' = ' . $value . '; ?>';
        return $parseStr;
    }

    /**
    * [_goodslist 商品列表]
    * @author StanleyYuen <[350204080@qq.com]>
    */
    public function _goodslist($tag, $content) {
        $name     = $this->autoBuildVar($tag['name']);
        // if ( empty($tag['ids']) ) {
        //     $tag['ids'] = getIds($tag['name']);
        // }

        $value    = 'getGoodsList("' . $tag['name'] . '")';
        $parseStr = '<?php ' . $name . ' = ' . $value . '; ?>';
        return $parseStr;
    }

    /**
    * [_linkList 链接列表]
    * @author StanleyYuen <[350204080@qq.com]>
    */
    public function _linkList($tag, $content) {
        $name     = $this->autoBuildVar($tag['name']);

        $value    = 'getLinkList("' . $tag['name'] . '")';
        $parseStr = '<?php ' . $name . ' = ' . $value . '; ?>';
        return $parseStr;
    }

    /**
     * [_tagList description]
     * @author NicFung <13502462404@qq.com>
     * @copyright Copyright (c)      2015          Xcrozz (http://www.xcrozz.com)
     * @param     [type]        $tag     [description]
     * @param     [type]        $content [description]
     * @return    [type]                 [description]
     */
    public function _tagList($tag, $content) {
        $name     = $this->autoBuildVar($tag['name']);

        $value    = 'getGoodsTag("' . $tag['name'] . '")';
        $parseStr = '<?php echo '.$value.'; ?>';
        return $parseStr;
    }

    /**
    * [_brandlist 品牌列表]
    * @author StanleyYuen <[350204080@qq.com]>
    */
    public function _brandlist($tag, $centent) {
       $name     = $this->autoBuildVar($tag['name']);
        if ( empty($tag['ids']) ) {
            return;
        }

        $value    = 'getGoodsList("' . $tag['ids'] . '")';
        $parseStr = '<?php '.$name.' = '.$value.'; ?>';
        return $parseStr;
    }
}
