<?php

/**
 * @name   Error
 * @desc   Error
 * @author robertzhai
 */
class Error
{

    const API_SUCC            = 0;
    const PARAM_ERROR         = 1001;
    const GET_TOKEN_ERROR     = 1002;


    const GET_USER_INFO_ERROR   = 2001;
    const USER_NOT_REG_ERROR    = 2002;
    const GET_WECHAT_INFO_ERROR = 2003;


    public static $msg = array(
        self::PARAM_ERROR           => '参数错误',
        self::GET_TOKEN_ERROR       => '获取token失败,请稍后再试',
        self::GET_USER_INFO_ERROR   => '获取用户信息失败,请稍后再试',
        self::USER_NOT_REG_ERROR    => '未注册请注册',
        self::GET_WECHAT_INFO_ERROR => '获取微信信息失败,请稍后再试'
    );

    public static function getMsg($errCode)
    {
        return isset(self::$msg[$errCode]) ? self::$msg[$errCode] : '';
    }

    public static function getError($errCode)
    {
        $errmsg = isset(self::$msg[$errCode]) ? self::$msg[$errCode] : '';
        return array('errno' => $errCode, 'errmsg' => $errmsg);
    }

}

class StrClean
{

    public static function clean($str)
    {
        $str = trim(strip_tags($str));
        return htmlspecialchars($str, ENT_NOQUOTES);
    }

}