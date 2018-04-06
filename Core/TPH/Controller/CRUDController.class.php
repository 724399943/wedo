<?php
//	ThinkphpHelper v0.3
//	
//	weiyunstudio.com
//	sjj zhuanqianfish@gmail.com
//	2014年9月18日
namespace TPH\Controller;
use Think\Controller;
use Think\Model;

class CRUDController extends Controller {	
	public function crud(){	//生成CRUD代码
		$this->assign('tableNameList', getTableNameList());
		$this->assign('moduleNameList', getModuleNameList());
		$this->assign('selectTableName', $this->getSessionTableName());
		$this->assign('db_prefix',C('DB_PREFIX'));
		$this->display();
    }

	public function getSessionTableName(){
		$selectTableName = implode("','", session('selectTableName'));
		return "['".$selectTableName."']";
	}
	
	public function getPageCode($tableName){	//	分页代码  未使用???
		$Model = M($tableName); // 实例化对象
		$count = $Model->where('status=1')->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list =	$Model->where('status=1')->order('create_time')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);
	}

	/**
	 * [getSearchView 得到搜索页面代码]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)             2016          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $tableInfoArray [description]
	 * @param     [type]        $columnNameKey  [description]
	 * @param     [type]        $TableName      [description]
	 * @return    [type]                        [description]
	 */
	public function getSearchView($tableInfoArray, $columnNameKey, $TableName){
		$description 	= I('description');
		$needSearch 	= I('needSearch');
		$searchType 	= I('searchType');
		$option 		= I('option');
		$form = array();
		foreach ($tableInfoArray as $key => $value) {
			if ($needSearch[$key] == '1') {
				$data['name'] 			= $value[$columnNameKey];
				$data['description'] 	= $description[$key];
				$data['searchType'] 	= $searchType[$key];
				$data['option'] 		= $option[$key];
				$form[] = $data;
				unset($data);
			}
		}

		// 表单搜索
		// 生成 form.html 文件的代码
        $search = array('<form class="mb-20" method="get" action="/{:MODULE_NAME}/'. $TableName .'/all">');
		foreach ($form as $key => $value) {
	        switch ($value['searchType']) {
	            case 'select':
	            	$options = $this->parseOption($value['option']);
	                // 默认选中
	                $searchSelected .= tab(2) . '$("[name=\'' . $value['name'] . '\']").find("[value=\'{$_GET[\'' . $value['name'] . '\']}\']").attr("selected", true);' . "\n";
	                $search[] = tab(1) . '<div class="select-box" style="width:250px">';
	                $search[] = tab(2) . '<span>' . $value['description'] . '：</span>';
	                $search[] = tab(2) . '<select name="' . $value['name'] . '" class="select">';
	                $search = array_merge($search, $this->getOption($options, $form, true, 3));
	                $search[] = tab(2) . '</select>';
	                $search[] = tab(1) . '</div>';
	                break;
	            case 'date':
					// include
					$include[] = '<include file="Public:datetimepicker" />';
					$include[] = '';
	            	// search
	            	$search[] = tab(1) . '<label>开始时间：</label>';
	            	$search[] = tab(1) . '<input type="text" id="startDateTime" date-time="{$return[\'startTime\']}" style="margin-right: 0px">';
	            	$search[] = tab(1) . '<input type="hidden" name="startTime" id="startTime" value="{$return[\'startTime\']}" >-';
	            	$search[] = tab(1) . '<input type="text" id="endDateTime" date-time="{$return[\'endTime\']}">';
	            	$search[] = tab(1) . '<input type="hidden" name="endTime" id="endTime" value="{$return[\'endTime\']}">';
	                break;
	            default:
	                $search[] = tab(1) . '<input type="text" class="input-text" style="width:250px" '
	                    . 'placeholder="' . $value['description'] . '" name="' . $value['name'] . '" '
	                    . 'value="{$_GET[\'' . $value['name'] . '\']}" '
	                    . '>';
	                break;
	        }
		}

		if (count($search) > 1) {
            // 有设置搜索则显示
            $search[] = tab(1) . '<button type="submit" class="btn btn-success">搜索</button>';
            $search[] = '</form>';
            $search[] = '';
        } else {
            // 不设置将form.html置空
            $search = array();
        }
        $return = array(
        	'search'	=> $search,
        	'include'	=> $include,
        );
        return $return;
	}

	/**
     * 获取下拉框的option
     */
    private function getOption($options, $form, $empty = true, $tab = 3)
    {
        switch ($options[0]) {
            case 'string':
                return array(tab($tab) . '<option value="">' . $options[1] . '</option>');
                break;
            case 'var':
                $ret = array();
                if ($empty) {
                    $ret[] = tab($tab) . '<option value="">所有' . $form['title'] . '</option>';
                }
                $ret[] = tab($tab) . '{foreach name="$Think.config.conf.' . $options[1] . '" item=\'v\' key=\'k\'}';
                $ret[] = tab($tab + 1) . '<option value="{$k}">{$v}</option>';
                $ret[] = tab($tab) . '{/foreach}';

                return $ret;
                break;
            case 'think_var':
                $ret = array();
                if ($empty) {
                    $ret[] = tab($tab) . '<option value="">所有' . $form['title'] . '</option>';
                }
                $ret[] = tab($tab) . '{foreach name="$' . $options[1] . '" item=\'v\'}';
                $ret[] = tab($tab + 1) . '<option value="{$v.id}">{$v.name}</option>';
                $ret[] = tab($tab) . '{/foreach}';

                return $ret;
                break;
            case 'array':
                $ret = array();
                foreach ($options[1] as $option) {
                    $ret[] = tab($tab) . '<option value="' . $option[0] . '">' . $option[1] . '</option>';
                }

                return $ret;
                break;
        }
    }

	/**
     * 格式化选项值
     */
    private function parseOption($option, $string = false)
    {
        if (!$option) return array('string', $option);
        if (preg_match('/^\{\$(.*?)\}$/', $option, $match)) {
            // {$vo.item} 这种格式传入的变量
            return array('think_var', $match[1]);
        } elseif (preg_match('/^\{(.*?)\}$/', $option, $match)) {
            // {vo.item} 这种格式传入的变量
            return array('var', $match[1]);
        } else {
            if ($string) {
                return array('string', $option);
            }
            // key:val#key2:val2#val3#... 这种格式
            $ret = array();
            $arrVal = explode('#', $option);
            foreach ($arrVal as $val) {
                $keyVal = explode(':', $val, 2);
                if (count($keyVal) == 1) {
                    $ret[] = array('', $keyVal[0]);
                } else {
                    $ret[] = array($keyVal[0], $keyVal[1]);
                }
            }
            return array('array', $ret);
        }
    }
	
	//列出所有记录页面代码（片段）
	public function generateAllPageCode(){
		$templateFilePath = MODULE_PATH. "Template/View/allCode.html";
		$tableName = I('table');
		$TableName = tableNameToModelName($tableName);
		$tableInfoArray = getTableInfoArray($tableName);
		$columnNameKey = getColumnNameKey();
		// add by kofu 2017/2/8
		$return = $this->getSearchView($tableInfoArray, $columnNameKey, $TableName);
		$searchCode = !empty($return['search']) ? implode("\n", $return['search']) : '';
		$includeCode = !empty($return['include']) ? implode("\n", $return['include']) : '';
		$this->assign('searchCode', $searchCode);
		$this->assign('includeCode', $includeCode);

		$this->assign('tableName', $tableName);
		$this->assign('TableName', $TableName);
		$this->assign('tableInfoArray', $tableInfoArray);
		$this->assign('columnNameKey', $columnNameKey);
		$resultCode = $this->fetch($templateFilePath);
		return $resultCode;
	}
	
	public function allPageCode(){
		echo $this->generateAllPageCode();
	}
	
	
	//列出所有记录页面，读取并填充数据
	public function previewAllPage(){ 

		$tableName = I('table'); 
		$Model = M($tableName);
		$resultCode = "<table class=\"table table-striped table-bordered table-hover\">\r\n<thead>\r\n<tr>\r\n";
		$tableInfoArray = getTableInfoArray($tableName);
		foreach($tableInfoArray as $tableInfo){ //拼接表头
			$name = $tableInfo[getColumnNameKey()];
			$resultCode .= "<th><center>".$name."</center></th>\r\n";
		}
		$resultCode .= "<th>操 作</th>\r\n</tr>\r\n</thead>\r\n";
		for($i = 0; $i < 5; $i++){//填充5个数据
			foreach($tableInfoArray as $tableInfo){ 
				$resultCode .= "<td>" .$tableInfo[getColumnNameKey()]. "</td>\r\n";
			}
			$resultCode .= '<td><a href="'.U(tableNameToModelName($tableName).'/edit?id='.$i).'">编辑</a> | '	
					.'<a href="'.U(tableNameToModelName($tableName).'/delete?id='.$i).'" onclick=\'return confirm("确定删除吗？")\'>删除</a></td></tr>'."\r\n";
		}
		$resultCode .= "</table>\r\n";
		echo $resultCode;
	}
	
	//生成所有记录代码
	public function generateAllCode(){
		$tableName = I('table');
		$isPage = I('isPage');

		// add by kofu 2017/02/09
		$TableName = tableNameToModelName($tableName);
		$tableInfoArray = getTableInfoArray($tableName);
		$columnNameKey = getColumnNameKey();
		$code = $this->parseSearchCode($tableInfoArray, $columnNameKey, $TableName);
		$startCode 	= !empty($code['startCode']) ? implode("\n", $code['startCode']) : '';
		$endCode 	= !empty($code['endCode']) ? implode("\n", $code['endCode']) : '';
		$this->assign('startCode', $startCode);
		$this->assign('endCode', $endCode);

		$this->assign('tableName', $tableName);
		$this->assign('TableName', $TableName);//修正为驼峰命名，首字母大写
		$resultCode = $this->makeControllerTemplate("all.html");
		return $resultCode;
	}
	
	public function allCode(){
		echo $this->generateAllCode();
	}
	
	//生成新建页面代码（片段）
	public function generateAddPage(){ 
		$templateFilePath = MODULE_PATH. "Template/View/addCode.html";
		$tableName = I('table'); 
		$TableName = tableNameToModelName($tableName);
		$tableInfoArray = getTableInfoArray($tableName);
		$columnNameKey = getColumnNameKey();
		
		$this->assign('tableName', $tableName);
		$this->assign('TableName', $TableName);
		$this->assign('tableInfoArray', $tableInfoArray);
		$this->assign('columnNameKey', $columnNameKey);
		$resultCode = $this->fetch($templateFilePath);
		return $resultCode;
	}
	
	//生成新建前台页面代码
	public function addPage(){
		echo $this->generateAddPage();
	}
	
	//新建操作代码
	public function generateAddCode(){	
		$tableName = I('table'); 
		$this->assign('tableName', $tableName);
		$this->assign('TableName', tableNameToModelName($tableName));//修正为驼峰命名，首字母大写
		$resultCode = $this->makeControllerTemplate("add.html");
		return $resultCode;
	}
	
	public function addCode(){
		echo $this->generateAddCode();
	}
	
	//编辑页面
	public function generateEditPage(){	
		$templateFilePath = MODULE_PATH. "Template/View/editCode.html";
		$tableName = I('table'); 
		$TableName = tableNameToModelName($tableName);
		$tableInfoArray = getTableInfoArray($tableName);
		$columnNameKey = getColumnNameKey();
		
		$this->assign('tableName', $tableName);
		$this->assign('TableName', $TableName);
		$this->assign('tableInfoArray', $tableInfoArray);
		$this->assign('columnNameKey', $columnNameKey);
		$resultCode = $this->fetch($templateFilePath);
		return $resultCode;
	}

	public function editPage(){	
		echo $this->generateEditPage();
	}
	
	//生成编辑代码
	public function generateEditCode(){ 
		$tableName = I('table'); 
		$this->assign('tableName', $tableName);
		$this->assign('TableName', tableNameToModelName($tableName));//修正为驼峰命名，首字母大写
		$resultCode = $this->makeControllerTemplate("edit.html");
		return $resultCode;
	}
	
	public function editCode(){
		echo $this-> generateEditCode();
	}
	
	//删除代码
	public function generateDeleteCode(){
		$tableName = I('table'); 
		$this->assign('tableName', $tableName);
		$this->assign('TableName', tableNameToModelName($tableName));//修正为驼峰命名，首字母大写
		$resultCode = $this->makeControllerTemplate("delete.html");
		return $resultCode;
	}
	
	public function deleteCode(){
		echo $this->generateDeleteCode();
	}
	
	
	//生成所有代码对应的文件，
	public function creatAllFiles(){
		$tableName = I('selectTableName');
		$moduleName = I('moduleName');
		$controllerPath = APP_PATH. tableNameToModelName($moduleName). "/Controller/";
		for($i = 0;$i < count($tableName); $i++){
			$_POST['table'] = $tableName[$i];
			$viewPath = APP_PATH. tableNameToModelName($moduleName). "/View/".tableNameToModelName($tableName[$i])."/";
			$controllerStr = "<?php\r\n";
			$controllerStr .= "//由ThinkphpHelper自动生成,请根据需要修改\r\n";
			$controllerStr .= "namespace ".tableNameToModelName($moduleName)."\Controller;\r\n";
			$controllerStr .= "use Think\Controller;\r\n\r\n";
			$controllerStr .= "class ". tableNameToModelName($tableName[$i]) ."Controller extends Controller {\r\n";
			$controllerStr .= $this->generateAllCode()."\r\n\r\n";
			$controllerStr .= $this->generateAddCode()."\r\n\r\n";
			$controllerStr .= $this->generateEditCode()."\r\n\r\n";
			$controllerStr .= $this->generateDeleteCode()."\r\n\r\n}";
			
			$originalAllViewStr = $this->generateAllPageCode();
			$allViewStr = $this->makeViewTemplate("all.html", "管理".$tableName[$i], $originalAllViewStr);
			$originalAddViewStr = $this->generateAddPage();
			$addViewStr = $this->makeViewTemplate("add.html", "新建".$tableName[$i], $originalAddViewStr);
			$originalEditViewStr = $this->generateEditPage();
			$editViewStr = $this->makeViewTemplate("edit.html", "编辑".$tableName[$i], $originalEditViewStr);
			
			file_put_contents($controllerPath.tableNameToModelName($tableName[$i])."Controller.class.php", $controllerStr);//生成Controller文件
			FileUtil::createDir($viewPath);
			file_put_contents($viewPath."all.html", $allViewStr);
			file_put_contents($viewPath."add.html", $addViewStr);
			file_put_contents($viewPath."edit.html", $editViewStr);
		}
		echo "生成完成。";
	}
	
	
	//解析前台View模板
	public function makeViewTemplate($templateFileName, $operateTitle, $content){
		$templateFilePath = MODULE_PATH. "Template/View/" .$templateFileName;
		$this->assign('operateTitle', $operateTitle);
		$this->assign('content', $content);
		$fileContent = $this->fetch($templateFilePath);
		return $fileContent;
	}
	
	//解析后台Controller模板
	public function makeControllerTemplate($templateFileName){
		$templateFilePath = MODULE_PATH. "Template/Controller/" .$templateFileName;
		$this->assign('operateTitle', $operateTitle);
		$fileContent = $this->fetch($templateFilePath);
		return $fileContent;
	}

	public function parseSearchCode($tableInfoArray, $columnNameKey, $TableName) {
		$description 	= I('description');
		$needSearch 	= I('needSearch');
		$searchType 	= I('searchType');
		$option 		= I('option');
		$form = array();
		foreach ($tableInfoArray as $key => $value) {
			if ($needSearch[$key] == '1') {
				$data['name'] 			= $value[$columnNameKey];
				$data['description'] 	= $description[$key];
				$data['searchType'] 	= $searchType[$key];
				$data['option'] 		= $option[$key];
				$form[] = $data;
				unset($data);
			}
		}

		$startCode[] = tab(1) . '$where = array();';
		foreach ($form as $key => $value) {
	        switch ($value['searchType']) {
	            case 'select':
	            	$startCode[] = tab(1) . '$' . $value['name'] . ' = I(\'' . $value['name'] . '\', -1);';
	            	$startCode[] = tab(1) . 'if ($' . $value['name'] . ' != \'-1\') {';
	                $startCode[] = tab(2) . '$where[\'' . $value['name'] . '\'] = $' . $value['name'] . ';';
	                $startCode[] = tab(1) . '}';
	                $endCode[] = tab(1) . '$return[\'' . $value['name'] . '\'] = $' . $value['name'] . ';';
	                break;
	            case 'date':
					$startCode[] = tab(1) . '$startTime = I(\'startTime\');';
					$startCode[] = tab(1) . '$endTime 	= I(\'endTime\');';
					$startCode[] = tab(1) . 'if (!empty($startTime) && !empty($endTime)) {';
					$startCode[] = tab(2) . '$where[\'' . $value['name'] . '\'] = array(\'BETWEEN\', array("{$startTime}, {$endTime}"));';
					$startCode[] = tab(1) . '} else {';
					$startCode[] = tab(2) . '$startTime = strtotime(\'2015-1-1\');';
					$startCode[] = tab(2) . '$endTime = strtotime(\'+1 days\');';
					$startCode[] = tab(1) . '}';
					$endCode[] = tab(1) . '$return[\'startTime\'] = $startTime;';
					$endCode[] = tab(1) . '$return[\'endTime\'] = $endTime;';
	                break;
	            default:
	            	$startCode[] = tab(1) . '$' . $value['name'] . ' = I(\'' . $value['name'] . '\');';
	            	$startCode[] = tab(1) . 'if (!empty($' . $value['name'] . ')) {';
	                $startCode[] = tab(2) . '$where[\'' . $value['name'] . '\'] = array(\'LIKE\', "%{$' . $value['name'] . '}%");';
	                $startCode[] = tab(1) . '}';
	                $endCode[] = tab(1) . '$return[\'' . $value['name'] . '\'] = $' . $value['name'] . ';';
	                break;
	        }
		}
		$startCode[] = '';
		$return = array(
			'startCode'	=> $startCode,
			'endCode'	=> $endCode
		);
		return $return;
	}

	/**
	 * [createSearch description]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
	 * @return    [type]        [description]
	 */
	public function createSearch(){
		$table = I('table');
		$tableInfoArray = getTableInfoArray($table);
		$columnNameKey = getColumnNameKey();
		$str = "<div>\r\n<table>\r\n<tr>\r\n<td>字段</td>\r\n<td>标题</td>\r\n<td>表单搜索</td>\r\n<td>搜索类型</td>\r\n<td>选项值</td>\r\n</tr>\r\n";
		foreach ($tableInfoArray as $key => $value) {
			$str .= "<tr>\r\n<td>{$value[$columnNameKey]}</td>\r\n<td><input type='text' name='description[]' placeholder='中文描述'></td>\r\n<td>是<input type='radio' class='needSearch' name='need_search[{$key}]' value='1'>否<input type='radio' name='need_search[{$key}]' class='needSearch' value='0' checked></td>\r\n<td><select name='searchType[]'><option value='text'>text</option><option value='select'>select</option><option value='date'>date</option></select></td>\r\n<td><input type='text' name='option[]' placeholder='例：-1:请选择#0:否#1:是'></td>\r\n</tr>\r\n";
		}
		$str .= "</table>\r\n</div>";
		echo $str;
	}
}