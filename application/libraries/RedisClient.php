<?php

/**
 * @desc   desc
 * @author robertzhai
 */
class RedisClient
{
    private static $_instance;

    public static function getInstance()
    {
        if (!self::$_instance) {
            try {
                $redis = new Redis();
                $redis->connect('127.0.0.1', 6379);
                if (!$redis) {
                    log_message('error', 'connect redis fail');
                    exit;
                }
                self::$_instance = $redis;
            } catch (Exception $e) {
                log_message('error', 'connect redis fail');
                exit;
            }

        }
        return self::$_instance;
    }
}