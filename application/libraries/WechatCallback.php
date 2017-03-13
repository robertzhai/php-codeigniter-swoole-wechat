<?php

/**
 * @name   WechatCallback
 * @desc   WechatCallback
 * @author robertzhai
 */
class WechatCallback
{
    private $fromUsername;
    private $app_id;
    private $app_secret;


    public function __construct()
    {
        $this->app_id = Constant::WECHAT_APP_ID;
        $this->app_secret = Constant::WECHAT_APP_SECRET;
    }

    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if ($this->checkSignature()) {
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = Constant::WECHAT_RESPOND_TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    public function responseMsg()
    {
        //获取公众号信息
        $postStr = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : '';
        log_message("debug", $postStr . PHP_EOL);
        if (!empty($postStr)) {

            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
            $this->fromUsername = StrClean::clean($postObj->FromUserName);
            log_message("debug", $RX_TYPE . ', ' . $this->fromUsername . PHP_EOL);
            //用户发送的消息类型判断
            switch ($RX_TYPE) {
                case "text":    //文本消息
                    $result = $this->receiveText($postObj);
                    break;
                case "image":   //图片消息
                    $result = $this->receiveImage($postObj);
                    break;
                case "voice":   //语音消息
                    $result = $this->receiveVoice($postObj);
                    break;
                case "video":   //视频消息
                    $result = $this->receiveVideo($postObj);
                    break;
                case "location"://位置消息
                    $result = $this->receiveLocation($postObj);
                    break;
                case "link":    //链接消息
                    $result = $this->receiveLink($postObj);
                    break;
                case "event":   //事件
                    $result = $this->handleEvent($postObj);
                    break;
                default:
                    $result = "unknow msg type: " . $RX_TYPE;
                    break;
            }
            $result = $result;
            echo $result;
        } else {
            echo "";
            exit;
        }
    }

    private function handleEvent($object)
    {
        //有openid
        $open_id = StrClean::clean($this->fromUsername);
        //处理公众号接收到的事件
        $contentStr = "";
        switch ($object->Event) {
            case "subscribe":


                $contentStr = "xxx";
                break;

            case "SCAN":
                $contentStr = "xxx";
                break;

            case 'unsubscribe':

                $contentStr = "xxx";
                break;

            case "CLICK":
                $contentStr = "xxx";
                break;

            default :
                $contentStr = "Unknow Event: " . $object->Event;
                break;
        }
        $resultStr = $this->transmitText($object, $contentStr);
        return $resultStr;
    }


    /*
     * 接收文本消息
     */
    private function receiveText($object)
    {

        $content = 'xx';


        $result = $this->transmitText($object, $content);
        return $result;
    }

    /*
     * 接收图片消息
     */
    private function receiveImage($object)
    {
        $content = "你发送的是图片，地址为：" . $object->PicUrl;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    /*
     * 接收语音消息
     */
    private function receiveVoice($object)
    {
        $content = "你发送的是语音，媒体ID为：" . $object->MediaId;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    /*
     * 接收视频消息
     */
    private function receiveVideo($object)
    {
        $content = "你发送的是视频，媒体ID为：" . $object->MediaId;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    /*
     * 接收位置消息
     */
    private function receiveLocation($object)
    {
        $content = "你发送的是位置，纬度为：" . $object->Location_X . "；经度为：" . $object->Location_Y . "；缩放级别为：" . $object->Scale . "；位置为：" . $object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    /*
     * 接收链接消息
     */
    private function receiveLink($object)
    {
        $content = "你发送的是链接，标题为：" . $object->Title . "；内容为：" . $object->Description . "；链接地址为：" . $object->Url;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    /*
     * 回复文本消息
     */
    private function transmitText($object, $content)
    {
        $textTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[text]]></MsgType>
        <Content><![CDATA[%s]]></Content>
        </xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }
}