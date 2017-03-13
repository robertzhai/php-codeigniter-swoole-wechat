<?php


class Wechat_User_model extends MY_Model
{
    protected $_table = 'wechat_user';

    public function __construct()
    {
        $this->load->database();
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function query_by_openid($openId)
    {
        //        $result = $this->_database->select('id,imei,user_id')->where('id', 'DESC')->limit(1)->get($this->_table)->result();
        $result = $this->get_by('open_id', $openId);
        if ($result) {
            return $result;
        } else {
            log_message('error', "queryByOpenId( $openId ) empty");
        }
        return false;
    }

    public function query_wechat_openid($key, $val)
    {
        $result = array();
        if (!$key || !$val) {
            return $result;
        }
        $user = $this->get_many_by($key, $val);
        if ($user) {
            foreach ($user as $item) {
                $result[] = $item->open_id;
            }
        }
        return $result;
    }
}