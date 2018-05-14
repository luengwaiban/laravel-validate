<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;

class Lesson extends BaseModel{
    public $timestamps = false;

    public static function rapydGrid(){
        return DB::table('lessons as l')
            ->leftJoin('all_users as u','l.user_id','=','u.id')
            ->select([
                'l.*',
                'u.nickname as user_name'
            ]);
    }
}