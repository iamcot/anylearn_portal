<?php

namespace Tests\Unit;

use App\PaymentGateway\OnepayLocal;
use Tests\TestCase;

class OnepayTest extends TestCase
{

    public function testCheckHash1()
    {
        $onepayServ = new OnepayLocal();
        $str = "vpc_AcqResponseCode=00&vpc_Amount=10000000&vpc_AuthorizeId=831000&vpc_Card=MC&vpc_CardExp=1225&vpc_CardNum=520000xxx0007&vpc_CardUid=INS-k-Virsn_Ag7gUAB_AQBYzg&vpc_CavvResponseCode=2&vpc_Command=pay&vpc_Locale=vn&vpc_MerchTxnRef=11660115949&vpc_Merchant=TOKENONEPAY&vpc_MerchantAdviceCode=01&vpc_Message=Approved&vpc_NetworkTransactionID=MCC5534990810%20%20&vpc_OrderInfo=1&vpc_PayChannel=WEB&vpc_SecureHash=845E6E81180FE8DF27F8D00B6424E45960BD51925FCE406330115F24171EEA43&vpc_TokenExp=0823&vpc_TokenNum=5200008032677007&vpc_TransactionNo=PAY-bwuncdiRStes7HoAjax6Tg&vpc_TxnResponseCode=0&vpc_Version=2";
        $data = [];
        foreach (explode('&', $str) as $couple) {
            list($key, $val) = explode('=', $couple);
            $data[$key] = $val;
        }

        $res = $onepayServ->checkHash($data);
        $this->assertTrue($res);
    }
    // public function testCheckHash3()
    // {
    //     $onepayServ = new OnepayLocal();
    //     $str = "vpc_AcqResponseCode=00&vpc_Amount=10000000&vpc_AuthorizeId=831000&vpc_Card=MC&vpc_CardExp=1225&vpc_CardNum=520000xxx0007&vpc_CardUid=INS-k-Virsn_Ag7gUAB_AQBYzg&vpc_CavvResponseCode=2&vpc_Command=pay&vpc_Locale=vn&vpc_MerchTxnRef=11660020709&vpc_Merchant=TOKENONEPAY&vpc_MerchantAdviceCode=01&vpc_Message=Approved&vpc_NetworkTransactionID=MCC2208460809%20%20&vpc_OrderInfo=1&vpc_PayChannel=WEB&vpc_SecureHash=37871CB30868A91C6A8366A72778BC467DB89D4FBF954D925A125838E1CEFADC&vpc_TokenExp=0823&vpc_TokenNum=5200008281756007&vpc_TransactionNo=PAY-L5igECG5QRuqDCPbGf8TPA&vpc_TxnResponseCode=0&vpc_Version=2";
    //     $data = [];
    //     foreach (explode('&', $str) as $couple) {
    //         list($key, $val) = explode('=', $couple);
    //         $data[$key] = $val;
    //     }

    //     $res = $onepayServ->checkHash($data);
    //     $this->assertTrue($res);
    // }
    public function testCheckHash2()
    {
        $onepayServ = new OnepayLocal();
        $str = "vpc_Amount=10000000&vpc_Card=970436&vpc_CardNum=970436xxx0002&vpc_CardUid=INS-ml6Ylv1KjgLgUAB_AQAZLA&vpc_Command=pay&vpc_Locale=vn&vpc_MerchTxnRef=11660021311&vpc_Merchant=TOKENONEPAY&vpc_Message=Approved&vpc_OrderInfo=1&vpc_PayChannel=WEB&vpc_SecureHash=B158CDD067DB0DE2CA5EF4C29D2057EE60331A4AADCC41C11587EA3BBCE28B9D&vpc_TokenExp=0823&vpc_TokenNum=9704366861722002&vpc_TransactionNo=PAY-JbRNI-v6T86SVPnNNh7PUg&vpc_TxnResponseCode=0&vpc_Version=2";
        $data = [];
        foreach (explode('&', $str) as $couple) {
            list($key, $val) = explode('=', $couple);
            $data[$key] = $val;
        }

        $res = $onepayServ->checkHash($data);
        $this->assertTrue($res);
    }
}
