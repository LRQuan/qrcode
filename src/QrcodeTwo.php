<?php

namespace lrq\qrcode;
use Exception;
use lrq\qrcode\color\BaseGradientColor;
use lrq\qrcode\color\LineGradientColor;
use lrq\qrcode\color\RoundnessGradientGradientColor;
use lrq\qrcode\front\BaseFrontBuilder;
use lrq\qrcode\front\RoundnessFrontBuilder;
use lrq\qrcode\locators\RoundnessLocator;
use lrq\qrcode\locators\LocatorInterface;
use lrq\qrcode\locators\SquareLocator;
use QrCode;
require_once 'tcpdf/QrCode.php';

/**
 * Class QrCodeService
 * @package app\services
 */
class QrcodeTwo
{
    private $rawW;
    private $drawW;
    private $rawFrame;
    private $image;
    /**
     * @var int 缝隙宽度
     */
    private $gapWidth = 10;

    /**
     * @var int 点宽度
     */
    private $pointWidth = 1;

    private $borderWidth = 0;

    /**
     * @var float|int
     */
    private $realW;
    /**
     * @var LocatorInterface
     */
    private $locator;

    public $locatorClass = RoundnessLocator::class;

    /**
     * @var BaseFrontBuilder
     */
    private $frontDrawer;
    /**
     * @var BaseGradientColor
     */
    private $frontColorBuild;

    protected $frontColorBuildParams = [
        'class' => RoundnessGradientGradientColor::class,
        'type' => LineGradientColor::TYPE_LT2RB,
        'startColor' => [0,255,0],
        'endColor' => [255,0,0]
    ];

    public function __construct(string $str, $level='L')
    {
        $qrCode = new Qrcode($str, $level);
        $rawFrame = $qrCode->getBarcodeArray();
        $this->rawFrame = $rawFrame['bcode'];
        $this->rawW = $rawFrame['num_cols'];
    }

    /**
     * @author lrq
     * @Date 2020/9/1
     * @Time 19:44
     * @throws Exception
     */
    public function execute()
    {
        $this->create();
        $this->drawFront();
        $this->locators();
        imagecolortransparent($this->image,-1);
        echo $this->png();exit();
    }

    private function drawFront()
    {
        $this->getFrontDrawer()->draw($this->image);
    }


    public function png($fileName=null)
    {
        if (!$this->image){
            return false;
        }
        if ($fileName){
            return imagepng($this->image,$fileName);
        }
        ob_start();
        imagepng($this->image);
        $data = ob_get_clean();

        $str = '<img src="data:image/png;base64,'.base64_encode($data).'" />';
        return $str;
    }

    private function create()
    {
        $this->drawW = $this->pointWidth*$this->rawW;
        $this->drawW += ($this->rawW-1)*$this->gapWidth;
        $image = imagecreatetruecolor($this->drawW, $this->drawW);
        for ($x=0; $x<$this->drawW; $x++){
            for ($y=0; $y<$this->drawW; $y++){
                $color = $this->getFrontColorBuild()->getColor($x,$y);
                ImageSetPixel($image,$x,$y,$color);
            }
        }

        $this->image = $image;
    }

    protected function getFrontColorBuild()
    {
        if ($this->frontColorBuild){
            return $this->frontColorBuild;
        }
        $width = $this->drawW;
        if ($this->frontColorBuildParams){
            $class = $this->frontColorBuildParams['class'];
            $type = $this->frontColorBuildParams['type'];
            $startColor = $this->frontColorBuildParams['startColor'];
            $endColor = $this->frontColorBuildParams['endColor'];
            $this->frontColorBuild = new $class($width, $type, $startColor, $endColor);
        }else{
            $this->frontColorBuild = new BaseGradientColor($width, 0, [0,0,0], [0,0,0]);
        }
        return $this->frontColorBuild;
    }

    private function locators()
    {
        $locations = $this->getLocations();
        foreach ($locations as $location) {
            $this->getLocators()->draw($this->image, $location[0], $location[1]);
        }
    }



    private function getLocations()
    {
        $locations = [];
        $width = $this->drawW-(7*$this->pointWidth+6*$this->gapWidth)-$this->borderWidth;
        $locations []= [
            $this->borderWidth,
            $this->borderWidth,
        ];
        $locations []= [
            $this->borderWidth,
            $width,
        ];
        $locations []= [
            $width,
            $this->borderWidth,
        ];
        return $locations;
    }


    private function getLocators()
    {
        if ($this->locator){
            return $this->locator;
        }
        if ($this->locatorClass){
            $this->locator = new $this->locatorClass($this->pointWidth, $this->gapWidth);
        }else{
            $this->locator = new SquareLocator($this->pointWidth, $this->gapWidth);
        }
        return $this->locator;
    }

    private function getFrontDrawer()
    {
        if ($this->frontDrawer){
            return $this->frontDrawer;
        }
        $this->frontDrawer = new RoundnessFrontBuilder($this->pointWidth,$this->gapWidth, $this->drawW, $this->rawFrame);

        return $this->frontDrawer;
    }

}