<?php

namespace lrq\qrcode;
use QrCode;
require_once 'tcpdf/QrCode.php';

/**
 * Class QrCodeService
 * @package app\services
 */
class QrcodeMain
{
    /**
     * 渐变方向
     */
    const GRADIENT_TYPE_L2R = 1; //[1=左往右 → 横],
    const GRADIENT_TYPE_T2B = 2; //[2=上往下 ↓ 竖],
    const GRADIENT_TYPE_RT2LB = 3; //[3=右上往左下 ↙ 撇],
    const GRADIENT_TYPE_LR2RB = 4; //[4=左上往右下 ↘ 捺]

    /**
     * 增加间隙后合并等级
     */
    const MERGE_LEVEL_NO = 0; //[0=不合并]
    const MERGE_LEVEL_LOCATOR = 1; //[1=合并定位符]
    const MERGE_LEVEL_BORDER = 2; //[2=合并四条边]
    const MERGE_LEVEL_ALL = 3; //[3=全合并]
    const LOCATOR_TYPE_CIRCLE = 1; //定位点类型 圆形
    const LOCATOR_TYPE_QUADRANGLE = 2; //定位点类型 正方形
    const LOCATOR_TYPE_DIAMOND = 3; //定位点类型 菱形
    const LOCATOR_TYPE_HOUSE = 4; //定位点类型 房子

    /**
     * 定位点
     * 0=使用原始的,
     * 1=圆形
     * 2=正方形
     * 3=菱形
     * 4=房子
     * @var null
     */
    private $locator = null;

    /**
     * @param $locator
     * @return $this
     * @author lrq
     */
    public function setLocator($locator)
    {
        $this->locator = $locator;
        return $this;
    }

    /**
     * 二维码内容生成的原始矩阵
     * @var array
     */
    private $rawFrame;

    /**
     * @var false|int 前景真数据颜色 RGB
     */
    private $frontColorStatic = [0,0,0];

    /**
     * 渐变背景色优先于纯色背景
     * @var null
     */
    private $frontColorGradient = null;


    /**
     * @var false|int 透明颜色 [r,g,b]
     */
    private $eptColor = null;


    /**
     * @var int 原始数据高
     */
    private $rawH;

    /**
     * @var int 原始数据宽
     */
    private $rawW;

    /**
     * 增加间隙后的宽
     * @var int
     */
    private $intervalW;

    /**
     * 增加间隙后的高
     * @var int
     */
    private $intervalH;

    /**
     * 增加边框后的宽
     * @var float|int
     */
    private $outerFrameW;

    /**
     * 增加边框后的高
     * @var float|int
     */
    private $outerFrameH;

    /**
     * 要返回的图片宽
     * @var float|int
     */
    private $targetW;

    /**
     * 要返回的图片高
     * @var float|int
     */
    private $targetH;

    /**
     * @var string 贴纸文件
     */
    private $decal;

    /**
     * 设置贴纸文件路径
     * @param $decal
     * @return $this
     * @author lrq
     */
    public function setDecal($decal)
    {
        $this->decal = $decal;
        return $this;
    }
    /**
     * 矩阵空隙 1-3
     * @var int
     */
    public $interval = 1;

    /**
     * 添加空隙后的矩阵
     * @var array
     */
    private $IntervalFrame;

    /**
     * 合并等级
     * [0=不合并] [1=合并定位符] [2=合并四条边] [3=全合并]
     * @var int
     */
    public $mergeLevel=self::MERGE_LEVEL_ALL;

    /**
     * @var int 二维码的外边距
     */
    private $outerFrame=0;

    /**
     * 生成二维码后的放大比例
     * @var int
     */
    private $pixelPerPoint=5;

    /**
     * 设置放大比例
     * @param int $pixelPerPoint
     * @return QrcodeMain
     * @author lrq
     */
    public function setPixelPerPoint(int $pixelPerPoint=5)
    {
        $this->pixelPerPoint = $pixelPerPoint;
        return $this;
    }

    /**
     * @var false|resource 图片数据
     */
    private $target_image;

    /**
     * @var false|int 背景色 RGB
     */
    private $backgroundColorStatic = [254,254,254];

    /**
     * 定位符颜色 不支持渐变
     * @var array
     */
    private $locatorColor = null;

    /**
     * 渐变背景色优先于纯色背景
     * @var null
     */
    private $backgroundColorGradient = null;

    /**
     * 贴图大小
     * @var int
     */
    private $decalSize = 5;

    /**
     * 设置贴图大小
     * @param float $size
     * @return $this
     * @author lrq
     */
    public function setDecalSize(float $size=0.2)
    {
        if ($size>0.2){
            $size = 0.2;
        }
        $this->decalSize = 1/$size;
        return $this;
    }

    /**
     * 获取矩阵
     * @return array
     * @author lrq
     */
    public function getRawFrame()
    {
        return $this->rawFrame;
    }

    /**
     * 设置外边距
     * @param $outerFrame
     * @return $this
     * @author lrq
     */
    public function setOuterFrame($outerFrame)
    {
        if ($outerFrame<0){
            $this->outerFrame = 1;
        }elseif ($outerFrame>=3){
            $this->outerFrame = 3;
        }else {
            $this->outerFrame = $outerFrame;
        }
        return $this;
    }

    /**
     * 设置定位符颜色
     * @param $r
     * @param $g
     * @param $b
     * @return QrcodeMain
     * @author lrq
     */
    public function setLocatorColor($r, $g, $b)
    {
        $this->locatorColor = [$r,$g,$b];
        return $this;
    }

    /**
     * 获取二维码的图片数据
     * @return false|resource
     * @author lrq
     */
    public function getImageData()
    {
        return $this->target_image;
    }

    /**
     * 设置纯色背景
     * @param $r
     * @param $g
     * @param $b
     * @return $this
     * @author lrq
     */
    public function setBackgroundColorStatic($r, $g, $b)
    {
        $this->backgroundColorStatic = [$r,$g,$b];
        return $this;
    }

    /**
     * 设置渐变背景
     * @param $startColor array
     * @param $endColor array
     * @param int $type [1=左往右 → 横], [2=上往下 ↓ 竖], [3=右上往左下 ↙ 撇], [4=左上往右下 ↘ 捺]
     * @return QrcodeMain
     * @author lrq
     */
    public function setBackgroundColorGradient($type=null, $startColor=[0,255,0], $endColor=[0,0,255])
    {
        if (!$type){
            $this->backgroundColorGradient = null;
        }else{
            $this->backgroundColorGradient = [$startColor, $endColor, $type];
        }
        return $this;
    }

    /**
     * 设置纯色前景色
     * @param $r
     * @param $g
     * @param $b
     * @return $this
     * @author lrq
     */
    public function setFrontColorStatic($r, $g, $b)
    {
        $this->frontColorStatic = [$r,$g,$b];
        return $this;
    }


    /**
     * 设置渐变前景色
     * @param $startColor array
     * @param $endColor
     * @param int $type [1=左往右 → 横], [2=上往下 ↓ 竖], [3=右上往左下 ↙ 撇], [4=左上往右下 ↘ 捺]
     * @return QrcodeMain
     * @author lrq
     */
    public function setFrontColorGradient($type=self::GRADIENT_TYPE_L2R, $startColor=[0,0,255], $endColor=[0,255,0])
    {
        if (!$type){
            $this->frontColorGradient = null;
        }else{
            $this->frontColorGradient = [$startColor, $endColor, $type];
        }
        return $this;
    }


    /**
     * 设置间隙
     * @param $interval
     * @return $this
     * @author lrq
     */
    public function setInterval($interval)
    {
        if ($interval < 0){
            $this->interval = 1;
        }elseif ($interval > 3){
            $this->interval = 3;
        }else{
            $this->interval = $interval;
        }
        return $this;
    }

    /**
     * [0=不合并] [1=合并定位符] [2=合并四条边] [3=全合并]
     * @param int $level
     * @return QrcodeMain
     * @author lrq
     */
    public function setMergeLevel($level = self::MERGE_LEVEL_ALL)
    {
        $this->mergeLevel = $level;
        return $this;
    }

    /**
     * 设置某个颜色为透明色
     * @param $r
     * @param $g
     * @param $b
     * @return $this
     * @author lrq
     */
    public function setEptColor($r,$g,$b)
    {
        $this->eptColor = [$r,$g,$b];
        return $this;
    }

    /**
     * QrCodeService constructor.
     * @param string $str 二维码的内容
     * @param string $level 二维码容错等级 大约能纠正的内容 [L≈7%] [M≈15%] [Q≈25%] [H≈30%]
     */
    public function __construct(string $str, $level='L')
    {
        $qrCode = new Qrcode($str, $level);
        $rawFrame = $qrCode->getBarcodeArray();
        $this->rawFrame = $rawFrame['bcode'];
        $this->rawH = $this->intervalH = $rawFrame['num_rows'];
        $this->rawW = $this->intervalW = $rawFrame['num_cols'];

    }

    /**
     * 设置数据间隙
     * @author lrq
     */
    private function countIntervalFrame()
    {
        $interval = $this->interval;
        $rawFrame = $this->rawFrame;

        $eptRow = array_fill_keys(range(0,count($rawFrame[0])*$interval-$interval),0);
        $res = array_fill_keys(range(0,count($rawFrame)*$interval-$interval), $eptRow);

        $right = $this->rawH-7;
        $bottom = $this->rawW-7;
        foreach ($rawFrame as $x=>$row) {
            foreach ($row as $y=>$value) {
                if ($this->locator && (($x<7&&$y<7) || ($x<7&&$y>=$right) || ($x>=$bottom&&$y<7))){ //自定义定位标识, 三个角设为空白,避免污染定位标识
                    $value = 0;
                }

                $res[$x*$interval][$y*$interval] = $value;
                if ($this->mergeLevel == self::MERGE_LEVEL_ALL){
                    $this->merge($res, $x, $y);
                }elseif ($this->mergeLevel==self::MERGE_LEVEL_BORDER && (($x<6||$y<6) || ($x>=$right||$y>=$bottom))){
                    $this->merge($res, $x, $y);
                }elseif ($this->mergeLevel==self::MERGE_LEVEL_LOCATOR && (($x<=6 && $y<=6) || ($x<=6 && $y>=$bottom) || ($x>=$right && $y<=6))){
                    $this->merge($res, $x, $y);
                }
            }
        }
        $this->intervalH = count($res);
        $this->intervalW = count($res[0]);
        $this->IntervalFrame = $res;
    }

    /**
     * 打印矩阵数据
     * @param $frame
     * @author lrq
     */
    public function print($frame)
    {
        foreach ($frame as $item) {
            echo implode('', $item), '<br/>';
        }
    }

    /**
     * 合并矩阵间隙
     * @param $res
     * @param $x
     * @param $y
     * @author lrq
     */
    private function merge(&$res, $x, $y)
    {
        $rawFrame = $this->rawFrame;
        $interval = $this->interval;

        if (isset($rawFrame[$x+1]) && $rawFrame[$x+1][$y]==$rawFrame[$x][$y]){
            for ($i=1; $i<$interval; $i++){
                $res[$x*$interval+$i][$y*$interval] = $rawFrame[$x][$y];
            }
        }
        if (isset($rawFrame[$x][$y+1]) && $rawFrame[$x][$y+1]==$rawFrame[$x][$y]){
            for ($i=1; $i<$interval; $i++){
                $res[$x*$interval][$y*$interval+$i] = $rawFrame[$x][$y];
            }
        }
        unset($res);
    }

    /**
     * 矩阵生成图片
     * 254灰被设置为透明色
     * @return false|resource
     * @author lrq
     */
    private function frame2image()
    {
        $this->outerFrameW = $outerFrameW = $this->intervalW + 2*$this->outerFrame;
        $this->outerFrameH = $outerFrameH = $this->intervalH + 2*$this->outerFrame;

        $outFrameImage = imagecreatetruecolor($outerFrameW, $outerFrameH);
        $this->fillBackground($outFrameImage);
        $this->fillFrontColor($outFrameImage);

        $this->target_image = $outFrameImage;
        return $this->target_image;
    }

    /**
     * 执行
     * @author lrq
     */
    public function execute()
    {
        $this->countIntervalFrame();
        $this->frame2image();
        $this->reSizeImage();
        $this->stickDecal();
        $this->stickLocators();

        if ($this->eptColor){
            list($r,$g,$b) = $this->eptColor;
            $eptColor = ImageColorAllocate($this->target_image, $r,$g,$b);
            imagecolortransparent($this->target_image ,$eptColor); //将已经画到图片中的某个颜色定义为透明色
        }
    }

    /**
     * 贴贴纸
     * @author lrq
     */
    private function stickDecal()
    {
        if (!$this->decal){
            return;
        }
        $logo = imagecreatefromstring(file_get_contents($this->decal));
        $logo_width = imagesx($logo);//logo图片宽度
        $logo_height = imagesy($logo);//logo图片高度
        $canScanWidth = $this->targetW/$this->decalSize;
        $canScanHeight = $this->targetH/$this->decalSize;
        $leftTop = [($this->targetW-$canScanWidth)/2, ($this->targetH-$canScanHeight)/2];

        imagecopyresampled(
            $this->target_image, //原始图片
            $logo, //要贴上去的图片
            $leftTop[0],  //贴图左上角在原图的坐标X
            $leftTop[1], //贴图左上角在原图的坐标Y
            0, //扫描贴图的坐标X
            0, //扫描贴图的坐标Y
            $canScanWidth, //覆盖的宽度
            $canScanHeight, //覆盖的高度
            $logo_width, //扫描贴图的宽度
            $logo_height //扫描贴图的高度
        );
        imagedestroy($logo);
    }

    /**
     * 计算渐变色rgb
     * @param resource $image
     * @param $startColor array 渐变开始颜色
     * @param $endColor array 渐变结束的颜色
     * @param $size int 总长度
     * @param $step int 当前所在进度
     * @return false|int
     * @author lrq
     */
    private function getGradient($image, $startColor, $endColor, $size, $step)
    {
        $res = [];
        $res[0] = $startColor[0]+($endColor[0]-$startColor[0])/$size*$step;
        $res[1] = $startColor[1]+($endColor[1]-$startColor[1])/$size*$step;
        $res[2] = $startColor[2]+($endColor[2]-$startColor[2])/$size*$step;
        return ImageColorAllocate($image, $res[0],$res[1],$res[2]);
    }

    /**
     * 把图片保存为png
     * @param $fileName
     * @return string
     * @author lrq
     */
    public function png($fileName=null)
    {
        if (!$this->target_image){
            return false;
        }
        if ($fileName){
            return imagepng($this->target_image,$fileName);
        }
        ob_start();
        imagepng($this->target_image);
        $data = ob_get_clean();

        $str = '<img src="data:image/png;base64,'.base64_encode($data).'" />';
        return $str;
    }

    /**
     * 把图片保存为png
     * @param $fileName
     * @return string
     * @author lrq
     */
    public function jpeg($fileName=null)
    {
        if (!$this->target_image){
            return false;
        }
        if ($fileName){
            return imagejpeg($this->target_image,$fileName);
        }
        ob_start();
        imagejpeg($this->target_image);
        $data = ob_get_clean();

        $str = '<img src="data:image/jpeg;base64,'.base64_encode($data).'" />';
        return $str;
    }

    /**
     * 把图片保存为png
     * @param $fileName
     * @return string
     * @author lrq
     */
    public function gif($fileName=null)
    {
        if (!$this->target_image){
            return false;
        }
        if ($fileName){
            return imagegif($this->target_image,$fileName);
        }
        ob_start();
        imagegif($this->target_image);
        $data = ob_get_clean();

        $str = '<img src="data:image/gif;base64,'.base64_encode($data).'" />';
        return $str;
    }

    /**
     * 填充背景色
     * @param $base_image
     * @author lrq
     */
    private function fillBackground($base_image)
    {
        //纯色
        if (!$this->backgroundColorGradient){
            list($r,$g,$b) = $this->backgroundColorStatic;
            $backendColo = ImageColorAllocate($base_image, $r,$g,$b);
            imagefill($base_image,0,0, $backendColo); //填充背景色
            return;
        }
        
//        //渐变背景色
        $gradient = $this->backgroundColorGradient;
        list($r,$g,$b) = $this->backgroundColorStatic;
        $staticColor = ImageColorAllocate($base_image, $r,$g,$b);

        for($y=0; $y<$this->outerFrameH; $y++) {
            for($x=0; $x<$this->outerFrameW; $x++) {
                $color = $this->getPositionColor($base_image, $gradient, $staticColor, $x, $y, $this->outerFrameH, $this->outerFrameW);
                ImageSetPixel($base_image,$x,$y, $color);
            }
        }

    }

    /**
     * 填充前景色
     * @param $base_image
     * @author lrq
     */
    private function fillFrontColor($base_image)
    {
        $outerFrame = $this->outerFrame;
        $frame = $this->IntervalFrame;

        $this->frontColorGradient;

        $h = $this->outerFrameH;
        $w = $this->outerFrameW;
        list($r,$g, $b) = $this->frontColorStatic;
        $trueStaticColor  = ImageColorAllocate($base_image, $r,$g,$b);
        for($y=0; $y<$this->intervalH; $y++) {
            for($x=0; $x<$this->intervalW; $x++) {
                if ($frame[$y][$x] == '1') {
                    $gradient = $this->frontColorGradient;
                    $color = $this->getPositionColor($base_image, $gradient, $trueStaticColor, $x, $y, $h, $w);
                    ImageSetPixel($base_image,$x+$outerFrame,$y+$outerFrame, $color);
                }
            }
        }

    }

    /**
     * 二维码尺寸变换
     * @return void
     * @author lrq
     */
    private function reSizeImage()
    {
        if ($this->pixelPerPoint<=1){
            return ;
        }
        $base_image = $this->target_image;
        $pixelPerPoint = $this->pixelPerPoint;
        $outerFrameW = $this->outerFrameW;
        $outerFrameH = $this->outerFrameH;
        $this->targetW = $targetW = $outerFrameW * $pixelPerPoint;
        $this->targetH = $targetH = $outerFrameH * $pixelPerPoint;

        $target_image =imagecreatetruecolor($targetW, $targetH);
        ImageCopyResized($target_image, $base_image, 0, 0, 0, 0, $targetW, $targetH, $outerFrameW, $outerFrameH);
        ImageDestroy($base_image);
        $this->target_image = $target_image;
        return ;
    }

    /**
     * 计算某个位置的颜色
     * @param $base_image
     * @param $gradient
     * @param int $staticColor
     * @param int $x
     * @param int $y
     * @param $h
     * @param $w
     * @return false|int
     * @author lrq
     */
    private function getPositionColor($base_image, $gradient, int $staticColor,int $x,int $y, $h, $w)
    {
        if (!$gradient){
            return $staticColor;
        }
        list($startColor, $endColor, $type) = $gradient;
        $diagonal = sqrt($h*$h+$w*$w); //对角线
        if ($type==self::GRADIENT_TYPE_L2R){
            $color = $this->getGradient($base_image, $startColor, $endColor, $w, $x);
        }elseif ($type==self::GRADIENT_TYPE_T2B){
            $color = $this->getGradient($base_image, $startColor, $endColor, $h, $y);
        }elseif ($type==self::GRADIENT_TYPE_RT2LB){  //撇和捺根据勾股定理算步长以及当前步
            $step = sqrt($x*$x+$y*$y);
            $color = $this->getGradient($base_image, $startColor, $endColor, $diagonal, $step);
        }elseif ($type==self::GRADIENT_TYPE_LR2RB){
            $tmpX = $this->outerFrameW-$x;
            $tmpY = $this->outerFrameH-$y;
            $step = sqrt($tmpX*$tmpX+$tmpY*$tmpY);
            $color = $this->getGradient($base_image, $startColor, $endColor, $diagonal, $step);
        }else{
            $color = $this->backgroundColorStatic;
        }
        return $color;
    }


    /**
     * 贴三个定位符
     * @author lrq
     */
    private function stickLocators()
    {
        if (!$this->locator){
            return;
        }
        list($r,$g,$b) = $this->locatorColor;
        $color = imagecolorallocate($this->target_image, $r,$g,$b);
        if ($this->locator){
            $cx = $cy = ($this->outerFrame+3*$this->interval)*$this->pixelPerPoint+$this->pixelPerPoint/2;
            $this->stickLocator($cx, $cy,$color);

            $cx = ($this->outerFrame+3*$this->interval)*$this->pixelPerPoint+$this->pixelPerPoint/2;
            $cy = imagesy($this->target_image)-$cx;
            $this->stickLocator($cx, $cy,$color);

            $cy = ($this->outerFrame+3*$this->interval)*$this->pixelPerPoint+$this->pixelPerPoint/2;
            $cx = imagesx($this->target_image)-$cx;
            $this->stickLocator($cx, $cy,$color);
            return;
        }

    }

    /**
     * 画定位点
     * @param $cx int 中心X坐标
     * @param $cy int 中心Y坐标
     * @param $color int 颜色
     * @author lrq
     */
    private function stickLocator($cx,$cy,$color)
    {
        $multiple = ($this->interval)*($this->pixelPerPoint);
        list($r,$g,$b) = $this->backgroundColorStatic;
        $bColor = imagecolorallocate($this->target_image, $r,$g,$b);

        $offSet = -$multiple+$this->pixelPerPoint/2;
//        $offSet = 0;
        $colors = [[4,$color], [3,$bColor], [2,$color]];

        if ($this->locator == self::LOCATOR_TYPE_CIRCLE){ //原形
            foreach ($colors as $item) {
                list($width, $color) = $item;
                $width = 2*($width*$multiple+$offSet);
                imagefilledellipse($this->target_image, $cx, $cy, $width, $width, $color);
            }
        }elseif ($this->locator == self::LOCATOR_TYPE_QUADRANGLE) { //正方形
            foreach ($colors as $item) {
                list($width, $color) = $item;
                imagefilledrectangle(
                    $this->target_image, $cx-$width*$multiple-$offSet, $cy-$width*$multiple-$offSet,
                    $cx+$width*$multiple+$offSet, $cy+$width*$multiple+$offSet, $color
                );
            }
        }elseif ($this->locator == self::LOCATOR_TYPE_DIAMOND){ //菱形
            foreach ($colors as $item) {
                list($width, $color) = $item;
                $values = [
                    $cx, $cy-$width*$multiple-$offSet, //上
                    $cx+$width*$multiple+$offSet, $cy,//右
                    $cx, $cy+$width*$multiple+$offSet,//下
                    $cx-$width*$multiple-$offSet, $cy //左
                ];
                imagefilledpolygon($this->target_image, $values, count($values)/2, $color);
            }
        }elseif ($this->locator == self::LOCATOR_TYPE_HOUSE){
            foreach ($colors as $item) {
                list($width, $color) = $item;
                $values = [
                    $cx, $cy-$width*$multiple-$offSet, //上
                    $cx+$width*$multiple+$offSet, $cy,//右
                    $cx+$width*$multiple+$offSet, $cy+$width*$multiple+$offSet,//下右
                    $cx-$width*$multiple-$offSet, $cy+$width*$multiple+$offSet,//下左
                    $cx-$width*$multiple-$offSet, $cy //左
                ];
                imagefilledpolygon($this->target_image, $values, count($values)/2, $color);
            }
        }
    }

    public function __destruct()
    {
        if ($this->target_image){
            imagedestroy($this->target_image);
        }
    }

}