<?php
/**
 * Created by PhpStorm.
 * User: 二狗
 * Date: 2018/3/16
 * Time: 23:46
 */
//引入类文件

require_once './wechat.class.php';

//进行实例化
$wechat=new WeChat();
//判断是否是进行token认证
$echoStr = $_GET["echostr"];
if(isset($echoStr)&&!empty($echoStr))
{
    $wechat->valid();
}else
{
    $wechat->responseMsg();
}

