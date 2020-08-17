<?php

use lrq\qrcode\QrcodeMain;

require_once 'vendor/autoload.php';

$str = 'test'; //初始化传入需要转化为二维码的字符串
$qrcode = new QrcodeMain($str,'L');

$qrcode
    ->setLocator($qrcode::LOCATOR_TYPE_CIRCLE) //设置外边距
    ->setInterval(3)
    ->setMergeLevel($qrcode::MERGE_LEVEL_NO)
    ->execute();
$qrcode->png('qr1.png');
$png1 = $qrcode->gif();
$qrcode
    ->setOuterFrame(1)
    ->setMergeLevel($qrcode::MERGE_LEVEL_NO) //合并点
    ->setPixelPerPoint(5) //设置每个点的像素

    ->setLocator($qrcode::LOCATOR_TYPE_CIRCLE) //设置定位点样式
    ->setLocatorColor(99,200,18) //设置定位点颜色

    ->setFrontColorStatic(10,100,18) //设置前景色
    ->setBackgroundColorGradient($qrcode::GRADIENT_TYPE_LR2RB,[200,55,30],[100,110,210]) //设置渐变背景色
    ->setEptColor(1,0,0) //把某个颜色设置为透明色
    ->execute();
$qrcode->png('qr2.png');
$png2 = $qrcode->png();

$qrcode->setInterval(2) //设置点间空隙

    ->setDecal('test.png') //设置中间的贴图
    ->setDecalSize(0.1) //设置中间贴图的大小

    ->setLocator($qrcode::LOCATOR_TYPE_DIAMOND) //设置定位点样式
    ->setLocatorColor(140,100,46) //设置定位点颜色
    ->setMergeLevel($qrcode::MERGE_LEVEL_BORDER)

    ->setBackgroundColorStatic(255,255,255) //设置背景色
    ->setBackgroundColorGradient()
    ->setMergeLevel($qrcode::MERGE_LEVEL_ALL)

    ->setFrontColorGradient($qrcode::GRADIENT_TYPE_T2B, [200,10,255],[100,200,100]) //设置渐变前景色
    ->setEptColor(255,255,255) //把某个颜色设置为透明色
    ->execute(); //生成图片数据

$qrcode->png('qr3.png'); //保存图片
$png3 = $qrcode->png(); //打印图片

$qrcode->setLocator(null)
    ->setFrontColorGradient(null)
    ->execute();
$qrcode->png('qr4.png');
$png4 = $qrcode->png();

echo $png1;
echo '<br/>';
echo $png2;
echo '<br/>';
echo $png3;
echo '<br/>';
echo $png4;
echo '<br/>';
$qrcode->print($qrcode->getRawFrame()); //打印矩阵数据
exit();