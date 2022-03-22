<?php
require_once __DIR__ . '/src/Config.php';
require_once __DIR__ . '/src/WxMsg.php';
require_once __DIR__ . '/src/WxRobot.php';

use  \dragonYing\lovecatwx\WxRobot;

/**我的机器人
 * Class MyRobot
 * @property $event 事件类型
 * @property $robot_wxid 机器人id
 * @property $robot_name 机器人名称
 * @property $type 消息类型
 * @property $from_wxid 消息来源
 * @property $from_name 消息来源名称
 * @property $final_from_wxid 消息最终来源
 * @property $final_from_name 消息最终来源名称
 * @property $to_wxid 接收方
 * @property $money 金额
 */
class  MyRobot extends WxRobot
{


    protected $event;
    protected $robot_wxid;
    protected $robot_name;
    protected $type;
    protected $from_wxid;
    protected $from_name;
    protected $final_from_wxid;
    protected $final_from_name;
    protected $to_wxid;
    protected $money;
    protected $msg;

    protected function getReqOption($key)
    {
        return $this->robotMsg[$key] ?? null;
    }

    /**处理消息
     * @param array $request
     */
    protected function dealRequest(array $request = [])
    {
        foreach ($request as $key => $val) {
            property_exists($this, $key) && $this->$key = $this->getReqOption($key);
        }

        if (!is_null($this->event) && !is_null($this->type)) {
            $methodName = lcfirst($this->event);
            method_exists($this, $methodName) && call_user_func([$this, $methodName]);
        }

    }

    protected function eventLogin()
    {

    }

    protected function eventGroupMsg()
    {
    }

    protected function eventFriendMsg()
    {
    }

    /**
     * 红包收款或转账
     */
    protected function eventReceivedTransfer()
    {
        $orderInfo = $this->msg;
        $money = $orderInfo['money'] ?? null;
        $paysubtype = $orderInfo['paysubtype'] ?? null;
        $receiver_pay_id = $orderInfo['receiver_pay_id'] ?? null;
        $payer_pay_id = $orderInfo['payer_pay_id'] ?? null;
        $is_arrived = $orderInfo['is_arrived'] ?? null;
        $remark = $orderInfo['remark'] ?? null;
        switch ($paysubtype) {
            case self::RESPONSE_EVENT_RECEIVED_TRANSFER_PAY_TYPE_MY_SEND://robot 发出转账
                break;
            case self::RESPONSE_EVENT_RECEIVED_TRANSFER_PAY_TYPE_BE_RECEIVED://robot 发出的转账被接收
                break;
            case self::RESPONSE_EVENT_RECEIVED_TRANSFER_PAY_TYPE_BE_SEND://robot 收到转账 但还没接收
                break;
            case self::RESPONSE_EVENT_RECEIVED_TRANSFER_PAY_TYPE_MY_RECEIVED://robot 收到转账 且已收款
                break;
        }

    }

    /**
     * 扫码收款
     */
    protected function eventScanCashMoney()
    {
        $orderInfo = $this->msg;
        $msgid = $orderInfo['msgid'] ?? null;
        $payer_wxid = $orderInfo['payer_wxid'] ?? null;
        $payer_nickname = $orderInfo['payer_nickname'] ?? null;
        $scene_desc = $orderInfo['scene_desc'] ?? null;
        $scene = $orderInfo['scene'] ?? null;
        $timestamp = $orderInfo['timestamp'] ?? time();
        switch ($scene) {
            case self::EVENT_SCAN_CASH_MONEY_SCENE_IN_PAYING://别人扫我的收款码 对方进入二维码支付
                break;
            case self::EVENT_SCAN_CASH_MONEY_SCENE_UN_PAY://别人扫我的收款码 对方未支付，已退出二维码支付
                break;
            case self::EVENT_SCAN_CASH_MONEY_SCENE_PAY_SUCCESS://别人扫我的收款码 对方扫描二维码支付成功
                break;
            case self::EVENT_SCAN_CASH_MONEY_SCENE_GET_MONEY://别人扫我的收款码 我自己个人收款完成
                break;
        }
    }

    protected function eventFriendVerify()
    {
    }

    protected function eventContactsChange()
    {
    }

    protected function eventGroupMemberAdd()
    {
    }

    protected function eventGroupMemberDecrease()
    {
    }

    protected function eventSysMsg()
    {
    }


}


//来自可爱猫的请求
$action = trim($_REQUEST['do'] ?? '');
/** @var  $robot MyRobot */
$robot = MyRobot::getInstance('127.0.0.1', 8090);//如果在机器人本机运行，修改为127.0.0.1或者localhost，若外网访问改为运行机器人的服务器外网ip

if (in_array($action, ['index', 'remote', 'down'])) {
    $robot->$action();
    exit();
}
//主动请求可爱猫
$res = $robot->getLoggedAccountList()->request();
print_r($res);
