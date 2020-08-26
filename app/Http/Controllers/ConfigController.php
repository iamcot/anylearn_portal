<?php

namespace App\Http\Controllers;

use App\Constants\ConfigConstants;
use App\Constants\FileConstants;
use App\Models\Configuration;
use App\Models\Voucher;
use App\Services\FileServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

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
        $this->data['vouchers'] = Voucher::all();
        $this->data['navText'] = __('Danh sách voucher');
        return view('config.voucher_list', $this->data);
    }

    public function voucherEdit(Request $request, $id = null)
    {
        if ($request->get('save')) {
            $input = $request->input();
            if (empty($input['id'])) {
                $newVoucher = Voucher::create([
                    'voucher' => $input['voucher'],
                    'value' => $input['value'],
                    'amount' => $input['amount'],
                    'expired' => strtotime($input['expired']),
                ]);
            } else {
                Voucher::find($input['id'])->update([
                    'voucher' => $input['voucher'],
                    'value' => $input['value'],
                    'amount' => $input['amount'],
                    'expired' => strtotime($input['expired']),
                ]);
            }
            return redirect()->route('config.voucher')->with('notify', 'Thao tác thành công.');
        }
        if ($id) {
            $this->data['voucher'] = Voucher::find($id);
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
