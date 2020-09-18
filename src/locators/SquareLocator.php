<?php


namespace lrq\qrcode\locators;


use lrq\qrcode\tools;

class SquareLocator implements LocatorInterface
{
    protected $locator;
    /**
     * @var float|int
     */
    protected $width;

    public function draw($image, $x, $y)
    {
        ImageCopyResized($image, $this->locator,
            $x,$y,
            0,0,
            $this->width+1, $this->width+1,
            $this->width, $this->width
        );
    }


    public function __construct($pointWidth, $graWidth)
    {
        if (!$this->locator){
            $width = 7*$pointWidth+6*$graWidth;
            $this->width = $width;
            $image = imagecreatetruecolor($width, $width);

            imagefill($image,0,0,16777215);

            imagefilledrectangle($image, 0, 0, $width, $width, 0);
            imagefilledrectangle($image, $pointWidth, $pointWidth, $width-$pointWidth, $width-$pointWidth, 16777215);
            imagefilledrectangle($image, 2*($pointWidth+$graWidth), 2*($pointWidth+$graWidth), $width-2*($pointWidth+$graWidth), $width-2*($pointWidth+$graWidth), 0);

            $this->locator = $image;
        }
    }

}