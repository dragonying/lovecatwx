<?php

namespace dragonYing\lovecatwx;

use dragonYing\lovecatwx\WxMsg;

abstract class  WxRobot extends WxMsg
{
    private $host;//可爱猫运行服务ip
    private $port;//可爱猫运行服务端口
    private $authorization_file; //通信鉴权密钥存储路径
    private $authorization;
    private $log_file;//日志文件
    public $open_log = true;

    //接收事件集
    const EVENTS = [
        self::RESPONSE_EVENT_LOGIN,
        self::RESPONSE_EVENT_GROUP_MSG,
        self::RESPONSE_EVENT_FRIEND_MSG,
        self::RESPONSE_EVENT_RECEIVED_TRANSFER,
        self::RESPONSE_EVENT_SCAN_CASH_MONEY,
        self::RESPONSE_EVENT_FRIEND_VERIFY,
        self::RESPONSE_EVENT_CONTACTS_CHANGE,
        self::RESPONSE_EVENT_GROUP_MEMBER_ADD,
        self::RESPONSE_EVENT_GROUP_MEMBER_DECREASE,
        self::RESPONSE_EVENT_SYS_MSG
    ];

    /** 消息 */
    protected $robotMsg = [];

    /*返回*/
    protected $resResult = null;

    static $instance = null;

    /**
     * @param string $host
     * @param int $port
     * @param string $authorFile
     * @param string $logFile
     * @return static
     */
    public static function getInstance($host = '127.0.0.1', $port = 8090, $authorFile = './authorization.txt', $logFile = './wxmsg.log')
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static($host, $port, $authorFile, $logFile);
        }
        return self::$instance;
    }

    /**
     * @param string $host
     * @param int $port
     */
    private function __construct($host = '127.0.0.1', $port = 8090, $authorFile = './authorization.txt', $logFile = './wxmsg.log')
    {
        $this->host = $host;
        $this->port = $port;
        $this->authorization_file = $authorFile;
        $this->log_file = $logFile;
        if (!is_file($this->authorization_file)) {
            $this->setAuthorization();
        }
        $this->authorization = $this->getAuthorization();
    }

    /**获取机器人消息
     * @return array
     */
    public function getRobotMsg()
    {
        return $this->robotMsg;
    }

    /**获取返回结果
     * @return null
     */
    public function getResult()
    {
        return $this->resResult;
    }

    /**获取url
     * @return string
     */
    public function getUrl()
    {
        return $this->host . ':' . $this->port;
    }


    /**json 输出
     * @param array $data
     */
    public function jsonOut($data = [])
    {
        echo json_encode($data);
    }

    /**
     * 聊天内容是否以关键词xx开头
     * @param string $str 聊天内容
     * @param string $pattern 关键词
     * @return boolean  true/false
     */
    public function startWith($str, $pattern)
    {
        return strpos($str, $pattern) === 0;
    }

    /**请求
     * @return mixed
     */
    public function request($params = [])
    {
        //处理完事件返回要怎么做
        $headers = [
            'Content-Type:application/json;charset=utf-8',
        ];
        if ($this->authorization) {
            $headers[] = "Authorization:{$this->authorization}";
        }
        empty($params) && $params = $this->getParam();

        $res = $this->sendHttp(json_encode($params), $this->getUrl(), $headers);
        try {
            $res = json_decode($res, true);
        } catch (\Throwable $e) {

        }
        $this->resResult = $res;
        return $res;
    }

    /**
     * 程序入口，返回空白Json或具有操作命令的数据
     * 该方法不需要动
     * @return string 符合可爱猫|http-sdk插件的操作数据结构json
     */
    public function index()
    {
        header("Content-type: text/html; charset=utf-8");
        date_default_timezone_set("PRC"); //设置下时区
        $data = file_get_contents('php://input'); //接收原始数据;
        if ($this->open_log && $this->log_file) {
            file_put_contents($this->log_file, $data . PHP_EOL, FILE_APPEND); //记录接收消息log
        }
        $rec_arr = json_decode($data, true); //把接收的json转为数组
        $this->robotMsg = $rec_arr;
        $this->checkAuthorization(); //检测通信鉴权，并维护其值
        $this->jsonOut($this->response());
    }

    protected function response()
    {
        $this->resetParam();//重置
        $request = $this->robotMsg;//机器人消息
        //事件存在
        if (in_array($request['event'], self::EVENTS)) {
            $this->dealRequest($request);
        }
        //处理完事件返回要怎么做
        return $this->getParam();
    }

    //自己实现处理消息
    abstract function dealRequest($request);


    /**
     * 将收到的图片转化为下载连接(直连文件)
     * 只有该文件和可爱猫在同一台服务器时可用
     * 并且运行该文件的用户必须拥目标文件的读取权限
     * @param string $filepath 收到的图片、视频、文件消息里的路径地址(其实就是msg的值)
     */
    public function down()
    {
        ob_clean();
        $filepath = $_REQUEST['filepath'] ?? './favicon.ico';
        if (!file_exists($filepath)) {
            exit(json_encode(['success' => false, 'message' => 'file not found!']));
        }
        // file_put_contents('./wxfile.log', $filepath.PHP_EOL, FILE_APPEND);
        $fp = fopen($filepath, "r");
        $filesize = filesize($filepath);

        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Accept-Length:" . $filesize);
        header("Content-Disposition: attachment; filename=" . basename($filepath));

        $buffer = 1024;
        $buffer_count = 0;
        while (!feof($fp) && $filesize - $buffer_count > 0) {
            $data = fread($fp, $buffer);
            $buffer_count += $buffer;
            echo $data;
        }
        fclose($fp);
    }

    /**
     * 控制机器人接口
     * 该方法不需要动
     * @return string 符合openHttpApi插件的操作数据结构json
     */
    public function remote()
    {
        header("Content-type: text/html; charset=utf-8");
        date_default_timezone_set("PRC"); //设置下时区
        $event = isset($_REQUEST['event']) ? trim($_REQUEST['event']) : self::REQUEST_EVENT_SEND_TEXT_MSG;
        $robot_wxid = isset($_REQUEST['robot_wxid']) ? trim($_REQUEST['robot_wxid']) : 'wxid_6mkmsto8tyvf52';
        $group_wxid = isset($_REQUEST['group_wxid']) ? trim($_REQUEST['group_wxid']) : '18221469840@chatroom';
        $member_wxid = isset($_REQUEST['member_wxid']) ? trim($_REQUEST['member_wxid']) : '';
        $member_name = isset($_REQUEST['member_name']) ? trim($_REQUEST['member_name']) : '';
        $to_wxid = isset($_REQUEST['to_wxid']) ? trim($_REQUEST['to_wxid']) : '18221469840@chatroom';
        $msg = isset($_REQUEST['msg']) ? trim($_REQUEST['msg']) : "你好啊！";
        $this->sendCustomMsg($event, $robot_wxid, $group_wxid, $member_wxid, $member_name, $to_wxid, $msg);
        $this->jsonOut($this->request());
    }


    /**
     * 发送 HTTP 请求
     *
     * @param string $params 请求参数,会原样发送
     * @param string $url 请求地址
     * @param array $headers 请求头
     * @param int $timeout 超时时间
     * @param string $method 请求方法 post / get
     * @return string  结果数据(Body原始数据，一般为json字符串)
     */
    public function sendHttp($params, $url = null, $headers = null, $method = 'post', $timeout = 3)
    {
        $curl = curl_init();
        if ('get' == strtolower($method)) { //以GET方式发送请求
            curl_setopt($curl, CURLOPT_URL, "{$url}?{$params}");
        } else { //以POST方式发送请求
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1); //post提交方式
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params); //设置传送的参数
        }
        if (!empty($headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($curl, CURLOPT_HEADER, false); //是否打印服务器返回的header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout); //设置等待时间
        $res = curl_exec($curl); //运行curl
        $err = curl_error($curl);

        if (false === $res || !empty($err)) {
            $Errno = curl_errno($curl);
            $Info = curl_getinfo($curl);
            curl_close($curl);
            return $err . ' result: ' . $res . 'error_msg: ' . $Errno;
        }
        curl_close($curl); //关闭curl

        return $res;
    }

    /**
     * 获取headers Nginx和Apache
     * @return array
     * @author 遗忘悠剑
     */
    private function getHeaders()
    {
        $headers = [];
        if (!function_exists('getallheaders')) {
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(
                        ' ',
                        '-',
                        ucwords(strtolower(str_replace('_', ' ', substr($name, 5))))
                    )] = $value;
                }
            }
        } else {
            $headers = getallheaders();
        }
        return $headers;
    }

    /**
     * 设置Authorization并返回
     * @param string $authorization
     * @return string
     * @author 遗忘悠剑
     */
    private function setAuthorization($authorization = '')
    {
        file_put_contents($this->authorization_file, $authorization);
        $this->authorization = $authorization;
        return $this->authorization;
    }


    /**
     * 获取Authorization
     * @return string
     * @author 遗忘悠剑
     */
    private function getAuthorization()
    {
        $this->authorization = file_get_contents($this->authorization_file) ?: '';
        return $this->authorization;
    }

    /**
     * 检测Authorization并返回
     * @return string
     * @author 遗忘悠剑
     */
    private function checkAuthorization()
    {
        $headers = $this->getHeaders();
        if (!empty($headers['Authorization']) && $headers['Authorization'] != $this->getAuthorization())
            return $this->setAuthorization($headers['Authorization'] ?: '');
    }

}