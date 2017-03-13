<?php

class Access_Token_model extends MY_Model
{
    protected $_table = 'wechat_token';

    public function __construct()
    {
        $this->load->database();
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function get_latest_token()
    {
        $result = $this->_database->select('id,access_token')->order_by('id', 'DESC')->limit(1)->get($this->_table)->result();
        if ($result && isset($result[0])) {
            return $result[0]->access_token;
        } else {
            log_message('error', "getLatestToken  fail , data ");
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function get_latest_token_array()
    {
        $result = $this->_database->select('*')->order_by('id', 'DESC')->limit(1)->get($this->_table)->result();
        if ($result && isset($result[0])) {
            return $result[0];
        } else {
            log_message('error', "getLatestToken  fail , data ");
        }
        return false;
    }
}