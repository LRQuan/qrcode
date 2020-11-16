<?php


namespace lrq\qrcode\front;


use lrq\qrcode\color\BaseGradientColor;
use lrq\qrcode\color\LineGradientColor;
use lrq\qrcode\color\RoundnessGradientGradientColor;
use lrq\qrcode\tools;

class BaseFrontBuilder implements FrontBuilder
{
    protected $gapWidth;
    protected $pointWidth;
    protected $drawW;

    /**
     * @var resource
     */
    protected $point;

    protected $rawFrame;

    public function __construct($pointWidth, $gapWidth, $drawW, $rawFrame)
    {
        $this->pointWidth = $pointWidth;
        $this->gapWidth = $gapWidth;
        $this->drawW = $drawW;
        $this->rawFrame = $rawFrame;

        $this->createPoint();
    }

    public function draw($image)
    {
        $width = $this->pointWidth+$this->gapWidth;
        $tmpImage = imagecreatetruecolor($this->drawW,$this->drawW);
        imagefill($tmpImage, 0,0,16777215);
        $this->locator($tmpImage);
        for ($x=0; $x<$this->drawW; ){
            for ($y=0; $y<$this->drawW; ){
                if ($this->inFront($x, $y)){
                    imagecopyresized($tmpImage, $this->point,
                        $x, $y, 0,0,
                        $this->pointWidth,$this->pointWidth,
                        $this->pointWidth,$this->pointWidth
                    );
                }
                $y += $width;
            }
            $x += $width;
        }
        imagecolortransparent($tmpImage,0);
        tools::echoImage($tmpImage);
        imagecopyresized($image, $tmpImage,0,0,0,0,$this->drawW,$this->drawW,$this->drawW,$this->drawW);
        imagedestroy($tmpImage);
    }

    protected function inFront(int $x, int $y)
    {
        $addWidth = $this->gapWidth+$this->pointWidth;

        $locatorW = 7*$addWidth;
        if ($x<$locatorW && $y<$locatorW) return false;
        if ($x<$locatorW && $y>=$this->drawW-$locatorW) return false;
        if ($x>=$this->drawW-$locatorW && $y<$locatorW) return false;

        if (!($x%$addWidth < $this->pointWidth)) return false;
        if (!($y%$addWidth < $this->pointWidth)) return false;

        $x = intval($x/$addWidth);
        $y = intval($y/$addWidth);

        if (!$this->rawFrame[$y][$x]) return false;
        return true;
    }

    protected function createPoint()
    {
        $point = imagecreatetruecolor($this->pointWidth, $this->pointWidth);
        imagefilledrectangle($point,0,0,$this->pointWidth,$this->pointWidth,-1);
        $this->point = $point;
    }

    public function __destruct()
    {
        if ($this->point){
            imagedestroy($this->point);
        }
    }

    private function locator($tmpImage)
    {
        $locatorW = 7*$this->pointWidth+6*$this->gapWidth;
        imagefilledrectangle($tmpImage,0,0,$locatorW,$locatorW,0);
        imagefilledrectangle($tmpImage,$this->drawW-$locatorW,0,$this->drawW,$locatorW,0);
        imagefilledrectangle($tmpImage,0,$this->drawW-$locatorW,$locatorW,$this->drawW,0);
    }
}