<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;

class Comment extends BaseModel{
    public $timestamps = false;

    public static function rapydGrid(){
        return DB::table('comments as c')
            ->leftJoin('all_users as u','c.user_id','=','u.id')
            ->leftJoin('teachers as tch','c.teacher_id','=','tch.id')
            ->leftJoin('lessons as l','c.lesson_id','=','l.id')
            ->select([
                'u.nickname as author',
                'tch.name as relate_teacher',
                'l.name as relate_lesson',
                'c.*'
            ]);
    }
}