<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;

class TagToTeacher extends BaseModel{
    public $timestamps = false;
    protected $table = 'tag_to_teacher';

    public static function rapydGrid($tid){
        return DB::table('teachers as tch')
            ->leftJoin('tag_to_teacher as t2t','tch.id','=','t2t.teacher_id')
            ->leftJoin('tags as t','t.id','=','t2t.tag_id')
            ->select([
                't2t.id',
                'tch.name as teacher_name',
                't.name as tag_name'
            ])
            ->where('tch.id',$tid)
            ->where('t2t.status',1);
    }
}