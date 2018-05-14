<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;

class Teacher extends BaseModel{
    public $timestamps = false;

    public static function rapydGrid(){
          $a =  DB::table('teachers as tch')
            ->leftJoin('lesson_to_teacher as l2t',function($join){
                $join->on('l2t.teacher_id','=','tch.id')
                ->where('l2t.status',1);
            })
            ->leftJoin('lessons as l','l2t.lesson_id','=','l.id')
            ->leftJoin('all_users as u','u.id','=','tch.user_id')
            ->leftJoin('tag_to_teacher as t2t','t2t.teacher_id','=','tch.id')
            ->leftJoin('tags as t',function($join){
                $join->on('t.id','=','t2t.tag_id')->where('t2t.status',1);
            })
            ->select([
                'tch.*',
                DB::raw('GROUP_CONCAT(distinct l.name) as lesson_name'),
                'u.nickname as creator_name',
                DB::raw('GROUP_CONCAT(distinct t.name) as tag_name'),
            ])
            ->groupBy('tch.id')
          ->where('id',10);
          dd($a->toSql());
    }
}