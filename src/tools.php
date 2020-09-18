<?php


namespace lrq\qrcode;


class tools
{
    public static function echoImage($image)
    {
        ob_start();
        imagepng($image);
        $data = ob_get_clean();

        $str = '<br/><img src="data:image/png;base64,'.base64_encode($data).'" />';
        echo $str;
    }
}