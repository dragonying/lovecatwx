<?php


class Base implements Config
{
    private $host;
    private $port;
    private $authorization_file; //通信鉴权密钥存储路径
    private $authorization;
    private $log_file;//日志文件
    public $open_log = true;

    /**
     * @param string $host
     * @param int $port
     * @return object
     */
    public static function init($host = '127.0.0.1', $port = 8090, $authorFile = './authorization.txt',$logFile='./wxmsg.log')
    {
        return new static($host, $port);
    }

    /**
     * @param string $host
     * @param int $port
     */
    public function __construct($host = '127.0.0.1', $port = 8090)
    {
        $this->host = $host;
        $this->port = $port;
        if (!is_file($this->authorization_file)){
            $this->setAuthorization();
        }
        $this->authorization = $this->getAuthorization();
    }

    /**
     * 格式化带emoji的消息，格式化为可爱猫可展示的表情
     * @param string $str 包含emoji表情的文本
     * @return string 拼接完成以后的字符串
     */
    public function formatEmoji($str)
    {
        $strEncode = '';
        $length = mb_strlen($str, 'utf-8');
        for ($i = 0; $i < $length; $i++) {
            $_tmpStr = mb_substr($str, $i, 1, 'utf-8');
            if (strlen($_tmpStr) >= 4) {
                $strEncode .= '[@emoji=' . trim(json_encode($_tmpStr), '"') . ']';
            } else {
                $strEncode .= $_tmpStr;
            }
        }
        return $strEncode;
    }


    /**json 输出
     * @param array $data
     */
    public function  jsonOut($data=[]){
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

    public function request($param)
    {
        if (is_string($param['msg']))
            $param['msg'] = $this->formatEmoji($param['msg']); //处理emoji
        //处理完事件返回要怎么做
        $headers = [
            'Content-Type:application/json;charset=utf-8',
        ];
        if ($this->authorization)
            $headers[] = "Authorization:{$this->authorization}";
        $json = json_encode($param);
        echo $json;
        return json_decode($this->sendHttp($json, null, $headers), true);
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
        if($this->open_log && $this->log_file){
            file_put_contents($this->log_file, $data . PHP_EOL, FILE_APPEND); //记录接收消息log
        }
        $rec_arr = json_decode($data, true); //把接收的json转为数组
        $this->checkAuthorization(); //检测通信鉴权，并维护其值
        $this->jsonOut($this->response($rec_arr));
    }

    public function response($request)
    {
        $response = ["event" => ""]; //event空时，机器人不处理消息
        $functions = $this->events[$request['event']];
        if (empty($functions)) //若没处理方法，直接返回空数据告知机器人不处理即可！
            return $response;

        foreach ($functions as $func => $is_on) {
            if ($is_on) {
                $response = call_user_func([$this, $func], $request);
                if ($response !== false)
                    break; //只要一个成功就跳出循环
            }
        }
        //处理完事件返回要怎么做
        return $response;
    }

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
        $param = [ //若想使用同步处理，也就是你接收完事件要如何处理，那么你就要完善下面这个数组
            "event" => isset($_REQUEST['event']) ? trim($_REQUEST['event']) : "SendTextMsg",
            "robot_wxid" => isset($_REQUEST['robot_wxid']) ? trim($_REQUEST['robot_wxid']) : 'wxid_6mkmsto8tyvf52', //wxid_6mkmsto8tyvf52 wxid_5hxa04j4z6pg22
            "group_wxid" => isset($_REQUEST['group_wxid']) ? trim($_REQUEST['group_wxid']) : '18221469840@chatroom',
            "member_wxid" => isset($_REQUEST['member_wxid']) ? trim($_REQUEST['member_wxid']) : '',
            "member_name" => isset($_REQUEST['member_name']) ? trim($_REQUEST['member_name']) : '',
            "to_wxid" => isset($_REQUEST['to_wxid']) ? trim($_REQUEST['to_wxid']) : '18221469840@chatroom',
            "msg" => isset($_REQUEST['msg']) ? trim($_REQUEST['msg']) : "你好啊！"
        ];
        echo json_encode($this->request($param));
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
        $url = $url ? $url : $this->host . ':' . $this->port;

        $curl = curl_init();
        if ('get' == strtolower($method)) { //以GET方式发送请求
            curl_setopt($curl, CURLOPT_URL, "{$url}?{$params}");
        } else { //以POST方式发送请求
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1); //post提交方式
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params); //设置传送的参数
        }
        /* $headers 格式
        $headers = [
            'Content-Type:application/json;charset=utf-8',
            'Authorization:Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5MjU3NTczMS0zMWVlLTQxM2UtYTcwZS1mMmMyNDk3Y2M4ODAiLCJqdGkiOiI0MTA4MGQ2NjZhMDY5ZjRkNjQzOTg0M2NiMDhiOWE5ZTE1YzRiNzA3ZTE0MzA1NGEyZmI4MTgxOGQ1NjYxOTc2NDczY2I5MTk1MzI5ODU1YyIsImlhdCI6MTYwOTE1MTYyNiwibmJmIjoxNjA5MTUxNjI2LCJleHAiOjE2MTE4MzAwMjMsInN1YiI6IjEiLCJzY29wZXMiOltdfQ.i0R_gQuJ6iNK8g4RaF4paBQ4GUxnoQ-0uOjEy4cc3o1_sN4imj-k5ocnHsPdV2e467XJXBmoIKGAlh1RDuKnA6ksa1arhM78YjqRRwjw5jICnQ1O8PM-hYiAOF33X32UeHujVskGgYobFmtgUERZP--69qkdlxxpgmfQBkGwE1-XJH4VjcX82xHvxtiC0O56krpmYP7N9EimVcIc6ciKV_inlM8epI8Io5JKddRppIga3e04nV5hujb0m8bN5rD32l7mOeYRyTNhZAaovbjAvjWSFrPCz4LoXXDyxUDEmfBKxUd1JFNHfdWBo3dFMCh9-MSuKdSVY0LqeKKf9FKoiYNBIETYgsdOIq_QKhoJsrumC2y_IZ6iwQEpaRrH2Y6dzUKzfisBc2dBBeFEmOIo4ZB-HajBcRNfnnue60RMCXs_GrczQ5np8P5CzhqdHomHA9VxbhyvzjO-qAB76lgaxaOVC4w7p_h74nXOY5HMMzK7_DTbwiMMGtpX2S_aN4Z2yuVEK9h0c8JBqGN-Theb7ZHznP-NTgCyBkmzx-FtF6Pmahgp7kYv6trrSNd0WdKpQn4XBaXbVKINaobtCd0QONnFcGf3svUg8Lfoyy-r3B8y7nh94-2iNBfvPlgqzwrBdhmEEMnz6oJXCscu-d9z6a8L8cQty3YgFSzNEbh1YoI'
        ];
        */
        if (!empty($headers))
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HEADER, false); //是否打印服务器返回的header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout); //设置等待时间
        $res = curl_exec($curl); //运行curl
        $err = curl_error($curl);

        if (false === $res || !empty($err)) {
            $Errno = curl_errno($curl);
            $Info = curl_getinfo($curl);
            curl_close($curl);
            print_r($Info);
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