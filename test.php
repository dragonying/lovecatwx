<?php
require_once __DIR__.'/src/Config.php';
require_once __DIR__.'/src/WxMsg.php';
require_once __DIR__.'/src/WxRobot.php';

use  \dragonYing\lovecatwx\WxRobot;


//来自可爱猫的请求
$do = trim($_REQUEST['do']??'');
/** @var  $robot WxRobot */
$robot = WxRobot::init('127.0.0.1',8090);//如果在机器人本机运行，修改为127.0.0.1或者localhost，若外网访问改为运行机器人的服务器外网ip
if (in_array($do, ['index', 'remote', 'down'])) {
    $robot->$do();
    exit();
}
//主动请求可爱猫
$res = $robot->getAppInfo()->request();
print_r($res);