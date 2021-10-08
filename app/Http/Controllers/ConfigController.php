<?php

namespace App\Http\Controllers;

use App\Constants\ConfigConstants;
use App\Constants\FileConstants;
use App\Models\Configuration;
use App\Models\Voucher;
use App\Models\VoucherEvent;
use App\Models\VoucherGroup;
use App\Services\FileServices;
use Geocoder\Laravel\Facades\Geocoder;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vanthao03596\HCVN\Models\District;
use Vanthao03596\HCVN\Models\Province;
use Vanthao03596\HCVN\Models\Ward;

class ConfigController extends Controller
{
    public function site(Request $request)
    {
        $configM = new Configuration();
        if ($request->input('save')) {
            $configs = $request->input('config');
            foreach ($configs as $key => $config) {
                $configM->createOrUpdate($key, $config, ConfigConstants::TYPE_CONFIG);
            }
            return redirect()->back()->with('notify',  true);
        }
        $this->data['configs'] = $configM->getSiteConfigs();
        $this->data['navText'] = __('Cài đặt thông số hệ thống');
        return view('config.site', $this->data);
    }

    public function guide($type)
    {
        if (!isset(ConfigConstants::$guideTitle[$type])) {
            return redirect()->back();
        }
        $this->data['guideType'] = $type;
        $this->data['data'] = null;
        $data = Configuration::where('key', $type)->first();
        if ($data) {
            $this->data['data'] = $data->value;
        }
        $this->data['navText'] = __(ConfigConstants::$guideTitle[$type]);
        $fileService = new FileServices();
        $fileService->getImagesOfData($this->data['data']);
        return view('config.guide', $this->data);
    }

    public function guideUpdate(Request $request, $type)
    {
        if (!isset(ConfigConstants::$guideTitle[$type])) {
            return redirect()->back();
        }
        if (empty($request->get('data'))) {
            return redirect()->back()->with('notify', __("Không thể lưu nội dung trống"));
        }
        $fileService = new FileServices();
        $fileService->cleanNotUsedImages($request->get('data'));
        $configM = new Configuration();
        $rs = $configM->createOrUpdate($type, $request->get('data'), ConfigConstants::TYPE_GUIDE);
        return redirect()->back()->with('notify', $rs);
    }

    public function banner(Request $request)
    {
        $key = ConfigConstants::CONFIG_APP_BANNERS;
        if ($request->hasFile('file')) {
            $fileService = new FileServices();
            $file = $fileService->doUploadImage($request, 'file', FileConstants::DISK_S3, false, FileConstants::FOLDER_BANNERS);
            if ($file !== false) {
                $banners = [];
                $dbBanners = Configuration::where('key', $key)->first();
                if ($dbBanners) {
                    $banners = json_decode($dbBanners->value, true);
                }
                $banners[count($banners)] = [
                    'file' => $file['url'],
                    'route' => $request->get('route'),
                    'arg' => $request->get('arg'),
                ];
                Configuration::where('key', $key)->delete();
                Configuration::create([
                    'key' => $key,
                    'type' => ConfigConstants::TYPE_CONFIG,
                    'value' => json_encode($banners),
                ]);
                return redirect()->back()->with('notify', true);
            }
        }
        $dbBanners = Configuration::where('key', $key)->first();
        if ($dbBanners) {
            $banners = json_decode($dbBanners->value, true);
        } else {
            $banners = [];
        }

        $this->data['files'] = $banners;
        $this->data['navText'] = __('Quản lý banner');
        return view('config.banner', $this->data);
    }

    public function delBanner($index)
    {
        $key = ConfigConstants::CONFIG_APP_BANNERS;
        $dbBanners = Configuration::where('key', $key)->first();
        if (!$dbBanners) {
            return redirect()->back();
        }
        $banners = json_decode($dbBanners->value, true);
        $bannerToDelete = $banners[$index];

        $fileService = new FileServices();
        $files = ['banners/' . $bannerToDelete['file']];
        $fileService->deleteFiles($files, FileConstants::DISK_S3);

        $newBanners = [];
        foreach ($banners as $i => $url) {
            if ($i != $index) {
                $newBanners[$i] = $url;
            }
        }

        Configuration::where('key', $key)->delete();
        Configuration::create([
            'key' => $key,
            'type' => ConfigConstants::TYPE_CONFIG,
            'value' => json_encode($newBanners),
        ]);
        return redirect()->back()->with('notify', true);
    }

    public function privacy()
    {
        $configM = new Configuration();
        $data = $configM->getDoc(ConfigConstants::GUIDE_PRIVACY);
        if ($data) {
            return $data->value;
        }
        echo "";
    }

    public function voucher(Request $request)
    {
        $this->data['vouchers'] = VoucherGroup::orderby('status', 'desc')
            ->orderby('id', 'desc')
            ->paginate(20);
        $this->data['navText'] = __('Danh sách Bộ Voucher');
        return view('config.voucher_group_list', $this->data);
    }

    public function voucherEvent(Request $request)
    {
        $this->data['events'] = VoucherEvent::orderby('status', 'desc')
            ->orderby('id', 'desc')
            ->paginate(20);
        $this->data['navText'] = __('Danh sách Sự kiện phát Voucher');
        return view('config.voucher_event', $this->data);
    }

    public function voucherEventLog(Request $request, $id)
    {
        $this->data['data'] = DB::table('voucher_event_logs AS vel')
            ->where('vel.voucher_event_id', $id)
            ->orderby('vel.id', 'desc')
            ->paginate(20);
        $this->data['navText'] = __('Danh sách Thành viên sử dụng event');
        $this->data['hasBack'] = route('config.voucherevent');
        return view('config.voucher_event_log', $this->data);
    }

    public function voucherList(Request $request, $id)
    {
        $this->data['vouchers'] = DB::table('vouchers')
            ->where('vouchers.voucher_group_id', $id)
            ->orderby('vouchers.id', 'desc')
            ->select('vouchers.*', DB::raw("(SELECT GROUP_CONCAT(users.phone SEPARATOR ', ') FROM vouchers_used
        join users on vouchers_used.user_id = users.id
        where vouchers_used.voucher_id = vouchers.id) AS used"))
            ->paginate(20);
        $this->data['navText'] = __('Danh sách Voucher');
        $this->data['hasBack'] = route('config.voucher');
        return view('config.voucher_list', $this->data);
    }

    public function voucherClose($type, $id)
    {
        if ($type == 'voucher') {
            $rs = Voucher::find($id)->update(['status' => DB::raw('1 - status')]);
        } else {
            $rs = VoucherGroup::find($id)->update(['status' => DB::raw('1 - status')]);
        }
        return redirect()->back()->with('notify', $rs);
    }

    public function voucherEventClose($id)
    {
        $rs = VoucherEvent::find($id)->update(['status' => DB::raw('1 - status')]);

        return redirect()->back()->with('notify', $rs);
    }

    public function voucherEventEdit(Request $request, $eventId = null)
    {
        if ($request->get('save')) {
            $input = $request->input();
            $data = [
                'type' => $input['type'],
                'title' => $input['title'],
                'trigger' => $input['trigger'],
                'targets' => $input['targets'],
                'notif_template' => $input['notif_template'],
                'email_template' => $input['email_template'],
                'qtt' => $input['qtt'],
                'status' => 1,
            ];

            if (empty($input['id'])) {
                $newEvent = VoucherEvent::create($data);
                if (!$newEvent) {
                    return redirect()->back()->with('notify', 'Tạo sự kiện mới thất bại');
                }
            } else {
                VoucherEvent::find($input['id'])->update($data);
                return redirect()->route('config.voucherevent')->with('notify', 'Thao tác thành công.');
            }
            
        }
        if (!empty($eventId)) {
            $this->data['event'] = VoucherEvent::find($eventId);
        }

        $this->data['navText'] = __('Quản lý Sự kiện phát voucher');
        $this->data['hasBack'] = true;
        return view('config.voucher_event_form', $this->data);
    }

    public function voucherEdit(Request $request)
    {
        if ($request->get('save')) {
            $totalSaved = 0;
            $input = $request->input();
            $partnerVouchers = empty($input['partner_vouchers']) ? [] : explode("\n", $input['partner_vouchers']);
            if ($input['voucher_type'] == VoucherGroup::TYPE_PARTNER) {
                $data = [
                    'type' => VoucherGroup::TYPE_PARTNER,
                    'prefix' => $input['prefix'],
                    'generate_type' => VoucherGroup::TYPE_PARTNER,
                    'qtt' => count($partnerVouchers),
                    'value' => 0,
                    'status' => 1,
                ];
                $newGroup = VoucherGroup::create($data);
                if ($newGroup) {
                    foreach ($partnerVouchers as $voucher) {
                        $existVoucher = Voucher::where('voucher', $voucher)->count();
                        if ($existVoucher > 0) {
                            continue;
                        }
                        Voucher::create([
                            'voucher_group_id' => $newGroup->id,
                            'voucher' => $voucher,
                            'amount' => 1,
                            'value' => $newGroup->value,
                            'status' => 1,
                            'expired' => 0
                        ]);
                        $totalSaved++;
                    }
                }
            } else {
                $data = [
                    'type' => $input['voucher_type'],
                    'generate_type' => $input['generate_type'],
                    'prefix' => $input['prefix'],
                    'qtt' => $input['qtt'],
                    'value' => $input['value'],
                    'status' => 1,
                ];
                $exists = VoucherGroup::where('prefix', $input['prefix'])->count();
                if ($exists > 0) {
                    return redirect()->back()->with('notify', 'Mã này đã tạo');
                }
                if ($data['type'] == VoucherGroup::TYPE_CLASS) {
                    if (empty(trim($input['ext']))) {
                        return redirect()->back()->with('notify', 'Voucher lớp học cần nhập IDs các khóa học.');
                    }
                    $classIds = explode(',', $input['ext']);
                    for ($i = 0; $i < count($classIds); $i++) {
                        $classIds[$i] = intval($classIds[$i]);
                    }
                    $data['ext'] = implode(",", $classIds);
                }
                $newGroup = VoucherGroup::create($data);
                if ($newGroup) {
                    if ($newGroup->generate_type == VoucherGroup::GENERATE_AUTO) {
                        for ($i = 1; $i <= $newGroup->qtt; $i++) {
                            Voucher::create([
                                'voucher_group_id' => $newGroup->id,
                                'voucher' => Voucher::buildAutoVoucher($newGroup->prefix),
                                'amount' => 1,
                                'value' => $newGroup->value,
                                'status' => 1,
                                'expired' => 0
                            ]);
                            $totalSaved++;
                        }
                    } else {
                        Voucher::create([
                            'voucher_group_id' => $newGroup->id,
                            'voucher' => $newGroup->prefix,
                            'amount' => $newGroup->qtt,
                            'value' => $newGroup->value,
                            'status' => 1,
                            'expired' => 0
                        ]);
                        $totalSaved++;
                    }
                }
            }

            return redirect()->route('config.voucher')->with('notify', "Thêm thành công $totalSaved voucher");
        }

        $this->data['navText'] = __('Quản lý voucher');
        return view('config.voucher_form', $this->data);
    }

    public function homePopup(Request $request)
    {
        if ($request->get('save')) {
            $version = 0;
            $lastImage = "";
            $key = $request->get('save') == 'save_app' ? ConfigConstants::CONFIG_HOME_POPUP : ConfigConstants::CONFIG_HOME_POPUP_WEB;

            $lastConfig = Configuration::where('key', $key)->first();
            if (!empty($lastConfig)) {
                $data = json_decode($lastConfig->value, true);
                $version = $data['version'];
                $lastImage = $data['image'];
                Configuration::where('key', $key)->delete();
            }

            $fileService = new FileServices();
            $file = $fileService->doUploadImage($request, 'image', FileConstants::DISK_S3, true, 'popup');

            $config = $request->all();

            Configuration::create([
                'key' => $key,
                'type' => ConfigConstants::TYPE_CONFIG,
                'value' => json_encode([
                    'image' => empty($file) ? $lastImage : $file['url'],
                    'title' => $config['title'],
                    'route' => isset($config['route']) ? $config['route'] : "",
                    'args' => isset($config['args']) ? $config['args'] : "",
                    'version' => (int)$version + 1,
                    'status' => isset($config['status']) && $config['status'] == 'on' ? 1 : 0,
                ]),
            ]);
            return redirect()->back()->with('notify', 'Cập nhật thành công');
        }
        $appConfig = Configuration::where('key', ConfigConstants::CONFIG_HOME_POPUP)->first();
        if ($appConfig) {
            $this->data['config'] = json_decode($appConfig->value, true);
        }
        $webConfig = Configuration::where('key', ConfigConstants::CONFIG_HOME_POPUP_WEB)->first();
        if ($webConfig) {
            $this->data['webconfig'] = json_decode($webConfig->value, true);
        }
        $this->data['navText'] = __('Quản lý Popup Trang Chủ APP/WEB');
        return view('config.homepopup', $this->data);
    }


    public function homeClasses(Request $request)
    {
        if ($request->get('save')) {
            $key = ConfigConstants::CONFIG_HOME_SPECIALS_CLASSES;
            $lastConfig = Configuration::where('key', $key)->first();

            if (!empty($lastConfig)) {
                $data = json_decode($lastConfig->value, true);
                Configuration::where('key', $key)->delete();
            }

            $configs = $request->all();

            $values = [];
            foreach ($configs['block'] as $index => $config) {
                if (empty($config['title'])) {
                    $values[$index] = [];
                } else {
                    $values[$index] = $config;
                }
            }
            Configuration::create([
                'key' => $key,
                'type' => ConfigConstants::TYPE_CONFIG,
                'value' => json_encode($values),
            ]);
            return redirect()->back()->with('notify', 'Cập nhật thành công');
        }
        $config = Configuration::where('key', ConfigConstants::CONFIG_HOME_SPECIALS_CLASSES)->first();
        $this->data['configs'] = [
            [], [], [], [], [],//5
        ];
        if ($config) {
            $values = json_decode($config->value, true);
            for ($i = 0; $i < count($this->data['configs']); $i++) {
                $this->data['configs'][$i] = empty($values[$i]) ? [] : $values[$i];
            }
        }

        $this->data['navText'] = __('Quản lý Các Khoá học Đặc biệt trên HOME APP');
        return view('config.homeclasses', $this->data);
    }


    public function locationTree($level, $parentCode)
    {
        if (!in_array($level, ['ward', 'district', 'ward_path'])) {
            return "";
        }

        if ($level  == 'district') {
            $districts = District::where('parent_code', $parentCode)->orderby('name')->get();
            return response()->json($districts);
        } else if ($level == 'ward') {
            $wards = Ward::where('parent_code', $parentCode)->orderby('name')->get();;
            return response()->json($wards);
        } else if ($level == 'ward_path') {
            $ward = Ward::where('code', $parentCode)->first();
            return response()->json($ward);
        }
    }

    public function locationGeo($address)
    {

        // return response()->json($data);
    }
}
