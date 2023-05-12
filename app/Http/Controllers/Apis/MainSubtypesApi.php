<?php

namespace App\Http\Controllers\APIs;

use App\Constants\ItemConstants;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Services\CommonServices;
use App\Services\J4uServices;
use App\Services\VoucherServices;
use Illuminate\Support\Facades\DB;

class MainSubtypesApi extends Controller
{
    public function index(Request $request, $subtype, $role='guest') 
    { 
        if (in_array($subtype, $this->getSubtypes())) {
            $commonS = new CommonServices();
            $data['schools'] = $this->getSchools($subtype);
            $data['partners'] = $this->getPartners($subtype);
            $data['partnerItems'] = $this->getItemsByPartners($subtype);
            $data['categoryItems'] = $this->getItemsByCategories($subtype);
            $data['vouchers'] = (new VoucherServices)->getVoucherEventsBySubtype($subtype);
            
            if ($role == 'member') {
                $user = $request->get('_user'); 
            
                $data['j4u'] = $commonS->setTemplate('/', 'Có thể bạn quan tâm', (new J4uServices)->get($user, $subtype));
                $data['repurchaseds'] = $commonS->setTemplate('/', 'Đăng ký lại', $commonS->getRepurchasedsBySubtype($user, $subtype));           
            }

            return $data;
        }
    }

    public function getsubtypes()
    {
        return Item::select('subtype')
            ->distinct('subtype')
            ->pluck('subtype')
            ->toArray();
    }

    public function getSchools($subtype) 
    {
        return DB::table('items')
            ->join('users', 'items.user_id', '=', 'users.id')
            ->where('items.subtype', $subtype)
            ->where('users.role', 'school')
            ->distinct('users.id')
            ->orderByRaw('users.is_hot desc, users.boost_score desc')
            ->take(5)
            ->pluck('name');
    }

    public function getPartners($subtype) 
    {
        return DB::table('users')
            ->join('items', 'items.user_id', 'users.id')
            ->whereIn('role', ['teacher', 'school'])  
            ->where('items.subtype', $subtype)
            ->select(
                'users.id', 
                'name', 
                'users.image'
            )
            ->orderByRaw('users.is_hot desc, users.boost_score desc')
            ->distinct('users.id')
            ->take(6)
            ->get();
    }

    public function getItemsByPartners($subtype) 
    {
        $data = [];
        $commonS = new CommonServices();

        foreach($this->getPartners($subtype) as $pt) {
            $items = DB::table('items')
                ->join('items_categories as ic', 'ic.item_id', '=', 'items.id')
                ->join('categories', 'categories.id', '=', 'ic.category_id')
                ->leftjoin(
                    DB::raw('(select item_id, avg(value) as rating from item_user_actions where type = "rating" group by(item_id)) as rv'), 
                    'rv.item_id',
                    'items.id'
                )
                ->where('items.status', ItemConstants::STATUS_ACTIVE)
                ->where('items.user_status', ItemConstants::USERSTATUS_ACTIVE)
                ->where('items.user_id', $pt->id)
                ->where('items.subtype', $subtype)
                ->select(
                    'items.id',
                    'items.title',
                    'items.image',
                    'items.price',
                    'items.is_hot',
                    'rv.rating',
                    DB::raw('group_concat(categories.title) as categories')
                )
                ->orderByRaw('items.is_hot desc, items.boost_score desc')
                ->groupBy('items.id')
                ->take(5)
                ->get();

            $data[] = $commonS->setTemplate('/', 'Các lớp học của '. $pt->name, $items);
        }

        return $data;   
    }

    public function getCategoriesBySubtype($subtype)
    {
        return DB::table('items')
            ->join('items_categories as ic', 'ic.item_id', '=', 'items.id')
            ->join('categories', 'categories.id', '=', 'ic.category_id')
            ->where('items.subtype', $subtype)
            ->select(
                'categories.id',
                'categories.title'
            )
            ->distinct('categories.id')
            ->groupBy('items.id')
            ->get();
    }

    public function getItemsByCategories($subtype) 
    {
        $data = [];
        $commonS = new CommonServices();

        foreach($this->getCategoriesBySubtype($subtype) as $ct) {
            $items = DB::table('items')
                ->join('items_categories as ic', 'ic.item_id', '=', 'items.id')
                ->join('categories', 'categories.id', '=', 'ic.category_id')
                ->leftjoin(
                    DB::raw('(select item_id, avg(value) as rating from item_user_actions where type = "rating" group by(item_id)) as rv'), 
                    'rv.item_id',
                    'items.id'
                )
                ->where('items.status', ItemConstants::STATUS_ACTIVE)
                ->where('items.user_status', ItemConstants::USERSTATUS_ACTIVE)
                ->where('subtype', $subtype)
                ->where('ic.category_id', $ct->id)
                ->select(
                    'items.id',
                    'items.title',
                    'items.image',
                    'items.price',
                    'items.is_hot',
                    'rv.rating',
                    DB::raw('group_concat(categories.title) as categories')
                )
                ->orderByRaw('items.is_hot desc, items.boost_score desc')
                ->groupBy('items.id')
                ->take(5)
                ->get();

            $data[] = $commonS->setTemplate('/', 'Các lớp học của '. $ct->title, $items);  
        }

        return $data;
    }

}
