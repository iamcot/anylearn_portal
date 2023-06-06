<?php

namespace App\Services;

use App\Constants\ConfigConstants;
use App\Models\Voucher;
use App\Models\VoucherEvent;
use App\Models\VoucherGroup;

class VoucherServices
{
    private function getFixedVouchers($events)
    {
        $vouchers = [];
        foreach($events as $event) {
            $groups = VoucherGroup::whereIn('id', explode(',', $event->targets))
                ->where('generate_type', 'manual')
                ->pluck('prefix')
                ->toArray();

            if ($groups)  {
                $voucher = new \stdClass();      
                $voucher->id = $event->id;
                $voucher->title = $event->title;
                $voucher->code = $groups; 
                $vouchers[] = $voucher; 
            }

            if (count($vouchers) == ConfigConstants::CONFIG_NUM_VOUCHER_DISPLAY) {
                break;
            }
        }

        return $vouchers;
    }
    public function getVoucherEvents()
    {
        $events = VoucherEvent::select('id', 'title', 'targets')
            ->orderByDesc('id')
            ->take(30)
            ->get();
    
        return $this->getFixedVouchers($events);
    }

    public function getVoucherEventsBySubtype($subtype)
    {
        // Add voucher_events.type = promote, trigger = subtype.id
        $subtypes = array_flip(config('subtype_list'));
        $events   =  VoucherEvent::select('id', 'title', 'trigger', 'targets')
            ->where('type', 'promote' )
            ->where('trigger', $subtypes[$subtype])
            ->orderByDesc('id')
            ->take(30)
            ->get();

        return $this->getFixedVouchers($events);
    }

    public function getVoucherByPartner($id) 
    {
        // Add voucher_events.type = partner, trigger = partner.id
        $events = VoucherEvent::where('type', 'partner')->where('trigger', $id)->first();
        return $events ? Voucher::whereIn('voucher_group_id', explode(',', $events->targets))->first() : '';
    }
}
