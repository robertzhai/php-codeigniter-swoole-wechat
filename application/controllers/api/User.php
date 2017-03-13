<?php

/**
 * http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html
 * @name   User
 * @desc   desc
 * @author robertzhai
 */
class User extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

    }

    /**
     * 获取用户信息
     * @return string
     */
    public function basicinfo()
    {
        $this->auth('basicinfo', 'user/basicinfo');
        if ($this->openid) {
            $this->load->model('Bind_User_model', 'bind_user_model');
            $userInfo = $this->bind_user_model->query_by_openid($this->openid);
            if ($userInfo) {
                $data['user_name'] = StrClean::clean($userInfo->user_name);
                $data['mobile'] = StrClean::clean($userInfo->mobile);
                $data['email'] = StrClean::clean($userInfo->email);

                $result = array(
                    'errno'  => 0,
                    'errmsg' => '',
                    'data'   => $data,
                );
            } elseif ($this->bind_user_model->has_error()) {
                $result = Error::getError(Error::GET_USER_INFO_ERROR);
            } else {
                $result = Error::getError(Error::USER_NOT_REG_ERROR);
            }
        } else {
            $result = Error::getError(Error::GET_WECHAT_INFO_ERROR);
            log_message('error', $result['errmsg']);
        }
        $this->json_output($result);

    }
}