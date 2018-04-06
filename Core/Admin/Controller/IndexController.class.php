<?php
namespace Admin\Controller;
use Think\Controller;
use Plugins\Detection\Detection;

// 控制台控制器
class IndexController extends BaseController {
    /**
     * [statistics 统计]
     * @author StanleyYuen <[350204080@qq.com]>
     */
    public function statistics() {
    	$this->display('statistics');
    }

    /**
     * [clearCache 清理缓存]
     * @author StanleyYuen <[350204080@qq.com]>
     */
    public function clearCache() {
    	if ( IS_POST ) {
			$id 	   = I('post.clearid');
			$clearList = I('post.clearlist');

			if ( empty($id) ) {
	            if ( empty($clearList) ) {
	                $this->error('参数丢失！');
	            }
				session('clearList', $clearList);
			} else {
	            if ( empty($id) ) {
	                $this->error('参数丢失！');
	            }
				$clearList = array($id);
				session('clearList', $clearList);
			}

			header('LOCATION:' . U('Index/removeCache'));
			exit();
    	} else {
	    	$this->display('clearCache');
    	}
    }

    /**
     * [clearCache 清理缓存]
     * @author StanleyYuen <[350204080@qq.com]>
     */
    public function removeCache() {
    	$clearlist = session('clearList');
    	if ( !empty($clearlist) ) {
    		$id = array_shift($clearlist);
    		session('clearList', $clearlist);
			switch ($id) {
				case '11':
					$name = "PC端商品详情页面缓存";
					$path = './webcache/Temp/PcGoodsTemp/';
					if ( file_exists($path) ) {
						$this->rrmdir('./webcache/Temp/PcGoodsTemp/');
					}
					break;

				case '12':
					$name = "PC端运行时(RUNTIME)缓存";
					$path = './webcache/Runtime/Cache/Shop/';
					if ( file_exists($path) ) {
						$this->rrmdir('./webcache/Runtime/Cache/Shop/');

					}
					break;

				case '21':
					$name = "WAP端商品详情页面缓存";
					$path = './webcache/Temp/MobileGoodsTemp/';
					if ( file_exists($path) ) {
						$this->rrmdir('./webcache/Temp/MobileGoodsTemp/');
					}
					break;

				case '22':
					$name = "WAP端运行时(RUNTIME)缓存";
					$path = './webcache/Runtime/Cache/Wap/';
					if ( file_exists($path) ) {
						$this->rrmdir('./webcache/Runtime/Cache/Wap/');
					}
					break;

				case '31':
					$name = "商品分类列表缓存";
					S('levelCategoryList', null);
					break;

				case '32':
					$name = "地区列表缓存";
					S('levelRegionList', null);
					break;

				case '41':
					$name = "后台运行时(RUNTIME)缓存";
					$path = './webcache/Runtime/Cache/Admin/';
					if ( file_exists($path) ) {
						$this->rrmdir('./webcache/Runtime/Cache/Admin/');
					}
					break;

				case '51':
					$name = "全站缓存";
					$variable = array(
						'11',
						'12',
						'21',
						'22',
						'31',
						'32',
						'41',
					);
					
					session('clearList', $variable);
					header('LOCATION:' . U('Index/removeCache'));
					exit();
					break;
			}

			$this->assign('flag', '0');
			$this->assign('msg', $name . '清理成功！');
			$this->display('removeCache');
		} else {
			$this->assign('flag', '1');
			$this->assign('msg', $name . '完成清理任务!');
			$this->display('removeCache');
		}
    }

    /**
     * [rrmdir 删除目录]
     * @author StanleyYuen <[350204080@qq.com]>
     */
	private function rrmdir($src) {
		$dir = opendir($src);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				$full = $src . '/' . $file;
				if ( is_dir($full) ) {
					$this->rrmdir($full);
				} else {
					unlink($full);
				}
			}
		}
		closedir($dir);
		rmdir($src);
	}
}