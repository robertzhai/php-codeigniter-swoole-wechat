<?php

/**
 * @name   Pushclient
 * @desc   sotcket 长连接 Pushclient
 * @author robertzhai
 */
class Pushclient extends CI_Controller
{

    public function __construct()
    {
        log_message('debug', 'start push job');
        $cmd = 'ps aux | grep -i Pushclient | grep -v grep | wc -l';
        exec($cmd, $ret);
        log_message('debug', '$ret:' . json_encode($ret));

        if ($ret && intval($ret[0]) >= 3) {
            log_message('debug', 'PushClient already running');
            exit('PushClient already running');
        } else {
            log_message('error', 'PushClient not running, start');
        }
        parent::__construct();
        require APPPATH . 'libraries/resque/demo/init.php';
        Resque::setBackend('127.0.0.1:6379');
        $this->load->library('MailTool');

    }

    /**
     * socket处理
     * @return mixed
     */
    public function socket()
    {
        $client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

        $client->on("connect", function (swoole_client $cli) {
            log_message("debug", "connect " . PHP_EOL);
            $data = 'xx';
            $cli->send($data);  //发送注册信息

        });
        $client->on("receive", function (swoole_client $cli, $data) {
            //..
            $data = 'xx';
            $this->save_data($data);
        });
        $client->on("error", function (swoole_client $cli) {
            log_message('error', 'socket连接错误' . PHP_EOL);
        });
        $client->on("close", function (swoole_client $cli) {
            //$cli->connect(Constant::PUSH_HOST, Constant::PUSH_PORT); //重连
            log_message('error', 'socket断开' . PHP_EOL);
        });

        //配置参数
        $client->set(array(
            'open_length_check'   => 1,
            'package_length_type' => 'C',
        ));

        $client->connect(Constant::PUSH_HOST, Constant::PUSH_PORT);

        //30秒发一次心跳包
        swoole_timer_tick(30 * 1000, function ($timer_id) use ($client) {
            //心跳包内容
            $heartbeat_param = 'ping';
            $heartbeat_bin = pack('C4', 0, 0, 0, strlen($heartbeat_param));  //加4字节宽的包头
            try {
                if (!$client || !($client instanceof swoole_client)) {
                    log_message('error', 'client 被服务端断开,退出重新执行' . PHP_EOL);
                    $ret = MailTool::send('socket报警', 'client 被服务端断开,退出重新执行');
                    if ($ret) {
                        log_message('debug', '发送报警邮件成功');
                    } else {
                        log_message('error', '发送报警邮件失败');
                    }
                    exit('client 被服务端断开,退出重新执行');
                }
                $client->send($heartbeat_bin . $heartbeat_param);
                log_message('debug', '发送心跳' . PHP_EOL);
            } catch (Exception $e) {
                log_message('error', '发送心跳, error:' . $e->getMessage() . PHP_EOL);
                log_message('error', ' 检测心跳错误,执行关闭 $client->close() ' . PHP_EOL);
                exit(' $client->close() ');
            }

        });
    }

    /**
     * @param $data
     * @return bool
     */
    private function save_data($data)
    {
        log_message("debug", " save_data to queue : " . json_encode($data) . PHP_EOL);
        if (!$data) {
            log_message('error', ' illegal push data :' . json_encode($data));
            return false;
        }
        $ret = Resque::enqueue(Constant::REDIS_QUEUE_USER, Constant::JOB_USER_NAME, $data, true);

        log_message('debug', 'Resque::enqueue result:' . $ret . "\n");
        if ($ret) {
            log_message("debug", " save_data to queue succ " . PHP_EOL);
            return true;
        } else {
            log_message("error", " save_data to queue fail " . PHP_EOL);
            return false;
        }
    }


}

        