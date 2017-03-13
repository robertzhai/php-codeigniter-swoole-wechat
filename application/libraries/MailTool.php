<?php

/**
 * @desc   Mail.php
 * @author robertzhai
 */
class MailTool
{

    public static function send($subject = '报警', $message = '报警内容')
    {
        $CI =& get_instance();
        $CI->load->library('email');
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'smtp.163.com';
        $config['smtp_user'] = 'xxx';
        $config['smtp_pass'] = 'xx';
        $config['smtp_port'] = '25';
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = true;
        $config['smtp_timeout'] = '10';
        $CI->email->initialize($config);

        $CI->email->from('xx@163.com', 'xx');
        $CI->email->to('xx@163.com');

        $CI->email->subject($subject);
        $CI->email->message($message);

        $ret = $CI->email->send();
        if ($ret) {
            return true;
        } else {
            log_message('error', $CI->email->print_debugger());
            return false;
        }
    }
}