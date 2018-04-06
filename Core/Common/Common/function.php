<?php
function time_format($time) {
    return ( empty($time) ) ? '-' : date('Y-m-d H:i:s', $time);
}

function curl($url, $data = '', $requestType = 'post') {
   //初始化curl
   	$ch = curl_init();
	//设置超时
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	if (strtolower($requestType) == 'post') {
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
	}

    $return = curl_exec($ch);
	curl_close($ch);
	return $return;
}

/**
 * [checkActionAuth 检查方法权限]
 * @author StanleyYuen <[350204080@qq.com]>
 */
function checkActionAuth($controllerAction) {
    static $authListCache;
    if ( empty($authListCache) ) {
        $authListCache = session('authList');
    }

    if ( is_array($controllerAction) ) {

        foreach ($controllerAction as $value) {
            if ( in_array(strtolower($value), $authListCache) ) {
                return true;
            }
        }
    } else {
        if ( in_array(strtolower($controllerAction), $authListCache) ) {
            return true;
        }
    }
}

/**
 * [checkControllerAuth 检查全控制器权限]
 * @author StanleyYuen <[350204080@qq.com]>
 */
function checkControllerAuth($controllerAction) {
    static $authListCache;
    if ( empty($authListCache) ) {
		$temp = session('authList');

		foreach ($temp as $key => $tvalue) {
			$temp[$key] = substr($tvalue, 0, strpos($tvalue, '-'));
		}

		$temp = array_unique($temp);
		$authListCache = $temp;
    }

    if ( is_array($controllerAction) ) {
      // 或条件
        foreach ($controllerAction as $value) {
            if ( in_array(strtolower($value), $authListCache) ) {
                return true;
            }
        }
    } else {
        if ( in_array(strtolower($controllerAction), $authListCache) ) {
            return true;
        }
    }
}

/**
 * [getAuthUrl 得到权限地址]
 * @author StanleyYuen <[350204080@qq.com]>
 */
function getAuthUrl($controllerAction) {
    static $authListCache;
    if ( empty($authListCache) ) {
        $authListCache = session('authList');
    }

    if ( is_array($controllerAction) ) {
        foreach ($controllerAction as $value) {
            if ( in_array(strtolower($value), $authListCache) ) {
                $url = str_replace('-', '/', $value);
                return U($url);
            }
        }
    } else {
        if ( in_array(strtolower($controllerAction), $authListCache) ) {
            $url = str_replace('-', '/', $controllerAction);
            return U($url);
        }
        return false;
    }
}

if ( ! function_exists('random_string') ) {
	/**
	* [random_string 随机字符串]
	* @param  string  $type [规则]
	* alnum  含有大小写字母以及数字。
	* numeric  数字字符串。
	* nozero  不含零的数字字符串。
	* alpha  含有大小写字母
	* unique 用 MD5 和 uniqid()加密的字符串。注意：第二个长度参数在这种类型无效。均返回一个32位长度的字符串。
	* md5
	* @param  integer $len  [字符数]
	* @return [type]        [description]
	*/
	function random_string($type = 'alnum', $len = 8) {
		switch($type) {
			case 'basic'	: return mt_rand();
				break;
			case 'alnum'	:
			case 'numeric'	:
			case 'nozero'	:
			case 'alpha'	:
				switch ($type) {
					case 'alpha'	:	$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						break;
					case 'alnum'	:	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						break;
					case 'numeric'	:	$pool = '0123456789';
						break;
					case 'nozero'	:	$pool = '123456789';
						break;
				}

				$str = '';
				for ($i=0; $i < $len; $i++) {
					$str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
				}
				return $str;
				break;
			case 'unique'	:
			case 'md5'		:
				return md5(uniqid(mt_rand()));
				break;
		}
	}
}

// array_column 需要 (PHP 5 >= 5.5.0)
if ( !function_exists('array_column') ) {
    function array_column($input, $columnKey, $indexKey = NULL) {
        $columnKeyIsNumber = (is_numeric($columnKey)) ? TRUE : FALSE;
        $indexKeyIsNull    = (is_null($indexKey))     ? TRUE : FALSE;
        $indexKeyIsNumber  = (is_numeric($indexKey))  ? TRUE : FALSE;
        $result            = array();

        foreach ((array)$input AS $key => $row) {
            if ($columnKeyIsNumber) {
                $tmp = array_slice($row, $columnKey, 1);
                $tmp = (is_array($tmp) && !empty($tmp)) ? current($tmp) : NULL;
            } else {
                $tmp = isset($row[$columnKey]) ? $row[$columnKey] : NULL;
            }

            if ( ! $indexKeyIsNull) {
                if ($indexKeyIsNumber) {
                    $key = array_slice($row, $indexKey, 1);
                    $key = (is_array($key) && ! empty($key)) ? current($key) : NULL;
                    $key = is_null($key) ? 0 : $key;
                } else {
                    $key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
                }
            }
            $result[$key] = $tmp;
        }
        return $result;
    }
}

if ( ! function_exists('encrypt')) {
	/**
	 * [encrypt 系统标准加密]
	 * @author StanleyYuen <[350204080@qq.com]>
	 */
	function encrypt($string, $length = '') {
	    $crypt = md5($string);

	    if ( !empty($length) ) {
	        return substr($crypt, 0, $length);
	    } else {
	        return $crypt;
	    }
	}
}

if ( ! function_exists('base64_upload')) {
	/**
	 * [base64_upload base64上传]
	 * @author NicFung <13502462404@qq.com>
	 * @copyright Copyright (c)            2015          Xcrozz (http://www.xcrozz.com)
	 * @license   [license]
	 * @version   [version]
	 * @param     [type]        $base64Str     [base64字符]
	 * @param     string        $savePath      [保存路径]
	 * @param     array         $attributeTemp [description]
	 * @param     string        $method        [description]
	 * @return    [type]                       [description]
	 */
	function base64_upload($base64Str, $savePath="", $attributeTemp=array(), $method="post") {
		$attribute = array(
			'maxSize' => 3145728,
			'exts' => array('jpg', 'gif', 'png', 'jpeg'),
		);
		if ( !empty($attributeTemp) ) {
			$attribute = array_merge($attribute, $attributeTemp);
		}

		$base64Len = (int) (strlen($base64Str) / 4 * 3);
		if ($base64Len > $attribute['maxSize']) {
			$return = array(
				'boolean' => FALSE,
				'status' => 300004,
			);
			return $return;
		}
		$rootpath = C('UPLOAD_PATH');
		if ( empty($savePath) ) {
			$savePath = $rootpath . 'tmp/'. date("Y-m-d") . '/';
		} else {
			$savePath = $rootpath . trim($savePath, '/') . '/' . date("Y-m-d") . '/';
		}
		if ( !file_exists($savePath) ) {
	        mkdir($savePath, 0755, true);
	    }
		if ( $method == 'post' ) {
		    //post的数据里面，加号会被替换为空格，需要重新替换回来，如果不是post的数据，则注释掉这一行
		    $base64Str = str_replace(' ', '+', $base64Str);
		}
	    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64Str, $result)){
	    	if ( !in_array($result[2], $attribute['exts']) ) {
	    		//判读图片类型
	    		$return = array(
	    			'boolean' => FALSE,
	    			'status' => 300001,
	    		);
	    		return $return;
	    	}
	        //匹配成功
	        $image_name = uniqid() . '.' . $result[2];
	        $image_file = "{$savePath}{$image_name}";
	        //服务器文件存储路径
	        if (file_put_contents($image_file, base64_decode(str_replace($result[1], '', $base64Str)))){
	        	$image_file = str_replace('./', '/', $image_file);
	            $return = array(
	            	'boolean' => TRUE,
	            	'data' => $image_file,
	            );
	            return $return;
	        }else{
	        	$return = array(
	        		'boolean' => FALSE,
	        		'status' => 300002,
	        	);
	            return $return;
	        }
	    } else {
	    	$return = array(
	    		'boolean' => FALSE,
	    		'status' => 300003,
	    	);
	        return $return;
	    }
	}
}

if (!function_exists('statusCode')) {
	/**
	 * [statusCode 返回json数据结构]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)      2016          Xcrozz (http://www.xcrozz.com)
	 * @param     array         $data    [description]
	 * @param     integer       $status  [description]
	 * @param     string        $message [description]
	 * @return    [type]                 [description]
	 */
	function statusCode($data=array(), $status=200000, $message="") {
	    if ( empty($message) ) {
	    	$think_language = cookie('think_language');
	    	$errcode = M('errcode');
	    	$message = $think_language == 'zh-cn' ? 
	    		$errcode->where(array('errCode' => $status))->getField('memo') : 
	    		$errcode->where(array('errCode' => $status))->getField('errMsg');
	    }
	    foreach ($data as $key => $value) {
	      	if (is_null($value)) {
	        	$data[$key] = '';
	      	}
	    }
	    $userInfo = session('userInfo');
	    $user['id'] = $userInfo['id'];
	    $user['headimgurl'] = $userInfo['headimgurl'];
	    $user['nickname'] = $userInfo['nickname'];
	    $array = array(
			'status' => $status,
			'message' => $message,
			'webSite' => C('webSite'),
			'data' => array(
				'session_id' => session_id(),
				'user'=> $user,
		    )
	    );

	    if ( NEED_PAGE && NEED_PAGE != 'NEED_PAGE') {
	        $array['data']['page_limit'] = PAGE_LIMIT;
	        $array['data']['page'] = I('request.page', 1, 'int');
	    }
	    $array['data'] = array_merge($array['data'], $data);
	    return json_encode($array);
	}
}

if (!function_exists('fileUpload')) {
	/**
	 * [fileUpload 上传文件]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2016          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $savePath     [description]
	 * @param     [type]        $callable     [description]
	 * @param     array         $parameters   [description]
	 * @param     boolean       $customUpload [description]
	 * @return    [type]                      [description]
	 */
	function fileUpload($savePath, $callable, $parameters = array(), $customUpload = false) {
	    $uploadPath = $customUpload === true ? '' : C('UPLOAD_PATH');
	    $savePath = $uploadPath . $savePath;
	    if ( !file_exists($savePath) ) {
	        mkdir($savePath, 0777, true);
	    }

	    $upload            = new \Think\Upload(array(), '', null, $customUpload);
	    $upload->maxSize   = 209715200;
	    $upload->exts      = array('jpg', 'gif', 'png', 'jpeg', 'zip', 'rar', 'ico');
	    $upload->rootPath  = $savePath; 
	    $info              = $upload->upload();
	    
	    if ( !$info ) {
	        echo $upload->getError();
	    } elseif ( is_callable($callable) ) {
	        $keys = array_keys($info);
	        $key  = $keys[0];
	        $one  = $info[$key];

	        if (empty($parameters)) {
				$one['filePath'] = $savePath . $one['savepath'] . $one['savename'];
				// 解压zip、rar文件
				switch ($one['ext']) {
					case 'zip':
					  	extractZip($one['filePath'], $savePath . $one['savepath']);
					  	break;
					case 'rar':
					  	extractRar($one['filePath'], $savePath . $one['savepath']);
					  	break;
					default:					  
					  	break;
				}
	        } else {
				// file input的id
				// $parameters['id'] = $parameters['id'] ? $parameters['id'] : 'fileToUpload';
				$parameters['id'] = $key ? $key : 'fileToUpload';
				$photoNamenoExt = str_replace('.' . $info[$parameters['id']]['ext'], '', $info[$parameters['id']]['savename']);
				$image = new \Think\Image();
				$image->open($savePath . $one['savepath'] . $one['savename']);

	          	if ($parameters['multi']) {
		            // 是否需要生成多种尺寸
		            foreach ($parameters['size'] as $key => $value) {
		              	$widPath = "{$savePath}{$one['savepath']}{$value['width']}x{$value['height']}/";
		              	if (!is_file($widPath)) {
		                	mkdir($widPath, 0700, TRUE);
		              	}
		              	$image->thumb($value['width'], $value['height'], 3)->save("{$savePath}{$one['savepath']}{$value['width']}x{$value['height']}/{$photoNamenoExt}_{$value['width']}x{$value['height']}.{$info[$parameters['id']]['ext']}");
		            }
	            	$one['filePath'] = "{$savePath}{$one['savepath']}{$parameters['size'][0]['width']}x{$parameters['size'][0]['height']}/{$photoNamenoExt}_{$parameters['size'][0]['width']}x{$parameters['size'][0]['height']}.{$info[$parameters['id']]['ext']}";
	          	} else {
	              	$widPath = "{$savePath}{$one['savepath']}{$parameters['width']}x{$parameters['height']}/";
	              	if (!is_file($widPath)) {
	                	mkdir($widPath, 0700, TRUE);
	              	}
	           		$image->thumb($parameters['width'], $parameters['height'], 3)->save("{$savePath}{$one['savepath']}{$parameters['width']}x{$parameters['height']}/{$photoNamenoExt}_{$parameters['width']}x{$parameters['height']}.{$info[$parameters['id']]['ext']}");
	            	$one['filePath'] = "{$savePath}{$one['savepath']}{$parameters['width']}x{$parameters['height']}/{$photoNamenoExt}_{$parameters['width']}x{$parameters['height']}.{$info[$parameters['id']]['ext']}";
	          	}
	        }
	        $callable($one);
	    }
	}
}

/**
 * [extractZip 解压zip文件]
 * @author kofu <418382595@qq.com>
 * @copyright Copyright (c)       2016          Xcrozz (http://www.xcrozz.com)
 * @param     [type]        $filename [description]
 * @param     [type]        $path     [description]
 * @return    [type]                  [description]
 */
function extractZip($filename, $path) {  
    //先判断待解压的文件是否存在  
    if (!file_exists($filename)) {  
        die("文件 $filename 不存在！");  
    }  
    $starttime = explode(' ', microtime()); //解压开始的时间   
    //打开压缩包  
    $resource = zip_open($filename);  
    // $i = 1;  
    //遍历读取压缩包里面的一个个文件  
    while ($dir_resource = zip_read($resource)) {  
        //如果能打开则继续  
        if (zip_entry_open($resource, $dir_resource)) {  
            //获取当前项目的名称,即压缩包里面当前对应的文件名  
            $file_name = $path.zip_entry_name($dir_resource);
            // asciitog($file_name);  // 中文转码
            //以最后一个“/”分割,再用字符串截取出路径部分  
            $file_path = substr($file_name, 0, strrpos($file_name, "/"));
            //如果路径不存在，则创建一个目录，true表示可以创建多级目录  
            if (!is_dir($file_path)) {  
                mkdir($file_path, 0777, true);  
            }  
            //如果不是目录，则写入文件  
            if (!is_dir($file_name)) {  
                //读取这个文件  
                $file_size = zip_entry_filesize($dir_resource);  
                //最大读取6M，如果文件过大，跳过解压，继续下一个  
                if ($file_size < (1024 * 1024 * 6)) {  
                    $file_content = zip_entry_read($dir_resource, $file_size);  
                    file_put_contents($file_name, $file_content);  
                } else {  
                    // echo "<p> ".$i++." 此文件已被跳过，原因：文件过大， -> ".iconv("gb2312", "utf-8", $file_name)." </p>";  
                }  
            }  
            //关闭当前  
            zip_entry_close($dir_resource);  
        }  
    }  
    //关闭压缩包  
    zip_close($resource);  
    // $endtime = explode(' ', microtime()); //解压结束的时间  
    // $thistime = $endtime[0] + $endtime[1] - ($starttime[0] + $starttime[1]);  
    // $thistime = round($thistime, 3); //保留3为小数  
    // echo "<p>解压完毕！，本次解压花费：$thistime 秒。</p>演示地址：/".$path;  
}

function imConf(){
    $options['client_id'] = C('Client_id');
    $options['client_secret'] = C('Client_secret');
    $options['org_name'] = C('Org_name');
    $options['app_name'] = C('App_name');
    return $options;
}

/**
 * [isPhone 验证手机号码格式]
 * @author kofu <[418382595@qq.com]>
 */
function isPhone($phone){
    $chars = "/^1[34578]{1}\d{9}$/";
    if (preg_match($chars, $phone)){
        return true;
    }
    return false;
}

/**
 * [isEmail 验证邮箱格式]
 * @author kofu <[418382595@qq.com]>
 */
function isEmail($value) {
    $chars = "/^[0-9a-zA-Z]+(?:[\_\.\-][a-z0-9\-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i";
    if (preg_match($chars, $value)){
        return true;
    }
    return false;
}

// 过滤javascript代码防止xss攻击
function remove_xss($val) {
	$val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
	$search = 'abcdefghijklmnopqrstuvwxyz';
	$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$search .= '1234567890!@#$%^&*()';
	$search .= '~`";:?+/={}[]-_|\'\\';
	for ($i = 0; $i < strlen($search); $i++) {
		$val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val);
		$val = preg_replace('/(�{0,8}'.ord($search[$i]).';?)/', $search[$i], $val);
	}

	$ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
	$ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
	$ra = array_merge($ra1, $ra2);
	$found = true;
	while ($found == true) {
		$val_before = $val;
		for ($i = 0; $i < sizeof($ra); $i++) {
			$pattern = '/';
			for ($j = 0; $j < strlen($ra[$i]); $j++) {
				if ($j > 0) {
					$pattern .= '(';
					$pattern .= '(&#[xX]0{0,8}([9ab]);)';
					$pattern .= '|';
					$pattern .= '|(�{0,8}([9|10|13]);)';
					$pattern .= ')*';
				}
				$pattern .= $ra[$i][$j];
			}
			$pattern .= '/i';
			$replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2);
			$val = preg_replace($pattern, $replacement, $val);
			if ($val_before == $val) {
				$found = false;
			}
		}
	}
	return $val;
}

/**
 * [getRegionName 获取地区名]
 * @author kofu <418382595@qq.com>
 * @copyright Copyright (c) 2016          Xcrozz (http://www.xcrozz.com)
 * @param     [type]        $id [description]
 * @return    [type]            [description]
 */
function getRegionName($id) {
	$region = M('region');
	$region_name = $region->where(array('id'=> $id))->getField('region_name');
	$region_name = !empty($region_name) ? $region_name : '';
	return $region_name;
}


/**
 * [mySubstr 截取字符串(中文截取)]
 * @author Fu <[418382595@qq.com]>
 */
function mySubstr($str,$len=20,$suffix='...',$charset='UTF-8'){
    $substr = mb_substr($str,0,$len,$charset);
    if($substr != $str)$substr .= $suffix;
    return $substr;
}

/**
 * [getCategoryName 获取商品分类名称]
 * @author kofu <418382595@qq.com>
 * @copyright Copyright (c) 2017          Xcrozz (http://www.xcrozz.com)
 * @param     [type]        $id [description]
 * @return    [type]            [description]
 */
function getCategoryName($id) {
	$name = M('goods_category')->where(array('id'=> $id))->getField('category_name');
	$name = !empty($name) ? $name : '';
	return $name;
}

/**
 * [getAgentCategoryName 获取店铺商品分类名称]
 * @author kofu <418382595@qq.com>
 * @copyright Copyright (c) 2017          Xcrozz (http://www.xcrozz.com)
 * @param     [type]        $id [description]
 * @return    [type]            [description]
 */
function getAgentCategoryName($id) {
	$name = M('agent_goods_category')->where(array('id'=> $id))->getField('category_name');
	$name = !empty($name) ? $name : '';
	return $name;
}

/**
 * [getNickname 获取用户昵称]
 * @author kofu <418382595@qq.com>
 * @copyright Copyright (c) 2017          Xcrozz (http://www.xcrozz.com)
 * @param     [type]        $id [description]
 * @return    [type]            [description]
 */
function getNickname($id) {
	$userModel = M('user');
	$name = $userModel->where(array('id'=> $id))->getField('`nickname`');
	$name = !empty($name) ? $name : '-';
	return $name;
}

/**
 * [getAgentName 获取店铺名称]
 * @author kofu <418382595@qq.com>
 * @copyright Copyright (c) 2017          Xcrozz (http://www.xcrozz.com)
 * @param     [type]        $id [description]
 * @return    [type]            [description]
 */
function getAgentName($id, $default = true) {
	$agentModel = M('agent');
	$name = $agentModel->where(array('id'=> $id))->getField('`agent_name`');
	$name = !empty($name) ? $name : ($default == true ? '-' : '自营店铺');
	return $name;
}

function getGoodsname($id) {
	$goodsModel = M('goods');
	$name = $goodsModel->where(array('id'=> $id))->getField('`goods_name`');
	$name = !empty($name) ? $name : '-';
	return $name;
}

/**
 * @param $expTitle 订单文件名称
 * @param $expCellName 订单标题名称
 * @param $expTableData 订单数据内容
 */
function exportExcel($expTitle,$expCellName,$expTableData){
    $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
    $fileName = $expTitle.date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
    $cellNum = count($expCellName);
    $dataNum = count($expTableData);
    vendor("PHPExcel.PHPExcel");
    set_time_limit(0);
    $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_discISAM;
    \PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
    $objPHPExcel = new \PHPExcel();
    $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

    //$objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
    // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));
    $objActSheet = $objPHPExcel->getActiveSheet(); 
    for($i=0;$i<$cellNum;$i++){
        $objActSheet->setCellValue($cellName[$i].'1', $expCellName[$i][1]);
    }

    for($i=0;$i<$dataNum;$i++){
        for($j=0;$j<$cellNum;$j++){
          // $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+2), $expTableData[$i][$expCellName[$j][0]]);
          $objActSheet->setCellValueExplicit($cellName[$j].($i+2), $expTableData[$i][$expCellName[$j][0]],PHPExcel_Cell_DataType::TYPE_STRING);
        }
    }
    
    header('pragma:public');
    header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
    header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
}

/**
 * [sendMail 发送邮件]
 * @author kofu <418382595@qq.com>
 * @copyright Copyright (c)      2017          Xcrozz (http://www.xcrozz.com)
 * @param     [type]        $to      [description]
 * @param     [type]        $title   [description]
 * @param     [type]        $content [description]
 * @return    [type]                 [description]
 */
function sendMail($to, $title, $content) {
    Vendor('PHPMailer.PHPMailerAutoload');     
    // PHPMailerAutoload('phpmailer');
    // PHPMailerAutoload('smtp');
    
    $mail           = new PHPMailer();       	//得到一个PHPMailer实例 
    $mail->CharSet  = "utf-8";                	//设置采用utf-8中文编码(内容不会乱码) 
    $mail->IsSMTP();                          	//设置采用SMTP方式发送邮件 
    $mail->SMTPDebug  = 0;                     	// 关闭SMTP调试功能
                                               	// 1 = errors and messages
                                               	// 2 = messages only
    $mail->SMTPSecure = 'ssl';                 	// 使用安全协议
    $mail->Host     = C('MAIL_HOST');     		//设置邮件服务器的地址(若为163邮箱，则是smtp.163.com)
    $mail->Port     = C('MAIL_PORT');           //设置邮件服务器的端口，默认为25 
    $mail->From     = C('MAIL_USERNAME');       //设置发件人的邮箱地址
    $mail->FromName = C('MAIL_FROMNAME');       //设置发件人的姓名(可随意) 
    $mail->SMTPAuth = C('MAIL_SMTPAUTH');       //设置SMTP是否需要密码验证，true表示需要 

    $mail->Username = C('MAIL_USERNAME');       //(后面有解释说明为何设置为发件人)
    $mail->Password = C('MAIL_PASSWORD');
    $mail->Subject  = $title;                 	//主题 
    $mail->AltBody  = "text/html";

    $contents = $content;

    $mail->Body = $contents;            //内容   
    $mail->IsHTML(true);      
    //$mail->WordWrap = 50;                    //设置每行的字符数 
    $mail->AddReplyTo(C('MAIL_USERNAME'), "from");    //设置回复的收件人的地址(from可随意) 
    $mail->AddAddress($to, "to");        //设置收件的地址(to可随意) 
    $result = $mail->Send();

    if ($result) {
      $returnData['result'] = 0;
      $returnData['errmsg'] = 'send success'; 
    } else {
      $returnData['result'] = 1;
      $returnData['errmsg'] = "{$mail->ErrorInfo}";
    }
    
    return $returnData;
} 