<?php


namespace lrq\qrcode\locators;


interface LocatorInterface
{
    public function draw($image, $x, $y);

    public function __construct($pointWidth, $graWidth);

}