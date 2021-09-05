<?php

namespace App\Http\Controllers;

use App\Constants\ConfigConstants;
use App\Constants\ItemConstants;
use App\Constants\UserConstants;
use App\Models\Configuration;
use App\Models\Item;
use App\Models\User;
use App\Services\ItemServices;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Vanthao03596\HCVN\Models\Province;

class PageController extends Controller
{

    public function home()
    {
        $lastConfig = Configuration::where('key', ConfigConstants::CONFIG_HOME_POPUP_WEB)->first();
        if (!empty($lastConfig)) {
            $homePopup = json_decode($lastConfig->value, true);
            if ($homePopup['status'] == 1) {
                $this->data['popup'] = $homePopup;
            }
        }
        $this->data['provinces'] = Province::orderby('name')->get();
        return view('home', $this->data);
    }

    public function ref(Request $request, $code = "")
    {
        if (empty($code)) {
            return redirect('/');
        }
        $refUser = User::where('refcode', $code)->first();
        if (!$refUser) {
            return redirect('/');
        }
        if ($request->get('has-account') || Auth::user()) {
            $this->data['isReg'] = true;
        }
        $this->data['user'] = $refUser;
        $this->data['newUser'] = Auth::user();
        $this->data['role'] = $request->get('r');
        if ($this->data['role'] == 'member') {
            return view('register.member', $this->data);
        } else if ($this->data['role'] == 'school') {
            return view('register.school', $this->data);
        } else if ($this->data['role'] == 'teacher') {
            return view('register.teacher', $this->data);
        }
        return view('register.index', $this->data);
    }

    public function _ref(Request $request, $code = "")
    {
        $data = [];
        if (empty($code)) {
            return redirect('/');
        }
        $refUser = User::where('refcode', $code)->first();
        if (!$refUser) {
            return redirect('/');
        }
        if ($request->get('has-account') || Auth::user()) {
            $data['isReg'] = true;
        }
        $data['user'] = $refUser;
        $data['newUser'] = Auth::user();
        return view('ref', $data);
    }

    public function pdp(Request $request, $itemId)
    {
        $itemService = new ItemServices();
        $user = null;
        try {
            $data = $itemService->pdpData($itemId, $user);
            // dd($data);
            $data['breadcrumb'] = [
                [
                    'url' => $data['author']->role == 'school' ? '/schools' : '/teachers',
                    'text' => $data['author']->role == 'school' ? 'Trung Tâm' : 'Chuyên gia',
                ],
                [
                    'url' => route('classes', [
                        'role' => $data['author']->role,
                        'id' => $data['author']->id,
                    ]),
                    'text' => $data['author']->name,
                ],
                [
                    'text' => 'Khoá học',
                ]
            ];
            return view('pdp.index', $data);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function search(Request $request)
    {
        if ($request->get('a') == 'search') {
            if (!in_array($request->get('o'), ['schools', 'teachers', 'classes'])) {
                return redirect()->back()->with('notify', 'Yêu cầu không hợp lệ');
            }
            return redirect()->route($request->get('o'), [
                'a' => 'search',
                'p' => $request->get('p'),
                'd' => $request->get('d'),
            ]);
        }
        return redirect()->back()->with('notify', 'Yêu cầu không hợp lệ');
    }

    public function schools(Request $request)
    {
        $list = DB::table('users')
            ->where('users.role', UserConstants::ROLE_SCHOOL)
            ->where('users.status', UserConstants::STATUS_ACTIVE)
            ->where('users.is_test', 0)
            ->where('users.is_child', 0)
            ->groupBy('users.name', 'users.image', 'users.id')
            ->select('users.name', 'users.image', 'users.id')
            ->orderBy('users.is_hot', 'desc');
        $listSearch = clone ($list);
        $data['hasSearch'] = false;
        if ($request->get('a') == 'search') {
            $data['hasSearch'] = true;
            $province = $request->get('p');
            $district = $request->get('d');
            if ($province) {
                $listSearch = $listSearch->leftJoin('user_locations AS ul', 'ul.user_id', '=', 'users.id')
                    ->where('ul.province_code', $province);
                if ($district) {
                    $listSearch = $listSearch->where('ul.district_code', $district);
                }
            }
        }
        $listSearch = $listSearch->paginate();

        if ($listSearch->total() == 0) {
            $data['searchNotFound'] = true;
            $list = $list->paginate();
        } else {
            $list = $listSearch;
            $data['searchNotFound'] = false;
        }

        $data['provinces'] = Province::orderby('name')->get();

        $data['list'] = $list;
        $data['breadcrumb'] = [
            [
                'text' => 'Trung Tâm & Trường học'
            ]
        ];
        $data['query'] = $request->input();
        return view('list.school', $data);
    }

    public function teachers(Request $request)
    {
        $list = DB::table('users')
            ->where('users.role', UserConstants::ROLE_TEACHER)
            ->where('users.status', UserConstants::STATUS_ACTIVE)
            ->where('users.is_test', 0)
            ->where('users.is_child', 0);

        if ($request->get('a') == 'search') {
            // $province = $request->get('p');
            // $district = $request->get('d');
            // if ($province) {
            //     $list = $list->leftJoin('user_locations AS ul', 'ul.user_id', '=', 'users.id')
            //     ->where('ul.province_code', $province);
            //     if ($district) {
            //         $list = $list->where('ul.district_code', $district);
            //     }
            // }
        }

        $list = $list->groupBy('users.name', 'users.image', 'users.id')
            ->select('users.name', 'users.image', 'users.id')
            ->orderBy('users.is_hot', 'desc')
            ->paginate();

        $data['list'] = $list;
        $data['breadcrumb'] = [
            [
                'text' => 'Chuyên viên & Giảng Viên'
            ]
        ];
        $data['query'] = $request->input();
        return view('list.teacher', $data);
    }

    public function classes(Request $request, $role, $id)
    {
        $data['author'] = User::find($id);
        if (empty($data['author'])) {
            return redirect()->back()->with('notify', 'Yêu cầu không hợp lệ');
        }
        $data['classes'] = Item::where('user_id', $id)
            ->where('type', ItemConstants::TYPE_CLASS)
            ->where('status', ItemConstants::STATUS_ACTIVE)
            ->where('user_status', ItemConstants::STATUS_ACTIVE)
            ->whereNull('item_id')
            ->orderBy('is_hot', 'desc')
            ->paginate();

        $data['breadcrumb'] = [
            [
                'url' => $data['author']->role == 'school' ? '/schools' : '/teachers',
                'text' => $data['author']->role == 'school' ? 'Trung Tâm' : 'Chuyên gia',
            ],
            [
                'text' => 'Các khoá học của ' . $data['author']->name,
            ]
        ];

        return view('list.class', $data);
    }

    public function helpcenter(Request $request) {
        echo '<p>Trang đang được xây dựng.</p>';
    }
}
