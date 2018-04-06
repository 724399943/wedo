<?php
namespace TPH\Controller;
use Think\Controller;

class BuildController extends Controller {
    private $moduleName;
    private $tableName;
    private $build = true;

    public function _initialize() {
        define(DS, DIRECTORY_SEPARATOR);
    }

    public function index() {
        $this->assign('db_prefix', C('DB_PREFIX'));
        $this->assign('tableNameList', getTableNameList());
        $this->assign('moduleNameList', getModuleNameList());
        $this->display();
    }

    /**
     * [createTbodyFormCode description]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function createTbodyFormCode() {
        $templateFilePath = MODULE_PATH. "Template/template/tbodyFormCode.html";
        $tableName = I('table');
        $tableInfoArray = getTableInfoArray($tableName);
        $columnNameKey  = getColumnNameKey();

        $this->assign('tableInfoArray', $tableInfoArray);
        $this->assign('columnNameKey', $columnNameKey);
        $resultCode = $this->fetch($templateFilePath);
        echo $resultCode;
    }

    /**
     * [createTbodyForm2Code description]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function createTbodyForm2Code() {
        $templateFilePath = MODULE_PATH. "Template/template/tbodyForm2Code.html";
        $tableName = I('table');
        $tableInfoArray = getTableInfoArray($tableName);
        $columnNameKey  = getColumnNameKey();

        $this->assign('tableInfoArray', $tableInfoArray);
        $this->assign('columnNameKey', $columnNameKey);
        $resultCode = $this->fetch($templateFilePath);
        echo $resultCode;
    }

    /**
     * [createFiles description]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function createFiles() {
        $form = I('form');
        $joinForm = I('joinForm');
        $this->tableName = I('tableName');
        $this->moduleName = I('moduleName');
        $titleName  = I('titleName');
        $this->tableName = tableNameToModelName(replaceDbPrefix($this->tableName));
        $this->moduleName = tableNameToModelName($this->moduleName);
        $tableName2 = I('tableName2');
        $tableName2 = (!empty($tableName2)) ? tableNameToModelName(replaceDbPrefix($tableName2)) : '';
        $viewModuleOn1 = I('viewModuleOn1');
        $viewModuleOn2 = I('viewModuleOn2');
        $joinType = I('joinType');

        // 检查目录是否可写
        $pathCheck = APP_PATH . $this->moduleName . DS;
        if (!self::checkWritable($pathCheck)) {
            throw new \Think\Exception("目录没有权限不可写，请执行一下命令修改权限：<br>chmod -R 755 " . realpath($pathCheck), 403);
        }
        // 检查模块目录下的Controller目录是否可写
        $pathCheck = APP_PATH . $this->moduleName . DS . 'Controller' . DS;
        if (!self::checkWritable($pathCheck)) {
            throw new \Think\ExceptionException("目录没有权限不可写，请执行一下命令修改权限：<br>chmod -R 755 " . realpath($pathCheck), 403);
        }
        // 检查模块目录下的Model目录是否可写
        $pathCheck = APP_PATH . $this->moduleName . DS . 'Model' . DS;
        if (!self::checkWritable($pathCheck)) {
            throw new \Think\ExceptionException("目录没有权限不可写，请执行一下命令修改权限：<br>chmod -R 755 " . realpath($pathCheck), 403);
        }
        // 检查模块目录下的View目录是否可写
        $pathCheck = APP_PATH . $this->moduleName . DS . 'View' . DS;
        if (!self::checkWritable($pathCheck)) {
            throw new \Think\ExceptionException("目录没有权限不可写，请执行一下命令修改权限：<br>chmod -R 755 " . realpath($pathCheck), 403);
        }

        $path = APP_PATH. $this->moduleName . DS . "View" . DS . $this->tableName . DS;
        // 生成目录
        FileUtil::createDir($path);
        $pathTemplate = APP_PATH . "TPH" . DS . "Template" . DS . "template" . DS;
        $fileName = APP_PATH . $this->moduleName . DS . 'Controller' . DS . $this->tableName . "Controller.class.php";
        $code = $this->parseCode($form);
        $searchCode = $this->parseSearchCode($form, $joinForm);
        $joinModelCode = $this->parseJoinModelCode($joinForm);
        $code = array_merge($code, $searchCode, $joinModelCode);

        $parameter = array(
            'path' => $path,
            'pathTemplate' => $pathTemplate,
            'fileName' => $fileName,
            'code' => $code,
            'titleName' => $titleName,
            'build' => $this->build,
            'tableName2' => $tableName2,
            'viewModuleOn1' => $viewModuleOn1,
            'viewModuleOn2' => $viewModuleOn2,
            'joinType' => $joinType,
        );
        $this->buildAll($parameter);
        exit(statusCode());
    }

    public function createAllCode() {
        $form = I('form');
        $joinForm = I('joinForm');
        $this->tableName  = I('tableName');
        $this->moduleName = I('moduleName');
        $titleName = I('titleName');
        $this->tableName = tableNameToModelName(replaceDbPrefix($this->tableName));
        $this->moduleName = tableNameToModelName($this->moduleName);
        $tableName2 = I('tableName2');
        $tableName2 = (!empty($tableName2)) ? tableNameToModelName(replaceDbPrefix($tableName2)) : '';
        $viewModuleOn1 = I('viewModuleOn1');
        $viewModuleOn2 = I('viewModuleOn2');
        $joinType = I('joinType');

        $path = APP_PATH. $this->moduleName . DS . "View" . DS . $this->tableName . DS;
        $pathTemplate = APP_PATH . "TPH" . DS . "Template" . DS . "template" . DS;
        $fileName = APP_PATH . $this->moduleName . DS . 'Controller' . DS . $this->tableName . "Controller.class.php";
        $code = $this->parseCode($form);
        $searchCode = $this->parseSearchCode($form, $joinForm);
        $joinModelCode = $this->parseJoinModelCode($joinForm);
        $code = array_merge($code, $searchCode, $joinModelCode);

        $this->build = false;
        $parameter = array(
            'path' => $path,
            'pathTemplate' => $pathTemplate,
            'fileName' => $fileName,
            'code' => $code,
            'titleName' => $titleName,
            'build' => $this->build,
            'tableName2' => $tableName2,
            'viewModuleOn1' => $viewModuleOn1,
            'viewModuleOn2' => $viewModuleOn2,
            'joinType' => $joinType,
        );

        $result['index'] = $this->buildIndex($parameter);
        $result['add'] = $this->buildAdd($parameter);
        $result['edit'] = $this->buildEdit($parameter);
        $result['controller'] = $this->buildController($parameter);
        $result['model'] = $this->buildModel($parameter);
        $result['status'] = '200000';
        exit(json_encode($result));
    }

    public function buildAll($parameter) {
        // 创建文件
        $this->buildIndex($parameter);
        $this->buildAdd($parameter);
        $this->buildEdit($parameter);
        $this->buildController($parameter);
        $this->buildModel($parameter);
    }

    /**
     * 创建 index.html 文件
     */
    private function buildIndex($parameter)
    {
        $path = $parameter['path'];
        $pathTemplate = $parameter['pathTemplate'];
        $fileName = $parameter['fileName'];
        $code = $parameter['code'];
        $titleName = $parameter['titleName'];
        $build = $parameter['build'];

        $script = '';
        $script = $code['script_search'] . "\n"
            . '<script>' . "\n"
            . tab(1) . '$(function () {' . "\n"
            . $code['jquery_search']
            . tab(1) . '})' . "\n"
            . '</script>' . "\n";
        
        $form = implode("\n" . tab(2), $code['search']);
        $style = $code['style_search'];
        $colspan = count($code['th']) + 1;
        $th = implode("\n" . tab(4), $code['th']);
        $td = implode("\n" . tab(6), $code['td']);
        $tdMenu = '<if condition="checkActionAuth(\'' . $this->tableName . '-edit' . $this->tableName . '\') || checkActionAuth(\'' . $this->tableName . '-del' . $this->tableName . '\')">' . "\n" . 
            tab(8) . '<if condition="checkActionAuth(\'' . $this->tableName . '-edit' . $this->tableName . '\')">' . "\n" . 
            tab(9) . '<a class="stdbtn btn_lime" href="{:U(\'' . $this->tableName . '/edit' . $this->tableName . '\', array(\'id\' => $vo[\'id\']))}">编辑</a>&nbsp;&nbsp;' . "\n" . 
            tab(8) . '</if>' . "\n" . 
            tab(8) . '<if condition="checkActionAuth(\'' . $this->tableName . '-del' . $this->tableName . '\')">' . "\n" . 
            tab(9) . '<a class="stdbtn btn_lime" href="{:U(\'' . $this->tableName . '/del' . $this->tableName . '\', array(\'id\' => $vo[\'id\']))}">删除</a>&nbsp;&nbsp;' . "\n" . 
            tab(8) . '</if>' . "\n" . 
            tab(7) . '<else/>' . "\n" . 
            tab(8) . '无权限访问' . "\n" . 
            tab(7) . '</if>' . "\n";

        $template = file_get_contents($pathTemplate . "index.tpl");
        $file = $path . "index.html";

        if ($build === true) {
            return file_put_contents($file, str_replace(
                    array("[TITLE]", "[STYLE]", "[TABLENAME]", "[FORM]", "[TH]", "[COLSPAN]", "[TD]", "[TD_MENU]", "[SCRIPT]"),
                    array($titleName, $style, $this->tableName, $form, $th, $colspan, $td, $tdMenu, $script),
                    $template
                )
            );
        } else {
            return str_replace(
                    array("[TITLE]", "[STYLE]", "[TABLENAME]", "[FORM]", "[TH]", "[COLSPAN]", "[TD]", "[TD_MENU]", "[SCRIPT]"),
                    array($titleName, $style, $this->tableName, $form, $th, $colspan, $td, $tdMenu, $script),
                    $template
                );
        }
    }

    /**
     * 创建 add.html 文件
     */
    private function buildAdd($parameter)
    {
        $path = $parameter['path'];
        $pathTemplate = $parameter['pathTemplate'];
        $fileName = $parameter['fileName'];
        $code = $parameter['code'];
        $titleName = $parameter['titleName'];
        $build = $parameter['build'];

        $template = file_get_contents($pathTemplate . "add.tpl");
        $file = $path . "add" . $this->tableName . ".html";
        if ($build === true) {
            return file_put_contents($file, str_replace(
                array("[TITLE]", "[STYLE]", "[ROWS]", "[TABLENAME]", "[SCRIPT]", "[JQUERY]"),
                array($titleName, $code['style_common'], $code['add'], $this->tableName, implode("", $code['script_src']), $code['jquery_add']),
                $template)
            );
        } else {
            return str_replace(
                array("[TITLE]", "[STYLE]", "[ROWS]", "[TABLENAME]", "[SCRIPT]", "[JQUERY]"),
                array($titleName, $code['style_common'], $code['add'], $this->tableName, implode("", $code['script_src']), $code['jquery_add']),
                $template);
        }
    }

    /**
     * 创建 edit.html 文件
     */
    private function buildEdit($parameter)
    {
        $path = $parameter['path'];
        $pathTemplate = $parameter['pathTemplate'];
        $fileName = $parameter['fileName'];
        $code = $parameter['code'];
        $titleName = $parameter['titleName'];
        $build = $parameter['build'];

        $template = file_get_contents($pathTemplate . "edit.tpl");
        $file = $path . "edit" . $this->tableName . ".html";

        if ($build === true) {
            return file_put_contents($file, str_replace(
                array("[TITLE]", "[STYLE]", "[ROWS]", "[TABLENAME]", "[SCRIPT]", "[JQUERY]"),
                array($titleName, $code['style_common'], $code['edit'], $this->tableName, implode("", $code['script_src']), $code['jquery_edit']),
                $template)
            );
        } else {
            return str_replace(
                array("[TITLE]", "[STYLE]", "[ROWS]", "[TABLENAME]", "[SCRIPT]", "[JQUERY]"),
                array($titleName, $code['style_common'], $code['edit'], $this->tableName, implode("", $code['script_src']), $code['jquery_edit']),
                $template);
        }
    }

    /**
     * 创建控制器文件
     */
    private function buildController($parameter)
    {
        $path = $parameter['path'];
        $pathTemplate = $parameter['pathTemplate'];
        $fileName = $parameter['fileName'];
        $code = $parameter['code'];
        $titleName = $parameter['titleName'];
        $build = $parameter['build'];

        $template = file_get_contents($pathTemplate . "Controller.tpl");
        $file = $fileName;
        $startCode  = !empty($code['startCode']) ? implode("\n", $code['startCode']) : '';
        $endCode    = !empty($code['endCode']) ? implode("\n", $code['endCode']) : '';

        if ($build === true) {
            return file_put_contents($file, str_replace(
                    array("[MODULE]", "[NAME]", "[TABLENAME]", "[STARTCODE]", "[ENDCODE]"),
                    array($this->moduleName, $this->tableName . 'Controller', $this->tableName, $startCode, $endCode),
                    $template
                )
            );
        } else {
            return str_replace(
                    array("[MODULE]", "[NAME]", "[TABLENAME]", "[STARTCODE]", "[ENDCODE]"),
                    array($this->moduleName, $this->tableName . 'Controller', $this->tableName, $startCode, $endCode),
                    $template
                );
        }
    }

    /**
     * 创建模型文件
     */
    private function buildModel($parameter)
    {
        $path = $parameter['path'];
        $pathTemplate = $parameter['pathTemplate'];
        $fileName = $parameter['fileName'];
        $code = $parameter['code'];
        $titleName = $parameter['titleName'];
        $build = $parameter['build'];
        $tableName2 = $parameter['tableName2'];
        $viewModuleOn1 = $parameter['viewModuleOn1'];
        $viewModuleOn2 = $parameter['viewModuleOn2'];
        $joinType = $parameter['joinType'];

        // 直接生成空模板
        $template = file_get_contents($pathTemplate . "Model.tpl");
        $file = APP_PATH . $this->moduleName . DS . "Model" . DS . $this->tableName . "ViewModel.class.php";
        $viewFields = tab(1) . 'public $viewFields = array(' . "\n" 
                    . tab(2) . '\''. $this->tableName . '\'' . '=>array(' . $code['table1Fields'] . "),\n" .(!empty($tableName2) ? tab(2) . '\''. $tableName2 . '\'' . '=>array('  . $code['table2Fields'] . ', \'_on\'=>\'' . $this->tableName . '.' . $viewModuleOn1 . '=' . $tableName2 . '.' . $viewModuleOn2 . '\'' . (($joinType != 'JOIN') ? ', \'_type\'=>\'' . $joinType . '\'' : '') . ")," : '') . "\n" 
                    . tab(1) . ');';

        if ($build === true) {
            return file_put_contents($file, str_replace(
                    array("[MODULE]", "[NAME]", "[VIEWFIELDS]"),
                    array($this->moduleName, $this->tableName, $viewFields),
                    $template
                )
            );
        } else {
            return str_replace(
                    array("[MODULE]", "[NAME]", "[VIEWFIELDS]"),
                    array($this->moduleName, $this->tableName, $viewFields),
                    $template
                );
        }
    }

    /**
     * 检查当前模块目录是否可写
     * @return bool
     */
    public static function checkWritable($path = '')
    {
        try {
            $path = $path ? $path : APP_PATH . 'Admin' . DS;
            $testFile = $path . "bulid.test";
            if (!file_put_contents($testFile, "test")) {
                return false;
            }
            // 解除锁定
            unlink($testFile);

            return true;
        } catch (\Think\Exception $e) {
            return false;
        }
    }

    /**
     * 创建文件的代码
     * @return array
     * return [
     *     'search'            => $search,
     *     'th'                => $th,
     *     'td'                => $td,
     *     'add'               => $addField,
     *     'edit'              => $editField,
     *     'script_src'        => $scriptSrc,
     *     'script_search'     => $scriptSearch,
     *     'style_common'      => $styleCommon,
     *     'style_search'      => $styleSearch,
     *     'jquery_add'        => $jqueryAdd,
     *     'jquery_edit'       => $jqueryEdit,
     *     'jquery_search'     => $jquerySearch,
     * ];
     */
    private function parseCode($postForm)
    {
        // 生成 form.html 文件的代码
        $search = array();
        $search[] = tab(2) . '<form class="order-list" method="GET" action="{:U(\'' . $this->tableName . '/index\')}">';
        $search[] = tab(1) . '<p class="select-style1">';
        // 生成 th.html 文件的代码
        $th = array();
        // 生成 td.html 文件的代码
        $td = array();
        // 生成 add.html 文件的代码
        $addField = '';
        // 生成 edit.html 文件的代码
        $editField = '';
        // index.html脚本引入
        $scriptSearch = '';
        $jquerySearch = '';
        $jqueryAdd = '';
        $jqueryEdit = '';
        $styleSearch = '';
        $styleCommon = '';
        $table1Fields = '';
        if (isset($postForm) && $postForm) {
            foreach ($postForm as $form) {
                // 状态选择的自动设置为单选框
                if ($form['name'] == 'status') {
                    $form['type'] = 'radio';
                    $form['option'] = '1:启用#0:禁用';
                }
                $options = $this->parseOption($form['option']);
                // 表单搜索
                if (isset($form['search']) && $form['search']) {
                    // 表单搜索
                    switch ($form['search_type']) {
                        case 'select':
                            // 默认选中
                            $jquerySearch .= tab(2) . '$("[name=\'' . $form['name'] . '\']").find("[value=\'{$return\'' . $form['name'] . '\']}\']").attr("selected", true);' . "\n";
                            $search[] = tab(2) . '<label>' . $form['title'] . '：</label>';
                            $search[] = tab(4) . '<div class="select-box" style="width:250px">';
                            $search[] = tab(5) . '<select name="' . $form['name'] . '" class="select">';
                            $search = array_merge($search, $this->getOption($options, $form, true, 6));
                            $search[] = tab(5) . '</select>';
                            $search[] = tab(4) . '</div>';
                            break;
                        case 'date':
                            $jquerySearch .= tab(2) . '$("#staDatartTime").val(moment.unix($("#staDatartTime").attr("date-time")).format("YYYY-MM-DD HH:mm:ss"));' . "\n" 
                                . tab(2) . '$("#endDataTime").val(moment.unix($("#endDataTime").attr("date-time")).format("YYYY-MM-DD HH:mm:ss"));' . "\n" 
                                . tab(2) . '$("#staDatartTime").datetimepicker({format:"Y-m-d H:i:s", onChangeDateTime:function(dp, $input) {$("#startTime").val(moment($input.val()).unix().valueOf());}});' . "\n" 
                                . tab(2) . '$("#endDataTime").datetimepicker({format:"Y-m-d H:i:s", onChangeDateTime:function(dp, $input) {$("#endTime").val(moment($input.val()).unix().valueOf());}});' . "\n";
                            $scriptSearch .= $this->getDatepickerScriptCode();
                            $styleSearch .= $this->getDatepickerStyleCode();
                            $search[] = tab(2) . '<label>开始时间：</label>' . "\n" 
                                . tab(4) . '<input type="text" id="startDateTime" date-time="{$return[\'startTime\']}" style="margin-right: 0px">' . "\n" 
                                . tab(4) . '<input type="hidden" name="startTime" id="startTime" value="{$return[\'startTime\']}" >-' . "\n" 
                                . tab(4) . '<input type="text" id="endDateTime" date-time="{$return[\'endTime\']}">' . "\n" 
                                . tab(4) . '<input type="hidden" name="endTime" id="endTime" value="{$return[\'endTime\']}">';
                            break;
                        default:
                            $search[] = tab(2) . '<label>' . $form['title'] . '：</label>' . "\n" 
                                . tab(4) . '<input type="text" class="input-text" style="width:250px" '
                                . 'placeholder="' . $form['title'] . '" name="' . $form['name'] . '" '
                                . 'value="{$return[\'' . $form['name'] . '\']}" '
                                . '>';
                            break;
                    }
                }

                // 字段是否在列表显示
                if (isset($form['index_view']) && $form['index_view']) {
                    $th[] = '<th width="">' . $form['title'] . "</th>";
                    $table1Fields .= '\'' . $form['name'] . '\'' . ',';
                    // $td[] = '<td>{$vo.' . $form['name'] . '}</td>';
                    switch ($form['type']) {
                        case 'radio':
                        case 'checkbox':
                            # code...
                            break;
                        case 'date':
                            $td[] = '<td>{$vo.' . $form['name'] . '|date="Y-m-d H:i:s", ###}</td>';
                            break;
                        case 'image':
                            $td[] = '<td><img width="100px" height="100px" src="{$vo.' . $form['name'] . '}" alt="' . $form['title'] . '"></td>';
                            break;
                        case 'textarea':
                        case 'password':
                        case 'number':
                        default:
                            $td[] = '<td>{$vo.' . $form['name'] . '}</td>';
                            break;
                    }
                }

                // 像id这种白名单字段不需要自动生成到编辑页
                if (!in_array($form['name'], array('id')) && isset($form['other_view']) && $form['other_view']) {
                    $lineCode = tab(3) . '<div class="line-dete">' . "\n"
                        . tab(4) . '<label>'
                        . $form['title'] . '：</label>' . "\n"
                        . tab(4) . '<span class="field">' . "\n";
                    $addField   .= $lineCode;
                    $editField  .= $lineCode;
                    switch ($form['type']) {
                        case "radio":
                        case "checkbox":
                            if ($form['type'] == "radio") {
                                // radio类型的控件进行编辑状态赋值，checkbox类型控件请自行根据情况赋值
                                $jqueryEdit .= tab(2) . '$("[name=\'' . $form['name'] . '\'][value=\'{$vo.' . $form['name']. '}\']").attr("checked", true);' . "\n";
                            } else {
                                $jqueryEdit .= tab(2) . 'var checks = \'' . $form['default'] . '\'.split(",");' . "\n" 
                                    . tab(2) . 'if (checks.length > 0){' . "\n" 
                                    . tab(3) . 'for (var i in checks){' . "\n" 
                                    . tab(4) . '$("[name=\'' . $form['name'] . '[]\'][value=\'"+checks[i]+"\']").attr("checked", true);' . "\n" 
                                    . tab(3) . '}' . "\n" 
                                    . tab(2) . '}'. "\n";
                            }

                            // 默认只生成一个空的示例控件，请根据情况自行复制编辑
                            $name = $form['name'] . ($form['type'] == "checkbox" ? '[]' : '');

                            switch ($options[0]) {
                                case 'string':
                                    $editField .= $this->getCheckbox($form, $name, $options[1], '', 0);
                                    break;
                                case 'var':
                                    $editField .= tab(4) . '{foreach name="$Think.config.conf.' . $options[1] . '" item=\'v\' key=\'k\'}' . "\n"
                                        . $this->getCheckbox($form, $name, '{$v}', '{$k}', '{$k}')
                                        . tab(4) . '{/foreach}' . "\n";
                                    break;
                                case 'array':
                                    foreach ($options[1] as $option) {
                                        $editField .= $this->getCheckbox($form, $name, $option[1], $option[0], $option[0]);
                                    }
                                    break;
                            }
                            break;
                        case "select":
                            // select类型的控件进行编辑状态赋值
                            $jqueryEdit  .= tab(2) . '$("[name=\'' . $form['name'] . '\']").find("[value=\'{$vo.' . $form['name'] . '}\']").attr("selected", true);' . "\n";
                            $selectCode = tab(5) . '<div class="select-box">' . "\n"
                                . tab(6) . '<select name="' . $form['name'] . '">' . "\n"
                                . implode("\n", $this->getOption($options, $form, false, 6)) . "\n"
                                . tab(6) . '</select>' . "\n"
                                . tab(5) . '</div>' . "\n";

                            $addField .= $selectCode;
                            $editField .= $selectCode;
                            break;
                        case "textarea":
                            $addField .= $this->getTextareaCode(1, $form);
                            $editField .= $this->getTextareaCode(2, $form);
                            break;
                        case "date":
                            $styleCommon .= $this->getDatepickerStyleCode();
                            $addField .= $this->getDateInputCode(2, $form);
                            $editField .= $this->getDateInputCode(2, $form);
                            $scriptSrc[] = $this->getDatepickerScriptCode();
                            $datepickerJquery = tab(2) . '$("#J' . $form['name'] . '").val(moment.unix($("#J' . $form['name'] . '").attr("date-time")).format("YYYY-MM-DD HH:mm:ss"));' . "\n" 
                                . tab(2) . '$("#J' . $form['name'] . '").datetimepicker({format:"Y-m-d H:i:s", onChangeDateTime:function(dp, $input) {$("#' . $form['name'] . '").val(moment($input.val()).unix().valueOf());}});' . "\n";
                            $jqueryAdd .= $datepickerJquery;
                            $jqueryEdit .= $datepickerJquery;
                            break;
                        case "image":
                            $addField .= $this->getImageCode(1, $form);
                            $editField .= $this->getImageCode(2, $form);;
                            $scriptSrc[] = "\n" . '<script type="text/javascript" src="__PUBLIC__/Admin/js/ajaxfileupload.js"></script>';
                            $uploadCode = $this->getUploadScriptCode();
                            $jqueryAdd .= $uploadCode;
                            $jqueryEdit .= $uploadCode;
                            break;
                        case "text":
                        case "password":
                        case "number":
                        default:
                            $addField .= $this->getInputCode(1, $form);
                            $editField .= $this->getInputCode(2, $form);
                            break;
                    }
                    $lastCode =  tab(4) . '</span>' . "\n"
                        . tab(3) . '</div>' . "\n";
                    $addField .= $lastCode;
                    $editField .= $lastCode;
                }
            }
        }
        if (count($search) > 1) {
            // 有设置搜索则显示
            $search[] = tab(2) . '<input type="submit" value="搜索"></input>';
            $search[] = tab(1) . '</p>';
            $search[] = '</form>';
            $search[] = '';
        } else {
            // 不设置将form.html置空
            $search = array();
        }

        return array(
            'search'            => $search,
            'th'                => $th,
            'td'                => $td,
            'add'               => $addField,
            'edit'              => $editField,
            'script_src'        => $scriptSrc,
            'script_search'     => $scriptSearch,
            'style_common'      => $styleCommon,
            'style_search'      => $styleSearch,
            'jquery_add'        => $jqueryAdd,
            'jquery_edit'       => $jqueryEdit,
            'jquery_search'     => $jquerySearch,
            'table1Fields'      => trim($table1Fields, ',')
        );
    }

    public function getInputCode($type, $form) {
        $code = tab(5) . '<input type="' . $form['type'] . '" class="smallinput" '
            . 'placeholder="' . $form['title'] . '" name="' . $form['name'] . '" '
            . 'value="' . ($type ==  1 ? '' : '{$vo.' . $form['name'] . '}') . '">' . "\n";
        return $code;
    }

    public function getImageCode($type, $form) {
        $code = tab(5) . '<div class="file-box">' . "\n"  
            . tab(6) . '<input type="hidden" id="image" name="' . $form['name'] . '" value="' . ($type ==  1 ? '' : '{$vo.' . $form['name'] . '}') . '">' . "\n"  
            . tab(6) . '<input type="file" class="file" name="up-pic" id="up-pic" value="">' . "\n"  
            . ($type ==  1 ? '' : tab(6) . '<notempty name="vo.' . $form['name'] . '">' . "\n" )
            . ($type ==  1 ? tab(6) : tab(7)) . '<img class="Jpic" src="' . ($type ==  1 ? '' : '{$vo.' . $form['name'] . '}') . '" alt="" style="display:' . ($type ==  1 ? 'none' : 'block') . '">' . "\n"  
            . ($type ==  1 ? '' : tab(6) . '</notempty>' . "\n" )
            . tab(5) . '</div>' . "\n";
        return $code;
    }

    public function getUploadScriptCode() {
        $code = tab(2) . '$(document).on(\'change\', \'#up-pic\', function() {' . "\n" . 
                tab(3) . '$.ajaxFileUpload({' . "\n" .  
                tab(4) . 'url: "{:U(\''. $this->tableName .'/photoSave\')}",' . "\n" .  
                tab(4) . 'secureuri: false,' . "\n" .  
                tab(4) . 'fileElementId: "up-pic",' . "\n" .  
                tab(4) . 'dataType: "json",' . "\n" .  
                tab(4) . 'success: function (data, status) {' . "\n" .  
                tab(5) . 'if(data.error != \'\') {' . "\n" .  
                tab(6) . 'alert(data.error);' . "\n" .  
                tab(5) . '} else {' . "\n" .  
                tab(6) . '$(".Jpic").attr("src", data.src).show();' . "\n" .  
                tab(6) . '$("#image").val(data.src);' . "\n" .  
                tab(5) . '}' . "\n" .  
                tab(3) . '},error: function (data, status, e) {' . "\n" .  
                tab(4) . 'var html=\'<div class="title">提示</div><div class="pop-false">\' + e + \'</div>\';' . "\n" .  
                tab(5) . 'popbox(html);' . "\n" .  
                tab(4) . '}' . "\n" .  
                tab(3) . '})' . "\n" .  
                tab(2) . '});' . "\n";
        return $code;
    }

    public function getTextareaCode($type, $form) {
        $code = tab(5) . '<textarea name="' . $form['name'] . '" '
            . 'style="width:800px;height:500px;">'
            . ($type ==  1 ? '' : '{$vo.' . $form['name'] . '}')
            . '</textarea>' . "\n";
        return $code;
    }

    public function getDatepickerStyleCode() {
        $code = '<link rel="stylesheet" href="__PUBLIC__/Admin/css/jquery.datetimepicker.css" type="text/css" />' . "\n" . 
            tab(1) . '<style type="text/css">' . "\n" .  
            tab(2) . '.givepop-box{ width: 400px; background: #fff; overflow: hidden; }' . "\n" . 
            tab(2) . '.givepop-box .g-line{ line-height: 32px; margin-top: 10px; padding-left: 40px; }' . "\n" . 
            tab(2) . '/*.g-line input{ width: 200px; }*/' . "\n" . 
            tab(2) . ' .givepop-box .stdbtn{ margin-left: 100px; }' . "\n" . 
            tab(1) . '</style>';
        return $code;
    }

    public function getDatepickerScriptCode() {
        $code = "\n" . '<script type="text/javascript" src="__PUBLIC__/Admin/js/jquery.datetimepicker.js"></script>' 
            . "\n" . '<script type="text/javascript" src="__PUBLIC__/Admin/js/moment.min.js"></script>';
        return $code;
    }

    public function getDateInputCode($type, $form) {
        $code = tab(5) . '<input type="text" class="smallinput" '
            . 'date-time="'. ($type ==  1 ? time() : '{$vo.' . $form['name'] . '}') . '" id="J'
            . $form['name'] . '">' . "\n"
            . tab(5) . '<input type="hidden" '
            . 'name="' . $form['name'] . '" '
            . 'value="'. ($type ==  1 ? time() : '{$vo.' . $form['name'] . '}') . '" id="'
            . $form['name'] . '">' . "\n";
        return $code;
    }

    public function parseSearchCode($postForm, $joinForm) {
        $startCode = array();
        $endCode = array();
        if (isset($postForm) && $postForm) {
            foreach ($postForm as $form) {
                // 表单搜索
                if (isset($form['search']) && $form['search']) {
                    switch ($form['search_type']) {
                        case 'select':
                            $startCode[] = tab(2) . '$' . $form['name'] . ' = I(\'' . $form['name'] . '\', -1);';
                            $startCode[] = tab(2) . 'if ($' . $form['name'] . ' != \'-1\') {';
                            $startCode[] = tab(3) . '$where[\'' . $form['name'] . '\'] = $' . $form['name'] . ';';
                            $startCode[] = tab(3) . '$link_parameter .= \'/' . $form['name'] . '/\' .$' . $form['name'] . ';';
                            $startCode[] = tab(2) . '}';
                            $endCode[] = tab(2) . '$return[\'' . $form['name'] . '\'] = $' . $form['name'] . ';';
                            break;
                        case 'date':
                            $startCode[] = tab(2) . '$startTime = I(\'startTime\');';
                            $startCode[] = tab(2) . '$endTime   = I(\'endTime\');';
                            $startCode[] = tab(2) . 'if (!empty($startTime) && !empty($endTime)) {';
                            $startCode[] = tab(3) . '$where[\'' . $form['name'] . '\'] = array(\'BETWEEN\', array("{$startTime}, {$endTime}"));';
                            $startCode[] = tab(3) . '$link_parameter .= \'/startTime/\' . $startTime . \'/endTime/\' . $endTime;';
                            $startCode[] = tab(2) . '} else {';
                            $startCode[] = tab(3) . '$startTime = strtotime(\'2015-1-1\');';
                            $startCode[] = tab(3) . '$endTime = strtotime(\'+1 days\');';
                            $startCode[] = tab(2) . '}';
                            $endCode[] = tab(2) . '$return[\'startTime\'] = $startTime;';
                            $endCode[] = tab(2) . '$return[\'endTime\'] = $endTime;';
                            break;
                        default:
                            $startCode[] = tab(2) . '$' . $form['name'] . ' = I(\'' . $form['name'] . '\');';
                            $startCode[] = tab(2) . 'if (!empty($' . $form['name'] . ')) {';
                            $startCode[] = tab(3) . '$where[\'' . $form['name'] . '\'] = array(\'LIKE\', "%{$' . $form['name'] . '}%");';
                            $startCode[] = tab(3) . '$link_parameter .= \'/' . $form['name'] . '/\' .$' . $form['name'] . ';';
                            $startCode[] = tab(2) . '}';
                            $endCode[] = tab(2) . '$return[\'' . $form['name'] . '\'] = $' . $form['name'] . ';';
                            break;
                    }
                }
            }
        }
        if (isset($joinForm) && $joinForm) {
            foreach ($joinForm as $form) {
                // 表单搜索
                if (isset($form['search']) && $form['search']) {
                    $form['name'] = !empty($form['asname']) ? $form['asname'] : $form['name'];
                    switch ($form['search_type']) {
                        case 'select':
                            $startCode[] = tab(2) . '$' . $form['name'] . ' = I(\'' . $form['name'] . '\', -1);';
                            $startCode[] = tab(2) . 'if ($' . $form['name'] . ' != \'-1\') {';
                            $startCode[] = tab(3) . '$where[\'' . $form['name'] . '\'] = $' . $form['name'] . ';';
                            $startCode[] = tab(3) . '$link_parameter .= \'/' . $form['name'] . '/\' .$' . $form['name'] . ';';
                            $startCode[] = tab(2) . '}';
                            $endCode[] = tab(2) . '$return[\'' . $form['name'] . '\'] = $' . $form['name'] . ';';
                            break;
                        case 'date':
                            $startCode[] = tab(2) . '$startTime = I(\'startTime\');';
                            $startCode[] = tab(2) . '$endTime   = I(\'endTime\');';
                            $startCode[] = tab(2) . 'if (!empty($startTime) && !empty($endTime)) {';
                            $startCode[] = tab(3) . '$where[\'' . $form['name'] . '\'] = array(\'BETWEEN\', array("{$startTime}, {$endTime}"));';
                            $startCode[] = tab(3) . '$link_parameter .= \'/startTime/\' . $startTime . \'/endTime/\' . $endTime;';
                            $startCode[] = tab(2) . '} else {';
                            $startCode[] = tab(3) . '$startTime = strtotime(\'2015-1-1\');';
                            $startCode[] = tab(3) . '$endTime = strtotime(\'+1 days\');';
                            $startCode[] = tab(2) . '}';
                            $endCode[] = tab(2) . '$return[\'startTime\'] = $startTime;';
                            $endCode[] = tab(2) . '$return[\'endTime\'] = $endTime;';
                            break;
                        default:
                            $startCode[] = tab(2) . '$' . $form['name'] . ' = I(\'' . $form['name'] . '\');';
                            $startCode[] = tab(2) . 'if (!empty($' . $form['name'] . ')) {';
                            $startCode[] = tab(3) . '$where[\'' . $form['name'] . '\'] = array(\'LIKE\', "%{$' . $form['name'] . '}%");';
                            $startCode[] = tab(3) . '$link_parameter .= \'/' . $form['name'] . '/\' .$' . $form['name'] . ';';
                            $startCode[] = tab(2) . '}';
                            $endCode[] = tab(2) . '$return[\'' . $form['name'] . '\'] = $' . $form['name'] . ';';
                            break;
                    }
                }
            }
        }
        $return = array(
            'startCode' => $startCode,
            'endCode'   => $endCode
        );
        return $return;
    }

    public function parseJoinModelCode($joinForm) {
        $table2Fields = '';
        if (isset($joinForm) && $joinForm) {
            foreach ($joinForm as $form) {
                if (isset($form['index_view']) && $form['index_view']) {
                    $table2Fields .= "'".$form['name']."'";
                    $table2Fields .= (!empty($form['asname'])) ? '=>'."'".$form['asname']."'" : '';
                    $table2Fields .= ",";
                }
            }
        }
        $return = array(
            'table2Fields'  => trim($table2Fields, ',')
        );
        return $return;
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
                $ret[] = tab($tab) . '<volist name="$' . $options[1] . '" item=\'v\'}';
                $ret[] = tab($tab + 1) . '<option value="{$v.id}">{$v.name}</option>';
                $ret[] = tab($tab) . '</volist>';

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
     * 生成复选框、单选框
     */
    private function getCheckbox($form, $name, $title, $value = '', $key = 0, $tab = 4)
    {
        return tab($tab) . '<div class="radio-box">' . "\n"
            . tab($tab + 1) . $title . '<input type="' . $form['type'] . '" name="' . $name . '" '
            . 'value="' . $value . '">' . "\n"
            . tab($tab + 1) . "\n"
            . tab($tab) . '</div>' . "\n";
    }

    //视图数据表列信息，返回checkbox形式
    public function getViewTableInfo(){ 
        C('LAYOUT_ON', false);
        $this->assign('label', I('label'));
        $selectTableName = I('selectTableName');
        $columnNameKey = getColumnNameKey();
        $this->assign('columnNameKey', $columnNameKey);
        $tableInfoList = getTableInfoArray($selectTableName);
        $this->assign('tableInfoList', $tableInfoList);
        echo $this->fetch('getViewTableInfo');
    }

    public function readOnColum(){
        C('LAYOUT_ON', false);
        $selectTableName = I('tableName');
        $columnNameKey = getColumnNameKey();
        $this->assign('columnNameKey', $columnNameKey);
        $tableInfoList = getTableInfoArray($selectTableName);
        $this->assign('tableInfoList', $tableInfoList);
        echo $this->fetch('viewModuleOn');
    }
    
    //关联视图数据表列信息，返回checkbox形式
    public function getViewTableInfo2(){    
        C('LAYOUT_ON', false);
        $this->assign('label', I('label'));
        $selectTableName = I('selectTableName');
        $columnNameKey = getColumnNameKey();
        $this->assign('columnNameKey', $columnNameKey);
        $tableInfoList = getTableInfoArray($selectTableName);
        $this->assign('tableInfoList', $tableInfoList);
        echo $this->fetch('getViewTableInfo2');
    }
}
