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
            case '7'://robot 发出转账
                break;
            case '5'://robot 发出的转账被接收
                break;
            case '1'://robot 收到转账 但还没接收
                break;
            case '3'://robot 收到转账 且已收款
                break;
        }

    }

    protected function eventScanCashMoney()
    {
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


$example = '{
    "event": "EventReceivedTransfer",
    "robot_wxid": "wxid_9qh4fyg2qug922",
    "robot_name": "",
    "type": 2000,
    "from_wxid": "",
    "from_name": "",
    "final_from_wxid": "wxid_9qh4fyg2qug922",
    "final_from_name": "夜雨   ??",
    "to_wxid": "wxid_6re9vwcvwcyu22",
    "money": "0.01",
    "msg": {//发出转账
        "paysubtype": "7",
        "is_arrived": 0,
        "is_received": 0,
        "receiver_pay_id": "1000050001202203220018146117528",
        "payer_pay_id": "100005000122032200070132722794459854",
        "money": "0.01",
        "remark": "",
        "robot_pay_id": "1000050001202203220018146117528",
        "pay_id": "100005000122032200070132722794459854",
        "update_msg": "receiver_pay_id、payer_pay_id属性为robot_pay_id、pay_id的新名字，内容是一样的，建议更换"
    }
    {//转账被接收
    "paysubtype": "5",
    "is_arrived": 0,
    "is_received": 0,
    "receiver_pay_id": "1000050001202203220018146117528",
    "payer_pay_id": "100005000122032200070132722794459854",
    "money": "0.01",
    "remark": "",
    "robot_pay_id": "1000050001202203220018146117528",
    "pay_id": "100005000122032200070132722794459854",
    "update_msg": "receiver_pay_id、payer_pay_id属性为robot_pay_id、pay_id的新名字，内容是一样的，建议更换"
}，
{ //接收到转账 但未 收款
    "paysubtype": "1",
    "is_arrived": 1,
    "is_received": 0,
    "receiver_pay_id": "1000050001202203221312917739869",
    "payer_pay_id": "100005000122032200064222346882456934",
    "money": "0.01",
    "remark": "",
    "robot_pay_id": "1000050001202203221312917739869",
    "pay_id": "100005000122032200064222346882456934",
    "update_msg": "receiver_pay_id、payer_pay_id属性为robot_pay_id、pay_id的新名字，内容是一样的，建议更换"
}
{//接收到转账 已收款
    "paysubtype": "3",
    "is_arrived": 1,
    "is_received": 1,
    "receiver_pay_id": "1000050001202203221312917739869",
    "payer_pay_id": "100005000122032200064222346882456934",
    "money": "0.01",
    "remark": "",
    "robot_pay_id": "1000050001202203221312917739869",
    "pay_id": "100005000122032200064222346882456934",
    "update_msg": "receiver_pay_id、payer_pay_id属性为robot_pay_id、pay_id的新名字，内容是一样的，建议更换"
}
}';

//别人扫我的收款码
//{"event":"EventScanCashMoney","robot_wxid":"wxid_9qh4fyg2qug922","robot_name":"","type":0,"from_wxid":"","from_name":"","final_from_wxid":"wxid_6re9vwcvwcyu22","final_from_name":"???龙英??","to_wxid":"wxid_9qh4fyg2qug922","money":"","msg":{"to_wxid":"wxid_9qh4fyg2qug922","msgid":1769241904,"payer_wxid":"wxid_6re9vwcvwcyu22","payer_nickname":"???龙英??","scene_desc":"进入二维码支付","scene":1,"timestamp":1647941842,"pay_wxid":"wxid_6re9vwcvwcyu22","pay_name":"???龙英??","update_msg":"payer_wxid、payer_nickname属性为pay_wxid、pay_name新属性名字，内容是一样的，建议更换"}}
//{"event":"EventScanCashMoney","robot_wxid":"wxid_9qh4fyg2qug922","robot_name":"","type":0,"from_wxid":"","from_name":"","final_from_wxid":"wxid_6re9vwcvwcyu22","final_from_name":"???龙英??","to_wxid":"wxid_9qh4fyg2qug922","money":"","msg":{"to_wxid":"wxid_9qh4fyg2qug922","msgid":1769241906,"payer_wxid":"wxid_6re9vwcvwcyu22","payer_nickname":"???龙英??","scene_desc":"未支付，已退出二维码支付","scene":-1,"timestamp":1647941845,"pay_wxid":"wxid_6re9vwcvwcyu22","pay_name":"???龙英??","update_msg":"payer_wxid、payer_nickname属性为pay_wxid、pay_name新属性名字，内容是一样的，建议更换"}}
//{"event":"EventScanCashMoney","robot_wxid":"wxid_9qh4fyg2qug922","robot_name":"","type":0,"from_wxid":"","from_name":"","final_from_wxid":"wxid_6re9vwcvwcyu22","final_from_name":"???龙英??","to_wxid":"wxid_9qh4fyg2qug922","money":"0.01","msg":{"to_wxid":"wxid_9qh4fyg2qug922","msgid":1769241920,"payer_wxid":"wxid_6re9vwcvwcyu22","payer_nickname":"???龙英??","payer_pay_id":"10001071012022032201894229164586","money":"0.01","scene_desc":"扫描二维码支付成功","scene":2,"timestamp":1647942073,"pay_wxid":"wxid_6re9vwcvwcyu22","pay_name":"???龙英??","update_msg":"payer_wxid、payer_nickname属性为pay_wxid、pay_name新属性名字，内容是一样的，建议更换"}}
//{"event":"EventScanCashMoney","robot_wxid":"wxid_9qh4fyg2qug922","robot_name":"","type":0,"from_wxid":"","from_name":"","final_from_wxid":"","final_from_name":"","to_wxid":"wxid_9qh4fyg2qug922","money":"0.01","msg":{"to_wxid":"wxid_9qh4fyg2qug922","msgid":1769241921,"received_money_index":"1","money":"0.01","total_money":"0.01","remark":"","scene_desc":"个人收款完成","scene":3,"timestamp":1647942074}}
//
////我扫别人的收款码
//{"event":"EventFriendMsg","robot_wxid":"wxid_9qh4fyg2qug922","robot_name":"","type":49,"from_wxid":"","from_name":"","final_from_wxid":"gh_3dfda90e39d6","final_from_name":"微信支付 ","to_wxid":"wxid_9qh4fyg2qug922","msg":"<msg> <appmsg appid=\"\" sdkver=\"0\"> \t<title><![CDATA[微信支付凭证]]></title> \t<des><![CDATA[收款方龙英\r\n付款金额￥0.01\r\n支付方式零钱\r\n交易状态支付成功，对方已收款]]></des> \t<action></action> \t<type>5</type> \t<showtype>1</showtype>     <soundtype>0</soundtype> \t<content><![CDATA[]]></content> \t<contentattr>0</contentattr> \t<url><![CDATA[https://wx.tenpay.com/cgi-bin/mmpayweb-bin/jumpuserroll?trans_id=100010710122032200072133396439576854&accid=085e9858ed437400d9af77348]]></url> \t<lowurl><![CDATA[]]></lowurl> \t<appattach> \t\t<totallen>0</totallen> \t\t<attachid></attachid> \t\t<fileext></fileext> \t\t<cdnthumburl><![CDATA[]]></cdnthumburl> \t\t<cdnthumbaeskey><![CDATA[]]></cdnthumbaeskey> \t\t<aeskey><![CDATA[]]></aeskey> \t</appattach> \t<extinfo></extinfo> \t<sourceusername><![CDATA[]]></sourceusername> \t<sourcedisplayname><![CDATA[]]></sourcedisplayname> \t<mmreader> \t\t<category type=\"0\" count=\"1\"> \t\t\t<name><![CDATA[微信支付]]></name> \t\t\t<topnew> \t\t\t\t<cover><![CDATA[]]></cover> \t\t\t\t<width>0</width> \t\t\t\t<height>0</height> \t\t\t\t<digest><![CDATA[收款方龙英\r\n付款金额￥0.01\r\n支付方式零钱\r\n交易状态支付成功，对方已收款]]></digest> \t\t\t</topnew> \t\t\t\t<item> \t<itemshowtype>4</itemshowtype> \t<title><![CDATA[微信支付凭证]]></title> \t<url><![CDATA[https://wx.tenpay.com/cgi-bin/mmpayweb-bin/jumpuserroll?trans_id=100010710122032200072133396439576854&accid=085e9858ed437400d9af77348]]></url> \t<shorturl><![CDATA[]]></shorturl> \t<longurl><![CDATA[]]></longurl> \t<pub_time>1647941978</pub_time> \t<cover><![CDATA[]]></cover> \t<tweetid></tweetid> \t<digest><![CDATA[收款方龙英\r\n付款金额￥0.01\r\n支付方式零钱\r\n交易状态支付成功，对方已收款]]></digest> \t<fileid>0</fileid> \t<sources> \t<source> \t<name><![CDATA[微信支付]]></name> \t</source> \t</sources> \t<styles><topColor><![CDATA[]]></topColor>\r\n<style>\r\n<range><![CDATA[{3,2}]]></range>\r\n<font><![CDATA[s]]></font>\r\n<color><![CDATA[#000000]]></color>\r\n</style>\r\n<style>\r\n<range><![CDATA[{10,5}]]></range>\r\n<font><![CDATA[s]]></font>\r\n<color><![CDATA[#000000]]></color>\r\n</style>\r\n<style>\r\n<range><![CDATA[{20,2}]]></range>\r\n<font><![CDATA[s]]></font>\r\n<color><![CDATA[#000000]]></color>\r\n</style>\r\n<style>\r\n<range><![CDATA[{27,10}]]></range>\r\n<font><![CDATA[s]]></font>\r\n<color><![CDATA[#000000]]></color>\r\n</style>\r\n</styles>\t<native_url></native_url>    <del_flag>0</del_flag>     <contentattr>0</contentattr>     <play_length>0</play_length> \t<play_url><![CDATA[]]></play_url> \t<player><![CDATA[]]></player> \t<template_op_type>0</template_op_type> \t<weapp_username><![CDATA[]]></weapp_username> \t<weapp_path><![CDATA[]]></weapp_path> \t<weapp_version>0</weapp_version> \t<weapp_state>0</weapp_state>     <music_source>0</music_source>     <pic_num>0</pic_num> \t<show_complaint_button>0</show_complaint_button> \t<vid><![CDATA[]]></vid> \t<recommendation><![CDATA[]]></recommendation> \t<pic_urls></pic_urls>\t<comment_topic_id>0</comment_topic_id>\t<cover_235_1><![CDATA[]]></cover_235_1> \t<cover_1_1><![CDATA[]]></cover_1_1>     <cover_16_9><![CDATA[]]></cover_16_9>     <appmsg_like_type>0</appmsg_like_type>     <video_width>0</video_width>     <video_height>0</video_height>     <is_pay_subscribe>0</is_pay_subscribe> \t<general_string><![CDATA[]]></general_string>     <finder_feed></finder_feed> \t</item> \t\t</category> \t\t<publisher> \t\t\t<username><![CDATA[wxzhifu]]></username> \t\t\t<nickname><![CDATA[微信支付]]></nickname> \t\t</publisher> \t\t<template_header><title><![CDATA[微信支付凭证]]></title>\r\n<title_color><![CDATA[]]></title_color>\r\n<pub_time>1647941978</pub_time>\r\n<first_data><![CDATA[]]></first_data>\r\n<first_color><![CDATA[]]></first_color>\r\n<hide_title_and_time>1</hide_title_and_time>\r\n<show_icon_and_display_name>0</show_icon_and_display_name>\r\n<display_name><![CDATA[]]></display_name>\r\n<icon_url><![CDATA[]]></icon_url>\r\n<hide_icon_and_display_name_line>1</hide_icon_and_display_name_line>\r\n<header_jump_url><![CDATA[https://wx.tenpay.com/cgi-bin/mmpayweb-bin/jumpuserroll?trans_id=100010710122032200072133396439576854&accid=085e9858ed437400d9af77348]]></header_jump_url>\r\n<shortcut_icon_url><![CDATA[]]></shortcut_icon_url>\r\n<ignore_hide_title_and_time>1</ignore_hide_title_and_time>\r\n<hide_time>1</hide_time>\r\n<pay_style>1</pay_style>\r\n<header_jump_type>2</header_jump_type>\r\n<display_name_desc><![CDATA[]]></display_name_desc>\r\n<show_right_icon_and_desc_name>0</show_right_icon_and_desc_name>\r\n<right_icon_url><![CDATA[]]></right_icon_url>\r\n<right_desc_name><![CDATA[]]></right_desc_name>\r\n<finder_user_name><![CDATA[]]></finder_user_name>\r\n<show_finder_feed_entry>0</show_finder_feed_entry>\r\n<finder_feedid><![CDATA[]]></finder_feedid>\r\n<finder_nonceid><![CDATA[]]></finder_nonceid>\r\n<finder_feed_thumnail><![CDATA[]]></finder_feed_thumnail>\r\n<transaction_id><![CDATA[10001071012022032201752267544505]]></transaction_id>\r\n</template_header> \t\t<template_detail><template_show_type>1</template_show_type>\r\n<text_content>\r\n<cover><![CDATA[]]></cover>\r\n<text><![CDATA[]]></text>\r\n<color><![CDATA[]]></color>\r\n</text_content>\r\n<line_content>\r\n<topline>\r\n<key>\r\n<word><![CDATA[付款金额]]></word>\r\n<color><![CDATA[#888888]]></color>\r\n<hide_dash_line>1</hide_dash_line>\r\n</key>\r\n<value>\r\n<word><![CDATA[￥0.01]]></word>\r\n<color><![CDATA[#000000]]></color>\r\n<small_text_count>1</small_text_count>\r\n</value>\r\n</topline>\r\n<lines>\r\n<line>\r\n<key>\r\n<word><![CDATA[收款方]]></word>\r\n<color><![CDATA[#888888]]></color>\r\n</key>\r\n<value>\r\n<word><![CDATA[龙英]]></word>\r\n<color><![CDATA[#000000]]></color>\r\n</value>\r\n</line>\r\n<line>\r\n<key>\r\n<word><![CDATA[支付方式]]></word>\r\n<color><![CDATA[#888888]]></color>\r\n</key>\r\n<value>\r\n<word><![CDATA[零钱]]></word>\r\n<color><![CDATA[#000000]]></color>\r\n</value>\r\n</line>\r\n<line>\r\n<key>\r\n<word><![CDATA[交易状态]]></word>\r\n<color><![CDATA[#888888]]></color>\r\n</key>\r\n<value>\r\n<word><![CDATA[支付成功，对方已收款]]></word>\r\n<color><![CDATA[#000000]]></color>\r\n</value>\r\n</line>\r\n</lines>\r\n</line_content>\r\n<opitems>\r\n<opitem>\r\n<word><![CDATA[查看账单详情]]></word>\r\n<url><![CDATA[https://wx.tenpay.com/cgi-bin/mmpayweb-bin/jumpuserroll?trans_id=100010710122032200072133396439576854&accid=085e9858ed437400d9af77348]]></url>\r\n<icon><![CDATA[]]></icon>\r\n<color><![CDATA[#000000]]></color>\r\n<weapp_username><![CDATA[]]></weapp_username>\r\n<weapp_path><![CDATA[]]></weapp_path>\r\n<op_type>0</op_type>\r\n<weapp_version>0</weapp_version>\r\n<weapp_state>0</weapp_state>\r\n<hint_word><![CDATA[]]></hint_word>\r\n<is_rich_text>0</is_rich_text>\r\n<display_line_number>0</display_line_number>\r\n<general_string><![CDATA[]]></general_string>\r\n<is_show_red_dot>0</is_show_red_dot>\r\n<ext_id><![CDATA[]]></ext_id>\r\n<business_id><![CDATA[]]></business_id>\r\n<thumbnail><![CDATA[]]></thumbnail>\r\n<is_show_play_btn>0</is_show_play_btn>\r\n<dmicon><![CDATA[]]></dmicon>\r\n</opitem>\r\n<show_type>1</show_type>\r\n</opitems>\r\n<new_tmpl_type>0</new_tmpl_type>\r\n</template_detail> \t    <forbid_forward>0</forbid_forward>         <notify_msg></notify_msg> \t</mmreader> \t<thumburl><![CDATA[]]></thumburl> \t     <template_id><![CDATA[ey45ZWkUmYUBk_fMgxBLvyaFqVop1rmoWLFd62OXGiU]]></template_id>                          \t </appmsg><fromusername><![CDATA[gh_3dfda90e39d6]]></fromusername><appinfo><version>0</version><appname><![CDATA[微信支付]]></appname><isforceupdate>1</isforceupdate></appinfo></msg>"}


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
