<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\VoucherEvent;

class VoucherServices
{
    public function getVoucherEvents()
    {
        return VoucherEvent::select('id', 'title')
            ->orderByDesc('id')
            ->take(3)
            ->get();
    }

    public function getVoucherEventsBySubtype($subtype)
    {
        // Add voucher_events.type = promote, trigger = subtype.id
        $subtypes = array_flip(config('subtype_list'));
        return VoucherEvent::select('id', 'title', 'trigger')
            ->where('type', 'promote' )
            ->where('trigger', $subtypes[$subtype])
            ->orderByDesc('id')
            ->take(3)
            ->get();
    }

    public function getVoucherByPartner($id) 
    {
        // Add voucher_events.type = partner, trigger = partner.id
        $voucherE = VoucherEvent::where('type', 'partner')->where('trigger', $id)->first();
        return $voucherE ? Voucher::whereIn('voucher_group_id', explode(',', $voucherE->targets))->first() : '';
    }
}
