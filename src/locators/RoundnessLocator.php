<?php


namespace lrq\qrcode\locators;


use lrq\qrcode\tools;

class RoundnessLocator extends SquareLocator implements LocatorInterface
{

    public function __construct($pointWidth, $graWidth)
    {
        if (!$this->locator){
            $width = 7*$pointWidth+6*$graWidth;
            $this->width = $width;

            $image = imagecreatetruecolor($width+1, $width+1);

            imagefill($image,0,0,16777215);

            $cx = $cy = $width>>1;
            imagefilledellipse($image, $cx, $cy, $width, $width, 0);

            $width = 5*$pointWidth+4*$graWidth;
            imagefilledellipse($image, $cx, $cy, $width, $width, 16777215);

            $width = 3*$pointWidth+2*$graWidth;
            imagefilledellipse($image, $cx, $cy, $width, $width, 0);

            imagecolortransparent($image, 0);

            tools::echoImage($image);
            $this->locator = $image;
        }
        parent::__construct($pointWidth, $graWidth);
    }

}