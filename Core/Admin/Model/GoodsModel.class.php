<?php
namespace Admin\Model;
use Think\Model;

class GoodsModel extends Model {
	private $dbPrefix;
	public function __construct() {
		parent::__construct();
		$this->dbPrefix = C('DB_PREFIX');
	}
	/**
	 * [_validate 自动验证]
     * @author kofu <[418382595@qq.com]>
	 */
	protected $_validate = array(
		// 新增
		array('goods_name', 'require', '请输入商品名称', 1, 'regex', 1),
		array('introduction', 'require', '请输入商品副标题', 1, 'regex', 1),
		array('category_id', 'require', '请选择平台分类', 1, 'regex', 1),
		array('agent_category_id', 'require', '请选择店内分类', 1, 'regex', 1),
		array('express_type', 'require', '请选择配送方式', 1, 'regex', 1),
		array('keyword', 'require', '请设置关键词', 1, 'regex', 1),
		array('keyword', checkKeywordSymbol, '关键词用;号隔开', 1, 'callback', 1),
		array('keyword', checkKeywordLength, '关键词最多设置3个', 1, 'callback', 1),
		// array('images', 'require', '请选择商品图片', 1, 'regex', 1),

		// 修改
		array('goods_name', 'require', '请输入商品名称', 1, 'regex', 2),
		array('introduction', 'require', '请输入商品副标题', 1, 'regex', 2),
		array('category_id', 'require', '请选择平台分类', 1, 'regex', 2),
		array('agent_category_id', 'require', '请选择店内分类', 1, 'regex', 2),
		array('express_type', 'require', '请选择配送方式', 1, 'regex', 2),
		array('keyword', 'require', '请设置关键词', 1, 'regex', 2),
		array('keyword', checkKeywordSymbol, '关键词用;号隔开', 1, 'callback', 2),
		array('keyword', checkKeywordLength, '关键词最多设置3个', 1, 'callback', 2),
		// array('images', 'require', '请选择商品图片', 1, 'regex', 2),
		
		array('goods_name', 'require', '请输入商品名称', 1, 'regex', 4),
		array('introduction', 'require', '请输入商品副标题', 1, 'regex', 4),
		array('express_type', 'require', '请选择配送方式', 1, 'regex', 4),

		array('goods_name', 'require', '请输入商品名称', 1, 'regex', 5),
		array('introduction', 'require', '请输入商品副标题', 1, 'regex', 5),
		array('express_type', 'require', '请选择配送方式', 1, 'regex', 5),
	);

	protected $_auto = array(
		array('add_time', 'time', 4, 'function'),
	);

	/**
	 * [getGoodsImages 获取商品相册]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
	public function getGoodsImages($goods_images_id) {
		$goods_images_id = trim($goods_images_id, ',');
		$goodsImagesModel = M('goods_images');
		$images = $goodsImagesModel->where(array('id'=> array('IN', $goods_images_id)))->field('`goods_image`')->select();
		$images = array_column($images, 'goods_image');
		if (!empty($images)) {
			return $images;
		} else {
			return array();
		}
	}
	
	/**
	 * [addGoodsImages 添加商品图片]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)     2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $images [description]
	 */
	public function addGoodsImages($images) {
		$goodsImagesModel = M('goods_images');
		$goods_images_id = array();
		foreach ($images as $key => $value) {
            $temp['goods_image'] = $value;
            $goods_images_id[] = $goodsImagesModel->add($temp);
        }
        
        return array(
        	'goods_image' => $images[0],
        	'goods_images_id' => implode(',', $goods_images_id)
        );
	}

	/**
	 * [editGoodsImages 编辑商品图片]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)        2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $images    [description]
	 * @param     [type]        $goodsData [description]
	 * @return    [type]                   [description]
	 */
	public function editGoodsImages($images, $goodsData) {
		$goodsImagesModel = M('goods_images');
		$where['id'] = array('IN', $goodsData['goods_images_id']);
		$imagesList = $goodsImagesModel->where($where)->select();
		$goods_images_id = array();
		// 处理旧图
		foreach ($imagesList as $key => $value) {
			if ( !in_array($value['goods_image'], $images) ) {
				$goodsImagesModel->delete($value['id']);
			} else {
				$goods_images_id[] = $value['id'];
			}
		}
		// 处理新图
		$imagesData = array_column($imagesList, 'goods_image');
		foreach ($images as $key => $value) {
			if ( !in_array($value, $imagesData) ) {
				$temp['goods_image'] = $value;
            	$goods_images_id[] = $goodsImagesModel->add($temp);
			}
		}
		return array(
        	'goods_image' => $images[0],
        	'goods_images_id' => implode(',', $goods_images_id)
        );
	}

	/**
	 * [addGoodsExtension 添加商品详情]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)          2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $description [description]
	 */
	public function addGoodsExtension($description) {
		$goodsExtensionModel = M('goods_extension');
		$descriptionData = array(
            'goods_desc' => $description,
            'add_time'   => time(),
        );
        $extensionId = $goodsExtensionModel->add($descriptionData);
        return $extensionId;
	}

	/**
	 * [editGoodsExtension 编辑商品详情]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)          2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $description [description]
	 * @param     [type]        $goodsData   [description]
	 * @return    [type]                     [description]
	 */
	public function editGoodsExtension($description, $goodsData) {
		$goodsExtensionModel = M('goods_extension');
		$map['id'] = $goodsData['goods_ext_id'];
		if( $goodsExtensionModel->where($map)->count() > 0 ){
			$descData['goods_desc'] = $description;
            $goodsExtensionModel->where($map)->data($descData)->save();
            $extensionId = $goodsData['goods_ext_id'];
        } else {
            $extensionId = $this->addGoodsExtension($description);
        }
		return $extensionId;
	}

	/**
	 * [deleteGoods 删除店铺商品]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)      2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $goodsId 	[description]
	 * @param     [type]        $agentId  	[description]
	 * @return    [type]                 	[description]
	 */
	public function deleteGoods($goodsId, $agentId) {
		$where 	= array(
			'id'        => array('IN', $goodsId),
            'agent_id'	=> $agentId
		);
		if ( $this->where($where)->save(array('is_delete'=> '1')) !== false ) {
			return true;
		} else {
			return false;
		}
	}
}
