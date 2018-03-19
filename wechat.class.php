<?php
/**
 * Created by PhpStorm.
 * User: 二狗
 * Date: 2018/3/16
 * Time: 23:46
 */
require_once './wechat.cfg.php';
class wechat
{
    //设置6个数值，在回复消息的时候使用
    public $fromUsername;//设置发送方（接收方）
    public $toUsername;  //设置接收方（发送方）
    public $msgType;     //设置发送的类型
    public $time;        //设置消息的时间
    public $keyword;     //接受到的内容
    public $msgId;       //消息的id
    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    /**
     * @return bool
     * @throws Exception
     * 进行token值的判断
     */
    private function checkSignature()
    {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 进行消息的发送
     */
    public function responseMsg()
    {
        //get post data, May be due to the different environments 获取post数据
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        //extract post data 对post数据进行判断
        if (!empty($postStr)){
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
               the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $FromUserName = $postObj->FromUserName;
            $ToUserName = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $CreateTime = time();
           // $msgType=$postObj->MsgType; 暂时不使用。
            $MsgId=$postObj->MsgId;
            if(!empty( $keyword ))
            {
                switch($keyword)
                {
                   case '文本';
                       $this->sendText($ToUserName,$FromUserName,$CreateTime,$msgType='text',$contentStr='',$MsgId);
                       break;
                   case '图片';
                       $this->sendImg($ToUserName,$FromUserName,$CreateTime,$MsgType='image',$PicUrl='',$MediaId='',$MsgId);
                       break;
                }
            }else{
                echo "Input something...";
            }

        }else {
            echo "";
            exit;
        }
    }


    /**
     * 回复文本使用的模板
     * @param $fromUsername //接受方
     * @param $toUsername //发送方
     * @param $time //消息创建时间
     * @param string $msgType //消息类型
     * @param string $contentStr //消息内容
     * @param $msgId //消息id
     */
    public function sendText($toUsername,$fromUsername,$time,$msgType='text',$contentStr='',$msgId)
    {
        $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <MsgId>%s</MsgId>
					</xml>";
        $msgType = "text";
        $contentStr = "Welcome to wechat world!";
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr,$msgId);
        echo $resultStr;
    }



    public function sendImg($ToUserName,$FromUserName,$CreateTime,$MsgType='image',$PicUrl='',$MediaId='',$MsgId)
    {
        $textTpl="<xml>
                        <ToUserName>< ![CDATA[%s] ]></ToUserName>
                        <FromUserName>< ![CDATA[%s] ]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType>< ![CDATA[%s] ]></MsgType>
                        <PicUrl>< ![CDATA[%s] ]></PicUrl>
                        <MediaId>< ![CDATA[%s] ]></MediaId>
                        <MsgId>%s</MsgId>
                  </xml>";
        $PicUrl='rU4ZKheoUu2MBu7y9ZwuX8pbPFBcKuLhoyi6rjrOCZxDVF4uJJogulQpseBjP_eX';
        $MediaId='rU4ZKheoUu2MBu7y9ZwuX8pbPFBcKuLhoyi6rjrOCZxDVF4uJJogulQpseBjP_eX'; //多媒体上传接口id
        $resultStr=sprintf($textTpl,$FromUserName,$ToUserName,$CreateTime,$MsgType,$PicUrl,$MediaId,$MsgId);
        echo $resultStr;
    }












}