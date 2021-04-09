<?php

namespace App\Models;

use App\Constants\NotifConstants;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoucherEventLog extends Model
{
    protected $table = 'voucher_event_logs';

    protected $fillable = [
        'voucher_event_id', 'user_id', 'trigger', 'target', 'data', 'voucher_id'
    ];

    public function useEvent($type, $userId, $triggerId)
    {
        $existsEvent = VoucherEvent::where('type', $type)
            ->where('trigger', $triggerId)
            ->orderby('id', 'desc')
            ->first();
        if (!$existsEvent) {
            return false;
        }
        $eventLogged = $this->where('voucher_event_id', $existsEvent->id)->count();
        if ($eventLogged >= $existsEvent->qtt) {
            return false;
        }

        $notifM = new Notification();
        $voucherGroupIds = explode(',', $existsEvent->targets);
        try {
            foreach ($voucherGroupIds as $voucherId) {
                $voucherGroup = VoucherGroup::where('id', trim($voucherId))
                    ->where('status', 1)
                    ->first();
                if (!$voucherGroup) {
                    continue;
                }
                if ($voucherGroup->generate_type == VoucherGroup::GENERATE_MANUALLY) {
                    $voucher = Voucher::where('voucher_group_id', $voucherGroup->id)->first();
                } else {
                    $voucher = DB::table('vouchers')
                        ->leftJoin('voucher_event_logs AS vel', 'vel.voucher_id', '=', 'vouchers.id')
                        ->leftJoin('vouchers_used AS vu', 'vu.voucher_id', '=', 'vouchers.id')
                        ->where('vouchers.voucher_group_id', $voucherGroup->id)
                        ->whereNull('vel.id')
                        ->whereNull('vu.id')
                        ->select('vouchers.*')
                        ->first();
                }
                if (!$voucher) {
                    continue;
                }

                $newEventLog = VoucherEventLog::create([
                    'voucher_event_id' => $existsEvent->id,
                    'user_id' => $userId,
                    'trigger' => $triggerId,
                    'target' => $voucherId,
                    'voucher_id' => $voucher->id,
                    'data' => $voucher->voucher,
                ]);
                if ($newEventLog) {
                    if ($voucherGroup->type == VoucherGroup::TYPE_MONEY) {
                        $notifM->createNotif(NotifConstants::VOUCHER_MONEY_SENT, $userId, [
                            'voucher' => $voucher->voucher,
                            'amount' => $voucher->value,
                        ]);
                    } elseif ($voucherGroup->type == VoucherGroup::TYPE_CLASS) {
                        $classesDB = DB::table('items')->whereIn('id', explode(',', $voucherGroup->ext))
                            ->select(DB::raw("GROUP_CONCAT(title SEPARATOR ', ') AS class"), DB::raw("GROUP_CONCAT(id SEPARATOR ',') AS ids"))
                            ->first();
                        if (!empty($classesDB)) {
                            $notifData = [
                                'voucher' => $voucher->voucher,
                                'class' => $classesDB->class,
                            ];
                            $firstId = explode(",", $classesDB->ids)[0];
                            $notifData['args'] = $firstId;
                            $notifM->createNotif(NotifConstants::VOUCHER_CLASS_SENT, $userId, $notifData);
                        }
                    } elseif ($voucherGroup->type == VoucherGroup::TYPE_PARTNER) {
                        $notifM->createNotif(NotifConstants::VOUCHER_PARTNER_SENT, $userId, [
                            'voucher' => $voucher->voucher,
                            'partner' => $voucherGroup->prefix,
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return false;
        }
        return true;
    }
}
