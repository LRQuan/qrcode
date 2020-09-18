<?php


namespace lrq\qrcode\color;


/**
 * 线性渐变
 * Class LineGradientColorBuilder
 * @package lrq\qrcode\colorBuilder
 *
 */
class LineGradientColor extends BaseGradientColor implements ColorInterface
{
    /**
     * 渐变方向
     */
    const TYPE_L2R = 1; //[1=左往右 → 横],
    const TYPE_T2B = 2; //[2=上往下 ↓ 竖],
    const TYPE_RT2LB = 3; //[3=右上往左下 ↙ 撇],
    const TYPE_LT2RB = 4; //[4=左上往右下 ↘ 捺]


    protected function getStep(int $x, int $y)
    {
        $type = $this->type;
        if ($type == self::TYPE_L2R){
            return $x;
        }elseif ($type==self::TYPE_T2B){
            return $y;
        }elseif ($type == self::TYPE_RT2LB)
            return -$x+$y+$this->width;
        elseif ($type == self::TYPE_LT2RB){
            return $x+$y;
        }
        return 0;
    }

    protected function setSize($width)
    {
        $type = $this->type;
        if ($type == self::TYPE_L2R || $type==self::TYPE_T2B){
            $this->size = $width;
        }elseif ($type==self::TYPE_RT2LB){
            $this->size = 2*$width;
        }elseif ($type == self::TYPE_LT2RB){
            $this->size = 2*$width;
        }
    }

}