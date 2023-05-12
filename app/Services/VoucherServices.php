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

    public function getVoucherEventsBySubtype($subtypes)
    {
        return VoucherEvent::select('id', 'title')
            ->orderByDesc('id')
            ->take(2)
            ->get();
    }
}
