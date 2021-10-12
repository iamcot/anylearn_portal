<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Voucher extends Model
{
    protected $fillable = [
        'voucher', 'amount', 'value', 'status', 'expired', 'voucher_group_id'
    ];

    public function useVoucher($userId, $voucher)
    {
        $dbVoucher = DB::table('vouchers')
            ->join('voucher_groups AS vg', 'vg.id', '=', 'vouchers.voucher_group_id')
            ->where('vouchers.voucher', $voucher)
            ->where('vouchers.status', 1)
            ->where('vg.status', 1)
            ->select('vg.type', 'vg.ext', 'vouchers.id', 'vouchers.amount', 'vouchers.value')
            ->first();
        if (!$dbVoucher) {
            throw new \Exception("Voucher không có");
        }
        if ($dbVoucher->type != VoucherGroup::TYPE_MONEY) {
            throw new \Exception("Loại voucher không hợp lệ.");
        }
        $userUsed = VoucherUsed::where('user_id', $userId)
            ->where('voucher_id', $dbVoucher->id)
            ->first();
        if ($userUsed) {
            throw new \Exception("Bạn đã sử dụng voucher này rồi.");
        }

        $numUsed = VoucherUsed::where('voucher_id', $dbVoucher->id)->count();
        if ($numUsed >= $dbVoucher->amount) {
            throw new \Exception("Voucher này đã bị sử dụng hết.");
        }
        $value = $dbVoucher->value;
        User::find($userId)->update([
            'wallet_m' => DB::raw("wallet_m + $value")
        ]);

        VoucherUsed::create([
            'voucher_id' => $dbVoucher->id,
            'user_id' => $userId,
        ]);
        return $value;
    }

    public function getVoucherData($userId, $voucher)
    {
        $dbVoucher = DB::table('vouchers')
            ->join('voucher_groups AS vg', 'vg.id', '=', 'vouchers.voucher_group_id')
            ->where('vouchers.voucher', $voucher)
            ->where('vouchers.status', 1)
            ->where('vg.status', 1)
            ->select('vg.type', 'vg.ext', 'vouchers.id', 'vouchers.amount', 'vg.value')
            ->first();
        if (!$dbVoucher) {
            throw new \Exception("Voucher không có");
        }
        $userUsed = VoucherUsed::where('user_id', $userId)
            ->where('voucher_id', $dbVoucher->id)
            ->first();
        if ($userUsed) {
            throw new \Exception("Bạn đã sử dụng voucher này rồi.");
        }
        $numUsed = VoucherUsed::where('voucher_id', $dbVoucher->id)->count();
        if ($numUsed >= $dbVoucher->amount) {
            throw new \Exception("Voucher này đã bị sử dụng hết.");
        }
        return $dbVoucher;
    }

    public function useVoucherClass($userId, $itemId, $dbVoucher)
    {
        if ($dbVoucher->type != VoucherGroup::TYPE_CLASS) {
            throw new \Exception("Loại voucher không hợp lệ.");
        }
        if (!in_array($itemId, explode(",", $dbVoucher->ext))) {
            throw new \Exception("Voucher không dùng cho khóa học này.");
        }

        VoucherUsed::create([
            'voucher_id' => $dbVoucher->id,
            'user_id' => $userId
        ]);
        return true;
    }

    public function useVoucherPayment($userId, $orderId, $dbVoucher)
    {
        if ($dbVoucher->type != VoucherGroup::TYPE_PAYMENT) {
            throw new \Exception("Loại voucher không hợp lệ.");
        }

        VoucherUsed::create([
            'voucher_id' => $dbVoucher->id,
            'user_id' => $userId,
            'order_id' => $orderId
        ]);
        return true;
    }

    public static function buildAutoVoucher($prefix, $length = 6)
    {
        $voucher = $prefix;
        for ($i = 1; $i <= $length; $i++) {
            $voucher .= mt_rand(0, 9);
        }
        return $voucher;
    }
}
