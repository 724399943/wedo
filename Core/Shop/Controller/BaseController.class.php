<?php
namespace Shop\Controller;
use Think\Controller;
use Plugins\Wechat\WeixinController;
use Plugins\Message\Message;
use Plugins\Huanxin\Easemob;

// 基础控制器
class BaseController extends Controller {
    private $message;
    protected $limitStart;
    protected $limitStr;
    protected $page;
    protected $wechatAgent;
    private $online = true;

    /**
     * [__construct 初始化]
     * @author kofu <[418382595@qq.com]>
     */
    public function __construct() {
        parent::__construct();
        $this->message = new Message();
        $this->wechatAgent = $this->isWechatAgent();
        if ( $this->wechatAgent !== false ) {
            C('DEFAULT_THEME', 'Wechat');
        } else {
            C('DEFAULT_THEME', 'Shop');
        }
        $this->loadWebInitialize();
    }

    /**
     * [loadWebInitialize description]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2015 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function loadWebInitialize() {
        $sessionId = I('request.session_id');
        if ( !empty($sessionId) ) {
            session_id($sessionId);
        }
        session_start();
        
        if ( !$this->online ) {
            $userInfo = M('user')->where(array('id'=> 35))->find();
            session('userInfo', $userInfo);
            session('userId', $userInfo['id']);
            session('is_temp', 0);
            session('agentId', 6);
        }

        $controllerName = CONTROLLER_NAME;
        $actionName     = ACTION_NAME;
        $c = M('config')->getField('config_sign,config_value');
        C($c);
        
        $right = M('controller_power')->where(array('controller_name'=>$controllerName, 'controller_function'=>$actionName))->find();

        if( count($right) < 1 ) {
            exit(statusCode(array(), 100001));
        } else {
            session_set_cookie_params(3600, '/', C('BASE_COOKIE_HOST'), false, true);
            define(NEED_PAGE, $right['need_page']);
            define(PAGE_LIMIT, $right['page_limit']);

            if($right['need_login'] == '1') {
                //用户尚未登录，直接跳到登录界面
                $userId = session('userId');//用户名称
                $isTemp = session('is_temp'); //是否是临时用户
                
                if( empty($userId) || $isTemp == '1') {
                    exit(statusCode(array(), 100000));
                }
            }
        }
        $this->load_limit();

        
        // $userId = session('userId');
        // if( empty($userId) ) {
        //     //生成临时用户ID
        //     $userId = time() . rand(0, 999);
        //     session('userId', $userId);     //临时用户名ID
        //     session('is_temp', '1'); //是否为临时
        // }
        
        // 更新用户的系统消息
        $this->updateUserMessage();
    }

    /**
     * [updateUserMessage description]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2016 Xcrozz (http://www.xcrozz.com)
     * @return    [type]        [description]
     */
    public function updateUserMessage() {
        $userId = session('userId');
        $is_temp = session('is_temp');
        // 更新用户的系统消息
        if ($is_temp == '0') {
            $this->message->getMessage($userId);
            // 默认系统语言
            $language = session('language');
            $language = !empty($language) ? $language : 'en-us';
            cookie('think_language', $language);
        } else {
            session('language', 'en-us');
            cookie('think_language', 'en-us');
        }
    } 

    /**
     * [isWechatAgent 检测是否微信客户端访问]
     * @author kofu <418382595@qq.com>
     * @copyright Copyright (c)           2015 Xcrozz (http://www.xcrozz.com)
     * @return    boolean       [description]
     */
    public function isWechatAgent() {
        $userAgent = addslashes($_SERVER['HTTP_USER_AGENT']);
        if (strpos($userAgent, 'Mobile') !== false) {
            return true;
        } elseif (strpos($userAgent, 'MicroMessenger') !== false && strpos($userAgent, 'Windows Phone') !== false) {
            return true;
        } else {
            return false;
        }
    }

    public function load_limit() {
        if ( NEED_PAGE ) {
            $page  = I('request.page', 1, 'int') - 1;
            $page  = $page < 0 ? 0 : $page;
            $limit = PAGE_LIMIT;
            $this->page         = $page;
            $this->limitStart   = $limit * $page;
            $this->limitStr     = "LIMIT {$this->limitStart} , {$limit}";
        } else {
            $limit = PAGE_LIMIT;
            $this->limitStr = "";
        }
    }

    public function imreg($id,$nickname){
        $options = imConf();
        $Easemob = new Easemob($options);
        $save = $Easemob->createUser($id,'pd'.$id,$nickname);
        return $save;
    }
}