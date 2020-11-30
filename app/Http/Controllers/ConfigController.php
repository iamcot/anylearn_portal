<?php

namespace App\Http\Controllers;

use App\Constants\ConfigConstants;
use App\Constants\FileConstants;
use App\Models\Configuration;
use App\Models\Voucher;
use App\Models\VoucherGroup;
use App\Services\FileServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $fileService = new FileServices();
        $file = $fileService->doUploadImage($request, 'file', FileConstants::DISK_S3, false, FileConstants::FOLDER_BANNERS);
        if ($file !== false) {
            return redirect()->back()->with('notify', true);
        }
        $banners = $fileService->getAllFiles(FileConstants::DISK_S3, FileConstants::FOLDER_BANNERS);
        $this->data['files'] = $fileService->removeSystemFiles($banners);
        $this->data['navText'] = __('Quản lý banner');
        return view('config.banner', $this->data);
    }

    public function delBanner($file)
    {
        $fileService = new FileServices();
        $files = [$fileService->decodeFileName($file)];
        $fileService->deleteFiles($files, FileConstants::DISK_S3);
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
        $this->data['vouchers'] = VoucherGroup::paginate(20);
        $this->data['navText'] = __('Danh sách Bộ Voucher');
        return view('config.voucher_group_list', $this->data);
    }

    public function voucherList(Request $request, $id)
    {
        $this->data['vouchers'] = Voucher::where('voucher_group_id', $id)->paginate(20);
        $this->data['navText'] = __('Danh sách Voucher');
        $this->data['hasBack'] = route('config.voucher');
        return view('config.voucher_list', $this->data);
    }

    public function voucherClose($id)
    {
        $rs = VoucherGroup::find($id)->update(['status' => DB::raw('1 - status')]);
        return redirect()->back()->with('notify', $rs);
    }


    public function voucherEdit(Request $request)
    {
        if ($request->get('save')) {
            $input = $request->input();
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
                        $newVoucher = Voucher::create([
                            'voucher_group_id' => $newGroup->id,
                            'voucher' => Voucher::buildAutoVoucher($newGroup->prefix),
                            'amount' => 1,
                            'value' => $newGroup->value,
                            'status' => 1,
                            'expired' => 0
                        ]);
                    }
                } else {
                    $newVoucher = Voucher::create([
                        'voucher_group_id' => $newGroup->id,
                        'voucher' => $newGroup->prefix,
                        'amount' => $newGroup->qtt,
                        'value' => $newGroup->value,
                        'status' => 1,
                        'expired' => 0
                    ]);
                }
            }

            return redirect()->route('config.voucher')->with('notify', 'Thao tác thành công.');
        }

        $this->data['navText'] = __('Quản lý voucher');
        return view('config.voucher_form', $this->data);
    }

    public function homePopup(Request $request)
    {
        if ($request->get('save')) {

            $lastConfig = Configuration::where('key', ConfigConstants::CONFIG_HOME_POPUP)->first();
            $version = 0;
            $lastImage = "";
            if (!empty($lastConfig)) {
                $data = json_decode($lastConfig->value, true);
                $version = $data['version'];
                $lastImage = $data['image'];
                Configuration::where('key', ConfigConstants::CONFIG_HOME_POPUP)->delete();
            }

            $fileService = new FileServices();
            $file = $fileService->doUploadImage($request, 'image', FileConstants::DISK_S3, true, 'popup');

            $config = $request->all();

            Configuration::create([
                'key' => ConfigConstants::CONFIG_HOME_POPUP,
                'type' => ConfigConstants::TYPE_CONFIG,
                'value' => json_encode([
                    'image' => empty($file) ? $lastImage : $file['url'],
                    'title' => $config['title'],
                    'route' => $config['route'],
                    'args' => $config['args'],
                    'version' => (int)$version + 1,
                    'status' => isset($config['status']) && $config['status'] == 'on' ? 1 : 0,
                ]),
            ]);
            return redirect()->back()->with('notify', 'Cập nhật thành công');
        }
        $lastConfig = Configuration::where('key', ConfigConstants::CONFIG_HOME_POPUP)->first();
        if ($lastConfig) {
            $this->data['config'] = json_decode($lastConfig->value, true);
        }
        $this->data['navText'] = __('Quản lý Popup Quảng cáo');
        return view('config.homepopup', $this->data);
    }
}
