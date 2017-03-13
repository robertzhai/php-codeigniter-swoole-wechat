<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Wechat extends MY_Controller
{

    /**
     * 响应微信服务器的请求
     * @return mixed
     */
    public function respond()
    {
        $this->load->library('WechatCallback');
        $this->load->model('Wechat_User_model');
        $wechatcallback = new WechatCallback();
        if (!isset($_GET['echostr'])) {
            $wechatcallback->responseMsg();
        } else {
            $wechatcallback->valid();
        }

    }

}