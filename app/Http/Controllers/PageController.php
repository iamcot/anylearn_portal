<?php

namespace App\Http\Controllers;

use App\Constants\ConfigConstants;
use App\Constants\ItemConstants;
use App\Constants\UserConstants;
use App\Models\Article;
use App\Models\Category;
use App\Models\Configuration;
use App\Models\Item;
use App\Models\ItemUserAction;
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
        $quotes = config('quotes', []);
        $quote = $quotes[mt_rand(0, count($quotes) - 1)];
        $this->data['quote'] = $quote;
        $this->data['provinces'] = Province::orderby('name')->get();
        $this->data['promotions'] = Article::where('type', Article::TYPE_PROMOTION)
            ->where('status', 1)->orderby('id', 'desc')->take(5)->get();
        $this->data['events'] = Article::where('type', Article::TYPE_EVENT)
            ->where('status', 1)->orderby('id', 'desc')->take(5)->get();
        $this->data['articles'] = Article::whereIn('type', [Article::TYPE_READ, Article::TYPE_VIDEO])
            ->where('status', 1)->orderby('id', 'desc')->take(5)->get();
        $homeClassesDb = Configuration::where('key', ConfigConstants::CONFIG_HOME_SPECIALS_CLASSES)->first();
        $homeClasses = [];
        if ($homeClassesDb) {
            foreach (json_decode($homeClassesDb->value, true) as $block) {
                if (empty($block)) {
                    continue;
                }
                $items = Item::whereIn('id', explode(",", $block['classes']))
                    ->where('status', 1)
                    ->where('user_status', 1)
                    ->get();
                $homeClasses[] = [
                    'title' => $block['title'],
                    'classes' => $items
                ];
            }
        }
        $this->data['classes'] = $homeClasses;

        return view(env('TEMPLATE', '') . 'home', $this->data);
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
        $user = Auth::user();
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
            return view(env('TEMPLATE', '') . 'pdp.index', $data);
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
            $searchType = $request->get('t');
            $searchCategory = $request->get('c');
            if ($province) {
                $listSearch = $listSearch->leftJoin('user_locations AS ul', 'ul.user_id', '=', 'users.id')
                    ->where('ul.province_code', $province);
                if ($district) {
                    $listSearch = $listSearch->where('ul.district_code', $district);
                }
            }
            if ($searchType) {
                $listSearch  = $listSearch->join(DB::raw("(SELECT user_id, count(*) AS numrow FROM items WHERE subtype = '" . preg_replace('/[^a-z]/', '', $searchType) . "' group by user_id ) AS t1"), function ($query) {
                    $query->on("t1.user_id", "=", "users.id")
                        ->where('t1.numrow', ">", 0);
                });
            }
            if ($searchCategory) {
                $listSearch  = $listSearch->join(DB::raw("(SELECT user_id, count(*) AS numrow FROM items JOIN items_categories AS ic ON ic.item_id = items.id  WHERE ic.category_id = '" . preg_replace('/[^0-9]/', '', $searchCategory) . "' group by user_id ) AS t2"), function ($query) {
                    $query->on("t2.user_id", "=", "users.id")
                        ->where('t2.numrow', ">", 0);
                });
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

        $data['listPaginate'] = $list->appends($request->query())->links();
        $data['list'] = [];
        foreach ($list as $user) {
            $userCategories = DB::table('items')
                ->join('items_categories', 'items_categories.item_id', '=', 'items.id')
                ->join('categories', 'categories.id', '=', 'items_categories.category_id')
                ->where('items.user_id', $user->id)
                ->groupBy('categories.id', 'categories.url', 'categories.title')
                ->select('categories.id', 'categories.url', 'categories.title')
                ->take(4)
                ->get();
            $user->categories = $userCategories;
            $data['list'][] = $user;
        }

        $data['breadcrumb'] = [
            [
                'text' => 'Trung Tâm & Trường học'
            ]
        ];
        $data['categories'] = Category::all();
        $data['query'] = $request->input();
        return view(env('TEMPLATE', '') . 'list.school', $data);
    }

    public function teachers(Request $request)
    {
        $list = DB::table('users')
            ->where('users.role', UserConstants::ROLE_TEACHER)
            ->where('users.status', UserConstants::STATUS_ACTIVE)
            ->where('users.is_test', 0)
            ->where('users.is_child', 0)
            ->groupBy('users.name', 'users.image', 'users.id')
            ->select('users.name', 'users.image', 'users.id')
            ->orderBy('users.is_hot', 'desc');

        $data['hasSearch'] = false;
        $listSearch = clone ($list);
        if ($request->get('a') == 'search') {
            $data['hasSearch'] = true;
            $searchType = $request->get('t');
            $searchCategory = $request->get('c');
            if ($searchType) {
                $listSearch  = $listSearch->join(DB::raw("(SELECT user_id, count(*) AS numrow FROM items WHERE subtype = '" . preg_replace('/[^a-z]/', '', $searchType) . "' group by user_id ) AS t1"), function ($query) {
                    $query->on("t1.user_id", "=", "users.id")
                        ->where('t1.numrow', ">", 0);
                });
            }
            if ($searchCategory) {
                $listSearch  = $listSearch->join(DB::raw("(SELECT user_id, count(*) AS numrow FROM items JOIN items_categories AS ic ON ic.item_id = items.id  WHERE ic.category_id = '" . preg_replace('/[^0-9]/', '', $searchCategory) . "' group by user_id ) AS t2"), function ($query) {
                    $query->on("t2.user_id", "=", "users.id")
                        ->where('t2.numrow', ">", 0);
                });
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

        $data['listPaginate'] = $list->appends($request->query())->links();
        $data['list'] = [];
        foreach ($list as $user) {
            $userCategories = DB::table('items')
                ->join('items_categories', 'items_categories.item_id', '=', 'items.id')
                ->join('categories', 'categories.id', '=', 'items_categories.category_id')
                ->where('items.user_id', $user->id)
                ->groupBy('categories.id', 'categories.url', 'categories.title')
                ->select('categories.id', 'categories.url', 'categories.title')
                ->take(4)
                ->get();
            $user->categories = $userCategories;
            $data['list'][] = $user;
        }

        $data['breadcrumb'] = [
            [
                'text' => 'Chuyên viên & Giảng Viên'
            ]
        ];
        $data['categories'] = Category::all();
        $data['query'] = $request->input();
        return view(env('TEMPLATE', '') . 'list.teacher', $data);
    }

    public function classes(Request $request, $role = null, $id = null)
    {
        $classes = DB::table('items')
            ->where('type', ItemConstants::TYPE_CLASS)
            ->where('status', ItemConstants::STATUS_ACTIVE)
            ->where('user_status', ItemConstants::STATUS_ACTIVE)
            ->whereNull('item_id')
            // ->join('items_categories', 'items_categories.item_id', '=', 'items.id')
            // ->join('categories', 'categories.id', '=', 'items_categories.category_id')
            // ->select('items.*', 'categories.id AS category_id', 'categories.url AS category_url', 'categories.title AS category_title')
            ->select('items.*')
            ->orderBy('is_hot', 'desc')
            ->orderBy('id', 'desc');

        if ($id) {
            $data['author'] = User::find($id);
            if (empty($data['author'])) {
                return redirect()->back()->with('notify', 'Yêu cầu không hợp lệ');
            }
            $classes = $classes->where('user_id', $id);
            $data['breadcrumb'] = [
                [
                    'url' => $data['author']->role == 'school' ? '/schools' : '/teachers',
                    'text' => $data['author']->role == 'school' ? 'Trung Tâm' : 'Chuyên gia',
                ],
                [
                    'text' => 'Các khoá học của ' . $data['author']->name,
                ]
            ];
        } else {
            $data['breadcrumb'] = [
                [
                    'text' => 'Các khoá học đang mở',
                ]
            ];
        }
        $data['hasSearch'] = false;
        $listSearch = clone ($classes);
        if ($request->get('a') == 'search') {
            $data['hasSearch'] = true;
            $searchTitle = $request->get('s');
            $searchType = $request->get('t');
            $searchCategory = $request->get('c');
            $searchPrice = $request->get('price');
            if ($searchTitle) {
                $listSearch  = $listSearch->where('title', 'LIKE', "%$searchTitle%");
            }
            if ($searchPrice) {
                $listSearch  = $listSearch->where('price', '<=', $searchPrice);
            }
            if ($searchType) {
                $listSearch  = $listSearch->where('subtype', $searchType);
            }
            if ($searchCategory) {
                $listSearch  = $listSearch->join('items_categories AS ic', function ($query) use ($searchCategory) {
                    $query->on('ic.item_id', '=', 'items.id')
                        ->where('ic.category_id', $searchCategory);
                });
            }
        }
        $listSearch = $listSearch->paginate();

        if ($listSearch->total() == 0) {
            $data['searchNotFound'] = true;
            $classes = $classes->paginate();
        } else {
            $classes = $listSearch;
            $data['searchNotFound'] = false;
        }

        // $data['classes'] = $classes;
        $data['classesPaginate'] = $classes->appends($request->query())->links();
        $data['classes'] = [];
        $itemUserActionM = new ItemUserAction();
        foreach ($classes as $class) {
            
            $class->rating = $itemUserActionM->rating($class->id);
            $data['classes'][] = $class;
        }

        $data['categories'] = Category::all();
        return view(env('TEMPLATE', '') . 'list.class', $data);
    }

    public function helpcenter(Request $request)
    {
        echo '<p>Trang đang được xây dựng.</p>';
    }
}
