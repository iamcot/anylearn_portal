<?php

namespace App\Services;

use App\Models\VoucherEvent;

class VoucherServices
{
    public function getVoucherEvents()
    {
        return VoucherEvent::select('id', 'title')
            ->orderByDesc('id')
            ->take(2)
            ->get();
    }

    public function getVoucherEventsBySubtype($subtype)
    {
        // Add voucher_events.type = promote, trigger = subtype.id
        $subtypes = array_flip(config('subtypes'));
        return VoucherEvent::select('id', 'title', 'trigger')
            ->where('type', 'promote' )
            ->where('trigger', $subtypes[$subtype])
            ->orderByDesc('id')
            ->take(2)
            ->get();
    }
}
