<?php

namespace App\Http\Controllers;

use App\Constants\ConfigConstants;
use App\Constants\ItemConstants;
use App\Constants\NotifConstants;
use App\Constants\OrderConstants;
use App\Constants\UserConstants;
use App\Models\Category;
use App\Models\Configuration;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemResource;
use App\Models\ItemUserAction;
use App\Models\Schedule;
use App\Models\User;
use App\Models\I18nContent;
use App\Models\Notification;
use App\Models\UserLocation;
use App\Services\FileServices;
use App\Services\ItemServices;
use App\Services\UserServices;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Validator;
use Vanthao03596\HCVN\Models\Province;
use Illuminate\Support\Str;


class ClassController extends Controller
{
    public function list(Request $request)
    {
        $user = Auth::user();
        if ($request->get('action') == 'rating') {
            $itemId = $request->get('class-id', 0);
            $rating = $request->get('rating', 5);
            $comment = $request->get('comment', '');
            if ($itemId <= 0) {
                return redirect()->back()->with(['notify' => 'Khóa học không tồn tại.']);
            }
            $itemUserActionM = new ItemUserAction();
            $rs = $itemUserActionM->saveRating($itemId, $user->id, $rating, $comment);
            return redirect()->back()->with(['notify' => $rs]);
        }
        $classService = new ItemServices();
        $userService = new UserServices();
        $this->data['navText'] = __('Lớp học của tôi');
        if ($request->input('action') == 'clear') {
            return redirect()->route('class');
        }
        $courseList = $classService->itemList($request, in_array($user->role, UserConstants::$modRoles) ? null : $user->id, ItemConstants::TYPE_CLASS);

        $this->data['courseList'] = $courseList;
        if ($userService->isMod()) {
            $this->data['isSale'] = false;
            if ($user->role == UserConstants::ROLE_SALE) {
                $this->data['isSale'] = true;
            }
            return view('class.list', $this->data);
        } else {
            return view(env('TEMPLATE', '') . 'me.class_list', $this->data);
        }
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $courseService = new ItemServices();
        if ($request->input('action') == 'create') {
            $input = $request->all();
            $rs = $courseService->createItem($input, ItemConstants::TYPE_CLASS);
            if ($rs === false || $rs instanceof Validator) {
                return redirect()->back()->withErrors($rs)->withInput()->with('notify', __('Tạo lớp học thất bại! Vui lòng kiểm tra lại dữ liệu'));
            } else {
                return redirect()->route('class.edit', ['id' => $rs])->with(['tab' => 'schedule', 'notify' => __('Tạo lớp học thành công, vui lòng cập nhật lịch học.')]);
            }
        }
        $configM = new Configuration();
        $this->data['configs'] = $configM->gets([
            ConfigConstants::CONFIG_DISCOUNT,
            ConfigConstants::CONFIG_COMMISSION,
            ConfigConstants::CONFIG_COMMISSION_FOUNDATION
        ]);
        $category = Category::all();
        $this->data['categories'] = $category;
        $this->data['companyCommission'] = null;
        $this->data['isSchool'] = false;
        $this->data['navText'] = __('Tạo lớp học');
        $this->data['hasBack'] = route('class');
        $userService = new UserServices();
        if ($userService->isMod()) {
            return view('class.list', $this->data);
        } else {
            return view(env('TEMPLATE', '') . 'me.class_edit', $this->data);
        }
    }

    public function del(Request $request, $courseId)
    {
        Schedule::where('item_id', $courseId)->delete();
        Item::find($courseId)->delete();
        return redirect()->back();
    }

    public function edit(Request $request, $courseId)
    {
        $courseService = new ItemServices();
        if ($request->input('action') == 'update') {
            $input = $request->all();
            try {
                $rs = $courseService->updateItem($request, $input);
            } catch (Exception $e) {
                return redirect()->back()->with(['tab' => $input['tab'], 'notify' => $e->getMessage()]);
            }

            if ($rs === false || $rs instanceof Validator) {
                return redirect()->back()->withErrors($rs)->withInput()->with(['tab' => $input['tab'], 'notify' => __('Sửa lớp học thất bại! Vui lòng kiểm tra lại dữ liệu')]);
            } else {
                return redirect()->back()->with(['notify' => $rs, 'tab' => $input['tab']]);
            }
        }
        $courseDb =  $courseService->itemInfo($courseId);
        if (empty($courseDb)) {
            return redirect()->route('class')->with('notify', __('Lớp học không tồn tại'));
        }
        $author = User::find($courseDb['info']->user_id);
        if ($author->role == UserConstants::ROLE_SCHOOL) {
            $this->data['isSchool'] = true;
            $this->data['teachers'] = DB::table('users')
                ->leftjoin('class_teachers AS ct', function ($join) use ($courseDb) {
                    $join->on('ct.user_id', '=', 'users.id');
                    $join->on('ct.class_id', '=', DB::raw($courseDb['info']->id));
                })

                ->where('users.user_id', $courseDb['info']->user_id)
                ->where('users.role', UserConstants::ROLE_TEACHER)
                ->select('users.*', 'ct.id AS isSelected')
                ->get();
        } else {
            $this->data['isSchool'] = false;
        }
        $configM = new Configuration();
        $this->data['configs'] = $configM->gets([
            ConfigConstants::CONFIG_DISCOUNT,
            ConfigConstants::CONFIG_COMMISSION,
            ConfigConstants::CONFIG_COMMISSION_FOUNDATION
        ]);

        $this->data['companyCommission'] = json_decode($courseDb['info']->company_commission, true);
        $userLocations = UserLocation::where('user_id', $author->id)->orderby('is_head', 'desc')->get();
        $this->data['userLocations'] = $userLocations;
        $this->data['openings'] = DB::table('items')
            ->join('user_locations', 'user_locations.id', '=', 'user_location_id')
            ->where('item_id', $courseId)
            ->select('items.title', 'items.id', 'user_locations.title AS location', 'items.user_status', 'items.date_start')
            ->get();

        if (!$request->session()->get('tab') && $request->get('tab')) {
            $request->session()->flash('tab', $request->get('tab'));
        }
        if ($request->get('op')) {
            $op = Item::find($request->get('op'));
            $this->data['opening'] = $op ?? null;
            $courseDb['schedule'] = Schedule::where('item_id', $op->id)->get();
        }
        $category = Category::all();

        $this->data['categories'] = $category;
        $itemCats = ItemCategory::where('item_id', $courseId)->get();
        $this->data['itemCategories'] = [];
        foreach ($itemCats as $cat) {
            $this->data['itemCategories'][] = $cat->category_id;
        }

        $this->data['ratings'] = DB::table('item_user_actions')
            ->join('users', 'users.id', '=', 'item_user_actions.user_id')
            ->where('type', 'rating')->where('item_id', $courseId)
            ->select('users.name', 'item_user_actions.*')
            ->get();

        $this->data['students'] = DB::table('order_details')
            // ->leftJoin('participations', 'participations.')
            ->join('users', 'users.id', '=', 'order_details.user_id')
            ->where('order_details.status', OrderConstants::STATUS_DELIVERED)
            ->where('order_details.item_id', $courseId)
            ->select(
                'users.name',
                'users.id',
                'order_details.created_at',
                DB::raw('(SELECT count(*) FROM participations 
            WHERE participations.participant_user_id = users.id AND participations.item_id = order_details.item_id
            GROUP BY participations.item_id
            ) AS confirm_count'),
                DB::raw("(SELECT value FROM item_user_actions 
            WHERE item_user_actions.user_id = users.id AND item_user_actions.item_id = order_details.item_id
            and item_user_actions.type = 'cert'
            ORDER BY id DESC
            LIMIT 1
            ) AS cert")
            )
            ->get();

        $this->data['course'] = $courseDb;
        $this->data['navText'] = __('Chỉnh sửa lớp học');
        $this->data['hasBack'] = route('class');
        $this->data['courseId'] = $courseId;

        $userService = new UserServices();
        if ($userService->isMod()) {
            return view('class.edit', $this->data);
        } else {
            return view(env('TEMPLATE', '') . 'me.class_edit', $this->data);
        }
    }

    public function resourceDelete($id)
    {
        $resourceM = new ItemResource();
        $rs = $resourceM->deleteRes($id);
        return redirect()->back()->with([
            'tab' => 'resource',
            'notify' => $rs
        ]);
    }

    public function delSchedule($id)
    {
        $rs = Schedule::where('item_id', $id)->delete();
        return redirect()->back()->with([
            'tab' => 'schedule',
            'notify' => ($rs > 0)
        ]);
    }

    public function category()
    {
        $data = Category::paginate();
        $i18nModel = new I18nContent();

        // change vi->en
        foreach ($data as $row) {
            foreach (I18nContent::$supports as $locale) {
                if ($locale == I18nContent::DEFAULT) {
                    foreach (I18nContent::$categoryCols as $col => $type) {
                        $row->$col =  [I18nContent::DEFAULT => $row->$col];
                    }
                } else {
                    $item18nData = $i18nModel->i18nCategory($row->id, $locale);
                    $supportCols = array_keys(I18nContent::$categoryCols);

                    foreach ($supportCols as $col) {
                        if (empty($item18nData[$col])) {
                            $row->$col = $row->$col + [$locale => ""];
                        } else {
                            $row->$col = $row->$col + [$locale => $item18nData[$col]];
                        }
                    }
                }
            }
        }
        $this->data['categories'] = $data;
        return view('category.index', $this->data);
    }
    public function categoryEdit(Request $request, $id = null)
    {
        if ($request->get('save')) {
            foreach (I18nContent::$supports as $locale) {
                $input = $request->all();
                // dd($input);
                $category = $input["title"];
                // dd($category);
                $url = Str::slug($category[$locale]);
                $catId = $request->get('id');
                // dd($category);
                $data = [
                    'title' => $category[$locale],
                    'url' => $url,
                ];
                $i18n = new I18nContent();
                if ($catId) {
                    if ($locale != I18nContent::DEFAULT) {
                        $i18n->i18nSave($locale, 'categories', $catId, 'title', $category[$locale]);
                        $i18n->i18nSave($locale, 'categories', $catId, 'url', $url);
                    } else {
                        Category::find($catId)->update($data);
                    }
                } else {
                    if ($locale == I18nContent::DEFAULT) {
                        $id = Category::create($data)->id;
                    } else {
                        $i18n->i18nSave($locale, 'categories', $id, 'title', $category[$locale]);
                        $i18n->i18nSave($locale, 'categories', $id, 'url', $url);
                    }
                }
            }
            return redirect()->route('category')->with('notify', 'Thành công');
        }
        if ($id) {
            $data = Category::find($id);
            $i18nModel = new I18nContent();

            // change vi->en

            foreach (I18nContent::$supports as $locale) {
                if ($locale == I18nContent::DEFAULT) {
                    foreach (I18nContent::$categoryCols as $col => $type) {
                        $data->$col = [I18nContent::DEFAULT => $data->$col];
                    }
                } else {
                    $supportCols = array_keys(I18nContent::$categoryCols);
                    $item18nData = $i18nModel->i18nCategory($data->id, $locale);
                    foreach ($supportCols as $col) {
                        if (empty($item18nData[$col])) {
                            $data->$col = $data->$col + [$locale => ""];
                        } else {
                            $data->$col = $data->$col + [$locale => $item18nData[$col]];
                        }
                    }
                }
            }
            $this->data['category'] = $data;
        }
        return view('category.form', $this->data);
    }
    public function likeTouch($itemId)
    {
        $user = Auth::user();
        $item = Item::find($itemId);
        if (!$item) {
            return redirect()->back()->with('notif', 'Trang không tồn tại');
        }
        $itemUserActionM = new ItemUserAction();
        $rs = $itemUserActionM->touchFav($itemId, $user->id);
        return redirect()->back();
    }

    public function specsList(Request $request, $type)
    {
        return view('specs.list');
    }

    public function specsEdit(Request $request, $type, $specId)
    {
        return view('specs.edit');
    }

    public function specsLink(Request $request, $type, $objId)
    {
        return view('specs.links');
    }

    public function authorConfirmJoinCourse(Request $request, $itemId)
    {
        $joinUserId = $request->get('join_user');
        $firstSchedule = Schedule::where('item_id', $itemId)->first();
        $itemServ = new ItemServices();
        try {
            $itemServ->comfirmJoinCourse($request, $joinUserId, $firstSchedule->id);
        } catch (\Exception $ex) {
            return redirect()->back()->with(['tab' => 'registered', 'notify' => $ex->getMessage()]);
        }

        return redirect()->back()->with(['tab' => 'registered', 'notify' => 'Thao tác thành công']);
    }

    public function authorCert(Request $request, $itemId, $userId)
    {
        $certTemplate = ItemResource::where('item_id', $itemId)
            ->where('type', 'cert')
            ->first();
        if (!$certTemplate) {
            return redirect()->back()->with(['tab' => 'registered', 'notify' => 'Chưa có mẫu chứng chỉ']);
        }
        $user = User::find($userId);
        if (!$user) {
            return redirect()->back()->with(['tab' => 'registered', 'notify' => 'Thành viên không tồn tại']);
        }
        $item = Item::find($itemId);
        $fileServ = new FileServices();
        try {
            $certUrl = $fileServ->generateCert($certTemplate, $user, $item);
            $notifM = new Notification();
            if ($user->is_child) {
                $parent = User::find($user->user_id);
                $receiverId = $parent->id;
            } else {
                $receiverId = $user->id;
            }
            $notifM->createNotif(NotifConstants::COURSE_CERT_SENT, $receiverId, [
                'name' => $user->name,
                'class' => $item->title,
                'url' => $certUrl,
            ]);
        } catch (Exception $ex) {
            Log::error($ex);
            return redirect()->back()->with(['tab' => 'registered', 'notify' => $ex->getMessage()]);
        }
        return redirect()->back()->with(['tab' => 'registered', 'notify' => 'Thao tác thành công']);
    }
}
