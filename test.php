<?php

use lrq\qrcode\locators\RoundnessLocator;
use lrq\qrcode\QrcodeTwo;

require_once 'vendor/autoload.php';


$str = '爱是个斯柯达复赛客服阿萨德管理会计爱搜VN萨克爱是个斯柯达复赛客服阿萨德管理会计爱搜VN萨克爱是个斯柯达复赛客服阿萨德管理会计爱搜VN萨克'; //初始化传入需要转化为二维码的字符串
$str = 'test test test te'; //初始化传入需要转化为二维码的字符串
$qrcode = new QrcodeTwo($str,'L');
$qrcode->locatorClass = RoundnessLocator::class;
$qrcode->execute();
echo $qrcode->png();exit();