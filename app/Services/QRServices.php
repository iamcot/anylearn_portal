<?php

namespace App\Services;
class QRServices
{
    public function QR($money,$content)
    {
        $str ="00020101021238500010A0000007270120000697041601065454460208QRIBFTTA53037045406";
        $str2 ="5802VN62340107NPS68690819";
        $qrcrc = $str.$money.$str2.$content.crc32($str.$money.$str2.$content);
        return $qrcrc;
    }
}
?>
