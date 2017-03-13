<?php
/**
 * @desc   desc
 * @author robertzhai
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

    protected $openid;

    /**
     * MY_Controller constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('HttpClient');
        $this->load->library('session');
        if ($this->session->has_userdata(Constant::OPENID_SESSION_KEY)) {
            $this->openid = $this->session->userdata(Constant::OPENID_SESSION_KEY);
            log_message('debug', 'from session $this->openid:' . $this->openid);
        }
        if (isset($_GET['debug']) && $_GET['debug'] == 1) {
            $this->openid = 'test_open_id';
        }
    }

    /**
     * json 输出
     * @param $data
     */
    public function json_output($data)
    {
        log_message('debug', 'json output:' . json_encode($data));
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
        return;
    }

    /**
     * str 输出
     * @param $data
     */
    public function str_output($data)
    {
        $this->output
            ->set_content_type('text/plain', 'UTF-8')
            ->set_output($data);
        return;
    }

    /**
     * check crontab
     */
    public function check_crontab()
    {
        if (!is_cli() || isset($_SERVER['REMOTE_ADDR'])) {
            header("HTTP/1.0 404 Not Found");
            log_message('debug', 'check_crontab fail');
            return;
        }
    }

    protected function auth($url_state, $url)
    {
        if (!$this->openid) {
            $state = $url_state;//该字符串是获取code时自定义的参数。
            if ($this->input->get('state') != $state) {//获取code
                $this->get_code($state, Constant::WECHAT_OPEN_PLATFORM_REDIRECT_URI_BASE . $url); //调用function.php中定义的get_code函数，$state是链接自带参数的
            } else { //获取code之后
                //获取access_token;
                $content = $this->get_access_token();
                log_message('debug', json_encode($content));
                if (isset($content['openid'])) {
                    $this->openid = $content['openid'];
                    $this->session->set_userdata(Constant::OPENID_SESSION_KEY, $this->openid);
                }
            }
        }
        if (!$this->openid) {
            show_error('获取用户信息失败,请稍后重试');
        }
    }

    /*获取code跳转*/
    protected function get_code($state, $url)
    {
        $params = "appid=" . Constant::WECHAT_OPEN_PLATFORM_APPID . "&redirect_uri=" .
            urlencode($url) . "&response_type=code&scope=snsapi_userinfo&state=" .
            $state . "#wechat_redirect";
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . $params;
        log_message('debug', $url);
        header('Location: ' . $url);
        exit;
    }

    //获取网页授权access_token，此access_token非普通的access_token，
    //详情请看微信公众号开发者文档
    protected function get_access_token()
    {
        $param ['appid'] = Constant::WECHAT_OPEN_PLATFORM_APPID; //AppID
        $param ['secret'] = Constant::WECHAT_APP_SECRET; //AppSecret
        $param ['code'] = $this->input->get('code');
        log_message('debug', 'code:' . $this->input->get('code'));
        $param ['grant_type'] = 'authorization_code';

        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query($param);
        $content = HttpClient::get($url, null, true);
        log_message('debug', $content);
        $content = json_decode($content, true);
        if (!$content || !isset ($content ['openid'])) {
            return false;
        }
        return $content;
    }

    /**
     * 通过授权获取用户信息, $content 是数组类型
     * @param $content
     * @return bool|mixed
     */
    protected function get_userinfo_by_auth($content)
    {
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $content ['access_token'] . '&openid=' . $content ['openid'] . '&lang=zh_CN';
        $user = HttpClient::get($url, null, true);
        $user = json_decode($user, true);
        if (!$user || !isset ($content ['openid'])) {
            return false;
        }
        log_message('debug', json_encode($user));
        return $user;
    }

    protected function get_userid_by_openid($openid)
    {
        $this->load->model('Wechat_User_model', 'wechat_user_model');
        $data = $this->wechat_user_model->query_by_openid($openid);
        if ($data && $data->user_id) {
            log_message('debug', 'userid:' . $data->user_id);
            return $data->user_id;
        } else {
            return false;
        }
    }

    protected function encrypt_str($str)
    {
        $this->load->library('encryption');
        $this->encryption->initialize(array('driver' => 'openssl'));
        $result = $this->encryption->encrypt($str);
        log_message('debug', "encrypt_str($str) : $result");
        return $result;
    }

    protected function decrypt_str($str)
    {
        $this->load->library('encryption');
        $this->encryption->initialize(array('driver' => 'openssl'));
        $result = $this->encryption->decrypt($str);
        log_message('debug', "decrypt_str($str) : $result");
        return $result;
    }

}
