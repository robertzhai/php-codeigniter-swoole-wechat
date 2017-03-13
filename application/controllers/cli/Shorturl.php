<?php

/**
 * @name   Shorturl
 * @desc   长url转换为短url
 * @author robertzhai
 */
class Shorturl extends CI_Controller
{

    /**
     * Shorturl constructor.
     */
    public function __construct()
    {
        if (!is_cli() || isset($_SERVER['REMOTE_ADDR'])) {
            header("HTTP/1.0 404 Not Found");
            exit('Not Found');
        }
        parent::__construct();
        $this->load->library('HttpClient');
    }

    /**
     * 查询token
     * @return mixed
     */
    private function query_token()
    {
        $this->load->model('Access_Token_model', 'token');
        $strToken = $this->token->get_latest_token();
        if (!$strToken) {
            $errno = Error::GET_TOKEN_ERROR;
            log_message('error', " $errno , " . Error::getMsg($errno));
        }
        return $strToken;
    }

    /**
     * url 转换
     * @return mixed
     */
    public function index()
    {

        $url = 'https://xxx.com/url';
        $arr = array(
            'action'   => 'long2short',
            'long_url' => $url,
        );
        $result = HttpClient::post(
            'https://api.weixin.qq.com/cgi-bin/shorturl?access_token=' . $this->query_token(),
            $arr, true, true);
        return $result;
    }
}