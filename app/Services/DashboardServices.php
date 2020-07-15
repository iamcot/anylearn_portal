<?php

namespace App\Services;

use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardServices
{
    public function userCount($role = null)
    {
        if (!$role) {
            return User::count();
        }
        return User::where('role', $role)->count();
    }
    public function itemCount()
    {
        return Item::count();
    }

    public function userCreatedByWeek() {
        $last3m = date('Ym', strtotime('-3 month'));
        
        $sql = "SELECT  yearweek(created_at) AS week, count(*) AS num FROM anylearn.users
        where created_at is not null
        and yearweek(created_at) >= ?
        group by yearweek(created_at);
        ";

        $results = DB::select($sql, [$last3m]);
        $chartDataset = [
            'labels' => [],
            'data' => []
        ];
        foreach($results as $row) {
            $year = implode('', array_slice(str_split($row->week), 0, 4));
            $week = implode('', array_slice(str_split($row->week), 4, 2));
            $chartDataset['labels'][] = date( 'd/m', strtotime($year . 'W' . $week . " +6 days" ));
            $chartDataset['data'][] = $row->num;
        }
        return $chartDataset;
    }

    public function topUser($num = 5) {
        $sql = "SELECT users.name, count(od.id) AS reg_num
        FROM users 
        JOIN items ON items.user_id = users.id
        LEFT JOIN order_details AS od on od.item_id = items.id
        where users.role in ('school', 'teacher')
        GROUP BY users.name
        ORDER BY reg_num DESC
        LIMIT ? 
        ";
        $results = DB::select($sql, [$num]);
        return $results;
    }

    public function topItem($num = 5) {
        $sql = "SELECT items.title, count(od.id) AS reg_num
        FROM items
        LEFT JOIN order_details AS od on od.item_id = items.id
        GROUP BY items.title
        ORDER BY reg_num DESC
        LIMIT ? 
        ";
        $results = DB::select($sql, [$num]);
        return $results;
    }
}
