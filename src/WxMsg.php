<?php


namespace dragonYing\lovecatwx;

use dragonYing\lovecatwx\Config;

class WxMsg implements Config
{
    private $event = null;
    private $robot_wxid = null;
    private $to_wxid = null;
    private $member_wxid = null;
    private $member_name = null;
    private $group_wxid = null;
    private $msg = null;

    public function __get($name)
    {
        return property_exists($this, $name) ? $this->$name : null;
    }


    /**
     * 重置参数
     */
    protected function resetParam()
    {
        foreach (array_keys($this->getParam()) as $k) {
            property_exists($this, $k) && $this->$k = null;
        }
    }

    /**获取参数
     * @return array
     */
    protected function getParam()
    {
        return [
            "event" => $this->event,
            "robot_wxid" => $this->robot_wxid,
            "to_wxid" => $this->to_wxid,
            "member_wxid" => $this->member_wxid,
            "member_name" => $this->member_name,
            "group_wxid" => $this->group_wxid,
            "msg" => is_string($this->msg) ? $this->formatEmoji($this->msg) : $this->msg
        ];
    }

    /**获取app信息
     */
    public function getAppInfo()
    {
        $this->event = self::REQUEST_EVENT_GET_APP_INFO;
        return $this;
    }


    /**获取账户昵称
     * @param $robotId
     */
    public function getRobotName($robotId)
    {
        $this->event = self::REQUEST_EVENT_GET_ROBOT_NAME;
        $this->robot_wxid = $robotId;
        return $this;
    }

    /**获取账户头像
     * @param $robotId
     */
    public function getRobotHead($robotId)
    {
        $this->event = self::REQUEST_EVENT_GET_ROBOT_HEAD_IMG_URL;
        $this->robot_wxid = $robotId;
        return $this;
    }


    /**获取群列表
     * @param $robotId
     * @param bool $fresh
     */
    public function getGroupList($robotId, $fresh = true)
    {
        $this->event = self::REQUEST_EVENT_GET_GROUP_LIST;
        $this->robot_wxid = $robotId;
        $this->msg = ['is_refresh' => $fresh];
        return $this;
    }

    /**发送文本消息
     * @param $robotId
     * @param $msg
     */
    public function sendTextMsg($robotId, $msg)
    {
        $this->robot_wxid = $robotId;
        $this->event = self::REQUEST_EVENT_SEND_TEXT_MSG;
        $this->msg = $msg;
        return $this;
    }

    /**发送图片消息
     * @param $robotId
     * @param null $url
     * @param null $patch
     */
    public function sendImgMsg($robotId, $toId, $url = null, $patch = null)
    {
        $this->robot_wxid = $robotId;
        $this->to_wxid = $toId;
        $this->event = self::REQUEST_EVENT_SEND_IMAGE_MSG;
        $this->msg = [
            'name' => md5(uniqid(mt_rand(1111, 99999999))),
            'url' => $url,
            'patch' => $patch
        ];
        return $this;
    }

    /**发送动态表情
     * @param $robotId
     * @param $toId
     * @param null $url
     * @param null $patch
     */
    public function sendEmojiMsg($robotId, $toId, $url = null, $patch = null)
    {
        $this->robot_wxid = $robotId;
        $this->to_wxid = $toId;
        $this->event = self::REQUEST_EVENT_SEND_EMOJI_MSG;
        $this->msg = [
            'name' => md5(uniqid(mt_rand(1111, 99999999))),
            'url' => $url,
            'patch' => $patch
        ];
        return $this;
    }

    /**发送群消息并艾特
     * @param $robotId
     * @param $groupId
     * @param $memberId
     * @param $msg
     * @param null $memberName
     */
    public function sendGroupMsgAndAt($robotId, $groupId, $memberId, $msg, $memberName = null)
    {
        $this->robot_wxid = $robotId;
        $this->group_wxid = $groupId;
        $this->member_wxid = $memberId;
        $this->msg = $msg;
        $this->event = self::REQUEST_EVENT_SEND_GROUP_MSG_AND_AT;
        return $this;
    }

    /**发送链接消息
     * @param $robotId
     * @param $toId
     * @param $title
     * @param $text
     * @param $targetUrl
     * @param $picUrl
     * @param $iconUrl
     */
    public function sendLinkMsg($robotId, $toId, $title, $text, $targetUrl, $picUrl, $iconUrl = null)
    {
        $this->robot_wxid = $robotId;
        $this->to_wxid = $toId;
        $this->event = self::REQUEST_EVENT_SEND_LINK_MSG;
        $this->msg = [
            "title" => $title,
            "text" => $text,
            "target_url" => $targetUrl,
            "icon_url" => $iconUrl,
            "pic_url" => $picUrl
        ];
        return $this;
    }

    /**发送音乐
     * @param $robotId
     * @param $toId
     * @param $music
     * @param int $type 0
     */
    public function sendMusicMsg($robotId, $toId, $music, $type = 0)
    {
        $this->robot_wxid = $robotId;
        $this->to_wxid = $toId;
        $this->event = self::REQUEST_EVENT_SEND_MUSIC_MSG;
        $this->msg = [
            'name' => $music,
            'type' => $type
        ];
        return $this;
    }


    /***发送小程序
     * @param $robotId
     * @param $toId
     * @param $msg
     */
    public function sendMiniApp($robotId, $toId, $msg)
    {
        $this->robot_wxid = $robotId;
        $this->to_wxid = $toId;
        $this->event = self::REQUEST_EVENT_SEND_MINI_APP;
        $this->msg = $msg;
        return $this;
    }

    /**
     * @param $robotId
     * @param bool $fresh 是否更新缓存
     * @param bool $rawData 是否原始数据
     */
    public function getFriendList($robotId, $fresh = true, $rawData = true)
    {
        $this->robot_wxid = $robotId;
        $this->event = self::REQUEST_EVENT_GET_FRIEND_LIST;
        $this->msg = [
            "is_refresh" => $fresh,
            "out_rawdata" => $rawData
        ];
        return $this;
    }

    /**获取群成员列表
     * @param $robotId
     * @param $groupId
     * @param bool $fresh
     */
    public function getGroupMemberList($robotId, $groupId, $fresh = true)
    {
        $this->robot_wxid = $robotId;
        $this->group_wxid = $groupId;
        $this->event = self::REQUEST_EVENT_GET_GROUP_MEMBER_LIST;
        $this->msg = [
            "is_refresh" => $fresh
        ];
        return $this;
    }


    /**获取群成员信息
     * @param $robotId
     * @param $groupId
     * @param $memberId
     * @param bool $fresh
     */
    public function getGroupMemberInfo($robotId, $groupId, $memberId, $fresh = true)
    {
        $this->robot_wxid = $robotId;
        $this->group_wxid = $groupId;
        $this->member_wxid = $memberId;
        $this->event = self::REQUEST_EVENT_GET_GROUP_MEMBER_INFO;
        $this->msg = [
            "is_refresh" => $fresh
        ];
        return $this;
    }


    /**接收好友转账
     * @param $robotId
     * @param $toId
     * @param $msg (接收到事件里的msg原样传回)
     */
    public function acceptTransfer($robotId, $toId, $msg)
    {
        $this->robot_wxid = $robotId;
        $this->to_wxid = $toId;
        $this->event = self::REQUEST_EVENT_ACCEPT_TRANSFER;
        $this->msg = $msg;
        return $this;
    }

    /**修改备注
     * @param $robotId
     * @param $toId
     * @param $msg
     */
    public function editFriendNote($robotId, $toId, $msg)
    {
        $this->robot_wxid = $robotId;
        $this->to_wxid = $toId;
        $this->msg = $msg;
        $this->event = self::REQUEST_EVENT_EDIT_FRIEND_NOTE;
        return $this;
    }


    /**删除好友
     * @param $robotId
     * @param $toId
     */
    public function deleteFriend($robotId, $toId)
    {
        $this->robot_wxid = $robotId;
        $this->to_wxid = $toId;
        $this->event = self::REQUEST_EVENT_DELETE_FRIEND;
        return $this;
    }

    /**邀请群成员
     * @param $robotId
     * @param $toId
     * @param $groupId
     */
    public function inviteInGroup($robotId, $toId, $groupId)
    {
        $this->robot_wxid = $robotId;
        $this->to_wxid = $toId;
        $this->group_wxid = $groupId;
        $this->event = self::REQUEST_EVENT_INVITE_IN_GROUP;
        return $this;
    }


    /**删除群成员
     * @param $robotId
     * @param $toId
     * @param $groupId
     */
    public function removeGroupMember($robotId, $toId, $groupId)
    {
        $this->robot_wxid = $robotId;
        $this->to_wxid = $toId;
        $this->group_wxid = $groupId;
        $this->event = self::REQUEST_EVENT_REMOVE_GROUP_MEMBER;
        return $this;
    }

    /**自定义消息
     * @param $event
     * @param null $robotId
     * @param null $toId
     * @param null $memberId
     * @param null $memberName
     * @param null $groupId
     * @param null $msg
     */
    public function sendCustomMsg($event, $robotId = null, $toId = null, $memberId = null, $memberName = null, $groupId = null, $msg = null)
    {
        $this->event = $event;
        $this->robot_wxid = $robotId;
        $this->to_wxid = $toId;
        $this->member_wxid = $memberId;
        $this->member_name = $memberName;
        $this->group_wxid = $groupId;
        $this->msg = $msg;
        return $this;
    }


    /**格式化带emoji的消息，格式化为可爱猫可展示的表情
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

}