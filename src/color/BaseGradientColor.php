<?php


namespace lrq\qrcode\color;

/**
 * 获取颜色值 ImageColorAllocate($image,$r,$g,$b)
 *      = $r八位二进制 . $g八位二进制 . $b八位二进制
 *      = $r<<16 + $g<<8 + $b
 *
 * 渐变(r,g,b)=开始值+(开始结束值差/总步长*当前步)
 *      单独计算r,g,b 开始值+(开始结束值差/总步长*当前步) 再用渐变(r,g,b)获取色值
 *      = 开始色值+(开始结束色值差/总步长*当前步)
 * Class BaseGradientColorBuilder
 * @package lrq\qrcode\colorBuilder
 */
class BaseGradientColor implements ColorInterface
{

    protected $size;
    protected $startColorValue;
    protected $type;
    protected $width;

    /**
     * @var float|int
     */
    protected $stepB;
    /**
     * @var float|int
     */
    protected $stepG;
    /**
     * @var float|int
     */
    protected $stepR;

    public function getColor(int $x, int $y)
    {
        $step = $this->getStep($x,$y);
        return $this->startColorValue
            + $this->color2int($step*$this->stepR, $step*$this->stepG,$step*$this->stepB);
    }

    public function __construct($width, $type, $startColor, $endColor)
    {
        $this->type = $type;
        $this->width = $width;
        $this->setSize($width);
        $this->setStepColor($startColor, $endColor);
    }

    protected function setStepColor($startColor, $endColor)
    {
        $this->startColorValue = $this->color2int($startColor[0], $startColor[1], $startColor[2]);
        $this->stepR = ($endColor[0]-$startColor[0])/$this->size;
        $this->stepG = ($endColor[1]-$startColor[1])/$this->size;
        $this->stepB = ($endColor[2]-$startColor[2])/$this->size;
    }

    protected function color2int($r, $g, $b)
    {
        return ($r<<16) + ($g<<8) + $b;
    }

}