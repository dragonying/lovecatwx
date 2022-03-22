<?php

namespace dragonYing\lovecatwx;


interface Config
{
    /***************** 接受事件 ************** 接受事件 ****************************/
    const RESPONSE_EVENT_LOGIN = 'EventLogin';//新的账号登录成功,下线时
    const RESPONSE_EVENT_GROUP_MSG = 'EventGroupMsg';//收到群消息时
    const RESPONSE_EVENT_FRIEND_MSG = 'EventFriendMsg';//收到私聊消息
    const RESPONSE_EVENT_RECEIVED_TRANSFER = 'EventReceivedTransfer';//收到转账,好友转账
    const RESPONSE_EVENT_SCAN_CASH_MONEY = 'EventScanCashMoney';//面对面收款,二维码收款
    const RESPONSE_EVENT_FRIEND_VERIFY = 'EventFriendVerify';//好友请求事件
    const RESPONSE_EVENT_CONTACTS_CHANGE = 'EventContactsChange';//朋友变动事件
    const RESPONSE_EVENT_GROUP_MEMBER_ADD = 'EventGroupMemberAdd';//群成员增加事件(新人进群)
    const RESPONSE_EVENT_GROUP_MEMBER_DECREASE = 'EventGroupMemberDecrease';//群成员减少事件（群成员退出）
    const RESPONSE_EVENT_SYS_MSG = 'EventSysMsg';//系统消息事件


    /***************** 消息类型 **************** 消息类型 *****************************/
    const MSG_TYPE_TEXT = 1;//文本消息
    const MSG_TYPE_IMG = 3;//图片消息
    const MSG_TYPE_VOICE = 34;//语音消息
    const MSG_TYPE_CARD = 42;//名片消息
    const MSG_TYPE_VIDEO = 43;//视频
    const MSG_TYPE_EMOJI = 47;//动态表情
    const MSG_TYPE_POSITION = 48;//地理位置
    const MSG_TYPE_SHARE_LINK = 49;//分享链接
    const MSG_TYPE_TRANSFER = 2000;//转账
    const MSG_TYPE_MONEY = 2001;//红包
    const MSG_TYPE_MINI_APP = 2002;//小程序
    const MSG_TYPE_GROUP_INVITE = 2003;//群邀请


    /***************** 请求事件 **************** 请求事件 *****************************/
    const REQUEST_EVENT_GET_APP_INFO = 'GetappInfo';//取插件信息
    const REQUEST_EVENT_GET_APP_DIR = 'GetAppDir';//取应用目录
    const REQUEST_EVENT_ADD_APP_LOGS = 'AddAppLogs';//添加日志
    const REQUEST_EVENT_RELOAD_APP = 'ReloadApp';//重载插件
    const REQUEST_EVENT_GET_ROBOT_HEAD_IMG_URL = 'GetRobotHeadimgurl';//取登录账号头像
    const REQUEST_EVENT_GET_LOGGED_ACCOUNT_LIST = 'GetLoggedAccountList';//取登录账号列表
    const REQUEST_EVENT_GET_ROBOT_NAME = 'GetRobotName';//取登录账号昵称
    const REQUEST_EVENT_SEND_TEXT_MSG = 'SendTextMsg';//发送文本消息
    const REQUEST_EVENT_SEND_IMAGE_MSG = 'SendImageMsg';//发送图片消息
    const REQUEST_EVENT_SEND_VIDEO_MSG = 'SendVideoMsg';//发送视频消息
    const REQUEST_EVENT_SEND_FILE_MSG = 'SendFileMsg';//发送文件消息
    const REQUEST_EVENT_SEND_EMOJI_MSG = 'SendEmojiMsg';//发送动态表情
    const REQUEST_EVENT_SEND_GROUP_MSG_AND_AT = 'SendGroupMsgAndAt';//发送群消息并艾特
    const REQUEST_EVENT_SEND_LINK_MSG = 'SendLinkMsg';//发送分享链接
    const REQUEST_EVENT_SEND_MUSIC_MSG = 'SendMusicMsg';//发送音乐分享
    const REQUEST_EVENT_SEND_CARD_MSG = 'SendCardMsg';//发送名片消息
    const REQUEST_EVENT_SEND_MINI_APP = 'SendMiniApp';//发送小程序
    const REQUEST_EVENT_GET_FRIEND_LIST = 'GetFriendList';//取好友列表
    const REQUEST_EVENT_ACCEPT_TRANSFER = 'AcceptTransfer';//接收好友转账
    const REQUEST_EVENT_AGREE_FRIEND_VERIFY = 'AgreeFriendVerify';//同意好友请求
    const REQUEST_EVENT_EDIT_FRIEND_NOTE = 'EditFriendNote';//修改好友备注
    const REQUEST_EVENT_DELETE_FRIEND = 'DeleteFriend';//删除好友
    const REQUEST_EVENT_GET_GROUP_LIST = 'GetFriendList';//取群聊列表
    const REQUEST_EVENT_GET_GROUP_MEMBER_LIST = 'GetGroupMemberList';//取群成员列表
    const REQUEST_EVENT_GET_GROUP_MEMBER_INFO = 'GetGroupMemberInfo';//取群成员详细
    const REQUEST_EVENT_AGREE_GROUP_INVITE = 'AgreeGroupInvite';//同意群聊邀请
    const REQUEST_EVENT_INVITE_IN_GROUP = 'InviteInGroup';//邀请加入群聊
    const REQUEST_EVENT_REMOVE_GROUP_MEMBER = 'RemoveGroupMember';//踢出群成员
    const REQUEST_EVENT_EDIT_GROUP_NAME = 'EditGroupName';//修改群名称
    const REQUEST_EVENT_EDIT_GROUP_NOTICE = 'EditGroupNotice';//修改群公告
    const REQUEST_EVENT_BUILD_NEW_GROUP = 'BuildNewGroup';//建立新群
    const REQUEST_EVENT_QUIT_GROUP = 'QuitGroup';//退出群聊

}
