<?php


class Base implements Config
{

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
}