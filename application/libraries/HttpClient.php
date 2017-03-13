<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class HttpClient
{

    public static function post($c_url, $c_url_data, $https = false, $format_json = true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $c_url);
        curl_setopt($ch, CURLOPT_POST, 1);

        if ($format_json) {

            if (is_array($c_url_data)) {
                $c_url_data = json_encode($c_url_data,JSON_UNESCAPED_UNICODE);
            }
            log_message('debug', 'data:' . $c_url_data . PHP_EOL);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $c_url_data);

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json; charset=utf-8',
                    'Content-Length: ' . strlen($c_url_data),
                )
            );
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $c_url_data);
        }

        //timeout 3 seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);  // 从证书中检查SSL加密算法是否存在
        }
        $result = curl_exec($ch);
        curl_close($ch);
        unset($ch);
        return $result;
    }

    public static function get($c_url, $c_url_data = null, $https = false)
    {

        $ch = curl_init();
        if ($c_url_data && is_array($c_url_data)) {
            $c_url_data = http_build_query($c_url_data);
        }
        if ($c_url_data) {
            $c_url .= '?' . $c_url_data;
        }
        curl_setopt($ch, CURLOPT_URL, $c_url);
        //timeout 3 seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);  // 从证书中检查SSL加密算法是否存在
        }
        $result = curl_exec($ch);
        curl_close($ch);
        unset($ch);
        return $result;
    }
}

