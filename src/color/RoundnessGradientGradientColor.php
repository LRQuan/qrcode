<?php


namespace lrq\qrcode\color;


/**
 * 圆心渐变
 * Class GradientColorBuilder
 * @package lrq\qrcode\colorBuilder
 */
class RoundnessGradientGradientColor extends BaseGradientColor implements ColorInterface
{
    private $a;
    private $b;

    public function __construct($width, $type, $startColor, $endColor, $center=[])
    {
        $this->a = $center['x']?? round($width/2);
        $this->b = $center['y']?? round($width/2);
        parent::__construct($width, $type, $startColor, $endColor);
    }

    protected function setSize($width)
    {
        $this->a = $this->b = -$width/2;
        $this->size = intval(sqrt($width*$width*2)/2);
    }

    protected function getStep(int $x, int $y)
    {
        return intval(sqrt(($x+$this->a)*($x+$this->a)+($y+$this->b)*($y+$this->b)));
    }

}