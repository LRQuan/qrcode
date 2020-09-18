<?php


namespace lrq\qrcode\front;


class RoundnessFrontBuilder extends BaseFrontBuilder implements FrontBuilder
{
    /**
     * @var resource
     */
    public $point;

    protected function createPoint()
    {
        $point = imagecreatetruecolor($this->pointWidth,$this->pointWidth);
        imagefill($point,0,0,16777215);
        imagefilledellipse($point,$this->pointWidth/2,$this->pointWidth/2,$this->pointWidth,$this->pointWidth,0);

        $this->point = $point;
    }
}