<?php
namespace Shop\Controller;
use Think\Controller;

class ArticleController extends BaseController {
    /**
     * [articleDetail 文章详情]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function articleDetail() {
        $sign = I('request.sign', 'aboutUs');
        if ( IS_POST ) {
            $article = M('article');
            $returnData['list'] = $article->where(array('sign'=> $sign))->find();
            $returnData['list']['content'] = htmlspecialchars_decode($returnData['list']['content']);
            // 处理图片地址
            $webSite = trim(C('webSite'), '/');
            $returnData['list']['content'] = str_replace('/Static/Uploads/', $webSite . '/Static/Uploads/', $returnData['list']['content']);
            exit(statusCode($returnData));
        } else {
            $this->assign('sign', $sign);
            $this->display();
        }
    }
}