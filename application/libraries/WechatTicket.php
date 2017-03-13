<?php

/**
 * @name   WechatTicket
 * @desc   二维码ticket
 * @author robertzhai
 */
class WechatTicket
{

    /**
     * 永久二维码ticket
     * @param $token
     * @param $url
     * @param $scene_str
     * @return bool|mixed
     */
    public static function get_ticket($token, $url, $scene_str)
    {

        $params = array(
            'action_name' => 'QR_LIMIT_STR_SCENE',
            'action_info' => array(
                'scene' => array(
                    'scene_str' => $scene_str,
                ),
            ),
        );
        log_message('debug', 'params:' . json_encode($params));
        $retTicket = HttpClient::post($url . $token,
            $params, true);
        log_message('debug', " retTicket: $retTicket ");

        if ($retTicket) {
            return json_decode($retTicket, true);
        } else {
            log_message('error', "getTicket err , retTicket: $retTicket ");
        }
        return false;
    }

    /**
     * 临时二维码ticket
     * @param $token
     * @param $url
     * @param $scene_str
     * @return bool|mixed
     */
    public static function get_temp_ticket($token, $url, $scene_str)
    {

        $params = array(
            'expire_seconds' => 604800,
            'action_name'    => 'QR_SCENE',
            'action_info'    => array(
                'scene' => array(
                    'scene_str' => $scene_str,
                ),
            ),
        );
        log_message('debug', 'params:' . json_encode($params));
        $retTicket = HttpClient::post($url . $token,
            $params, true);
        log_message('debug', " retTicket: $retTicket ");

        if ($retTicket) {
            return json_decode($retTicket, true);
        } else {
            log_message('error', "getTicket err , retTicket: $retTicket ");
        }
        return false;
    }

}