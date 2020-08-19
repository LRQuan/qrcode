二维码生成与美化工具

```shell script
composer require lrq/qrcode:~1.0.0
```

如果只是想生成一个默认的二维码
```php
use lrq\qrcode\QrcodeMain;

$str = 'test'; //初始化传入需要转化为二维码的字符串
$qrcode = new QrcodeMain($str); //初始化传入需要转化为二维码的字符串
$qrcode->execute();
$qrcode->png('qr1.png'); //保存图片
echo $qrcode->gif(); //输出<img /> 标签
```
部分美化参数如下
```php
$qrcode->setOuterFrame(1) //外边框
    ->setMergeLevel($qrcode::MERGE_LEVEL_NO) //合并点
    ->setPixelPerPoint(5) //设置每个点的像素

    ->setLocator($qrcode::LOCATOR_TYPE_CIRCLE) //设置定位点样式
    ->setLocatorColor(99,200,18) //设置定位点颜色

    ->setFrontColorStatic(10,100,18) //设置前景色
    ->setBackgroundColorGradient($qrcode::GRADIENT_TYPE_LR2RB,[200,55,30],[100,110,210]) //设置渐变背景色

    ->setEptColor(0,0,0) //把某个颜色设置为透明色

    ->setDecal('test.jpg') //中间的贴图
    ->setDecalSize(0.1) //中间贴图的大小

    ->setLocator(null) //移除定位点
    ->setFrontColorGradient(null) //移除前景色渐变
```
