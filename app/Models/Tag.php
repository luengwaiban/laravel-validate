<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;

class Tag extends BaseModel{

    public $timestamps = false;
    public $table = 'tags';


    public static function rapydGrid(){
        return DB::table('tags as t')
            ->leftJoin('all_users as u','t.user_id','=','u.id')
            ->select([
                't.*',
                'u.nickname as user_name'
            ]);
    }
}