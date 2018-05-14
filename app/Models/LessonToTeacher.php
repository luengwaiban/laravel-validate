<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;

class LessonToTeacher extends BaseModel{
    public $timestamps = false;
    protected $table = 'lesson_to_teacher';

    public static function rapydGrid($tid){
        return DB::table('teachers as tch')
            ->leftJoin('lesson_to_teacher as l2t','tch.id','=','l2t.teacher_id')
            ->leftJoin('lessons as l','l.id','=','l2t.lesson_id')
            ->select([
                'l2t.id',
                'tch.name as teacher_name',
                'l.name as lesson_name'
            ])
            ->where('tch.id',$tid)
            ->where('l2t.status',1);
    }
}