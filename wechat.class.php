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
            $FromUserName = $postObj->FromUserName; //用户的标识
            $ToUserName = $postObj->ToUserName;  //微信服务器的标识
            $keyword = trim($postObj->Content);//用户发送过来的内容
            $MsgId=$postObj->MsgId; //消息的id
            if(!empty( $keyword ))
            {
                switch($keyword)
                {
                   case '文本';
                       $this->sendText($postObj);
                       break;
                   case '图片';
                       $this->sendImg($postObj);
                       break;
                   case '语音';
                       $this->sendVoice($postObj);
                       break;
                   case '视频';
                       $this->sendVideo($postObj);
                       break;
                   case '音乐';
                       $this->sendMusic($postObj);
                       break;
                    case '图文';
                        $this->sendImgText($postObj);
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
     * 回复文本消息
     * @param $postObj
     */
    public function sendText($postObj)
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
        $contentStr = "Welcome to duanzhihong world!";
        $resultStr = sprintf($textTpl, $postObj->FromUserName, $postObj->ToUserName,time(), $msgType, $contentStr,$postObj->MsgId);
        echo $resultStr;
    }


    /**
     * 回复图片消息
     * @param $postObj
     */
    public function sendImg($postObj)
    {
        $imgTpl="<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Image>
                            <MediaId><![CDATA[%s]]></MediaId>
                        </Image>
                 </xml>";
        $MsgType='image'; //返回的文件的类型、
        $MediaId='rU4ZKheoUu2MBu7y9ZwuX8pbPFBcKuLhoyi6rjrOCZxDVF4uJJogulQpseBjP_eX'; //多媒体上传接口id
        $resultStr=sprintf($imgTpl,$postObj->FromUserName,$postObj->ToUserName,time(),$MsgType,$MediaId);
        echo $resultStr;
    }

    /**
     * 回复语音消息
     */

    /**
     * 回复视频消息
     */

    /**
     * 回复音乐消息
     */

    /**
     * 回复单个图文消息
     * @param $postObj
     */
    public function sendImgText($postObj)
    {
          $imgTextTel="<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <ArticleCount>1</ArticleCount>
                        <Articles>
                            <item>
                                <Title><![CDATA[%s]]></Title>
                                <Description><![CDATA[%s]]></Description>
                                <PicUrl><![CDATA[%s]]></PicUrl>
                                <Url><![CDATA[%s]]></Url>
                            </item>
                        </Articles>
                       </xml>";
          $MsgType='news';
          $Title='我在北京';
          $Description='今天是03月21号';
          $PicUrl='http://47.94.170.230/wechat/image/test.jpg';
          $Url='http://www.baidu.com';
          $resultStr=sprintf($imgTextTel,$postObj->FromUserName,$postObj->ToUserName,time(),$MsgType,$Title,$Description,$PicUrl,$Url);
          echo $resultStr;
    }









}