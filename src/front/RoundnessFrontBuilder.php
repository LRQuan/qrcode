<?php


namespace lrq\qrcode\front;


use lrq\qrcode\tools;

class RoundnessFrontBuilder extends BaseFrontBuilder implements FrontBuilder
{
    /**
     * @var resource
     */
    public $point;

    protected function createPoint()
    {
        $point = imagecreatetruecolor($this->pointWidth+$this->gapWidth, $this->pointWidth+$this->gapWidth);
        imagefill($point,0,0,16777215);
        imagefilledellipse($point,round($this->pointWidth/2),round($this->pointWidth/2),$this->pointWidth,$this->pointWidth,0);
        tools::echoImage($point);
        $this->point = $point;
    }
}