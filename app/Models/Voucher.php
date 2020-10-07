<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Voucher extends Model
{
    protected $fillable = [
        'voucher', 'amount', 'value', 'status', 'expired'
    ];

    public function useVoucher($userId, $voucher)
    {
        $dbVoucher = $this->where('voucher', $voucher)->first();
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
        $value = $dbVoucher->value;
        User::find($userId)->update([
            'wallet_m' => DB::raw("wallet_m + $value")
        ]);
        VoucherUsed::create([
            'voucher_id' => $dbVoucher->id,
            'user_id' => $userId
        ]);
        return $value;
        // throw new \Exception("error of voucher");
    }
}
