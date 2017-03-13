<?php

/**
 * http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html
 * @name   User
 * @desc   desc
 * @author robertzhai
 */
class User extends MY_Controller
{

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * 绑定用户
     * @return void
     */
    public function bind()
    {
        $this->auth('bind', 'user/bind');
        $this->load->view('bind.html');
    }


}