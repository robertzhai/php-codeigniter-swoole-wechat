<?php
/**
 * @desc   desc
 * @author robertzhai
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Wechat extends CI_Controller
{

    /**
     * Wechat constructor.
     */
    public function __construct()
    {
        if (!is_cli() || isset($_SERVER['REMOTE_ADDR'])) {
            header("HTTP/1.0 404 Not Found");
            exit('Not Found');
        }
        parent::__construct();
        $this->load->library('HttpClient');
        $this->load->model('Access_Token_model', 'token');
    }

    /**
     * 获取token
     * @return mixed
     */
    public function get_access_token()
    {

        $tokenInfo = $this->token->get_latest_token_array();
        if (!$tokenInfo || !$tokenInfo->expirt_time) {
            log_message('error', 'check db info getLatestTokenArray');
            exit('check db info');
        }

        //至少还剩30分钟才进行刷新
        if ($tokenInfo->expirt_time > time() + 60 * 30) {
            log_message('error', 'no need to refresh');
            exit('no need to refresh');
        }

        $params = array(
            'grant_type' => 'client_credential',
            'appid'      => Constant::WECHAT_APP_ID,
            'secret'     => Constant::WECHAT_APP_SECRET,
        );
        $retToken = HttpClient::get(Constant::WECHAT_TOKEN_URL,
            $params, true);

        if ($retToken) {
            $retToken = json_decode($retToken, true);
        } else {
            log_message('error', "get token err , retToken: $retToken ");
        }
        if ($retToken && is_array($retToken) && isset($retToken['access_token']) &&
            $retToken['access_token'] && isset($retToken['expires_in'])
        ) {

            $intTime = time();
            $arrToken = array(
                'access_token' => $retToken['access_token'],
                'expirt_time'  => $retToken['expires_in'] + $intTime,
                'create_time'  => $intTime,
            );
            $result = $this->token->insert($arrToken);

            if (!$result || $result < 1) {
                //重试一次
                $result = $this->token->insert($arrToken);
                if (!$result || $result < 1) {
                    log_message('error', "insert token fail , data: " . json_encode($arrToken));
                    log_message('error', $this->token->last_query_sql());
                }
            } else {
                log_message('debug', "insert token ok , data: " . json_encode($arrToken));
            }
            return $result;
        }


    }

    /**
     * @return mixed
     */
    private function query_token()
    {
        $strToken = $this->token->get_latest_token();
        if (!$strToken) {
            $errno = Error::GET_TOKEN_ERROR;
            log_message('error', " $errno , " . Error::getMsg($errno));
        }
        return $strToken;
    }

    /**
     * 修改菜单
     * php index.php cli/wechat create_menu
     * @return mixed
     */
    public function create_menu()
    {
        $access_token = $this->query_token();
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" . $access_token;
        $arr = array(
            'button' => array(
                array(
                    'name'       => "aaaaa",
                    'sub_button' => array(
                        array(
                            'name' => "bbb",
                            'type' => 'view',
                            'url'  => 'xx',
                        ),
                        array(
                            'name' => "ccc",
                            'type' => 'view',
                            'url'  => 'xx',
                        ),


                    ),
                ),

            ),
        );
        //echo json_encode($arr, JSON_UNESCAPED_UNICODE);
        $result = HttpClient::post($url, $arr, true);
        return $result;

    }

    /**
     * 查询菜单
     * @param $access_token 已获取的ACCESS_TOKEN
     * @return mixed
     */
    public function getmenu()
    {
        $access_token = $this->query_token();
        $url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=" . $access_token;
        $data = file_get_contents($url);
        return $data;
    }

    /**
     * 删除菜单
     * @param $access_token 已获取的ACCESS_TOKEN
     * @return boolean
     */
    public function delmenu()
    {
        $access_token = $this->query_token();
        $url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=" . $access_token;
        $data = json_decode(file_get_contents($url), true);
        if ($data['errcode'] == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 发送微信消息
     * @return mixed
     */
    public function send_message()
    {
        $open_id = 'xx-xx';
        $access_token = $this->query_token();
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $access_token;
        $arr = array(
            'touser'  => $open_id,
            'msgtype' => 'text',
            'text'    => array(
                'content' => 'hello message',
            ),
        );
        $result = HttpClient::post($url, $arr, true);
        return $result;
    }
}
