<?php

namespace App\Admin\Controllers;

use App\Models\Lesson;
use App\Models\LessonToTeacher;
use App\Models\Tag;
use App\Models\TagToTeacher;
use App\Models\Teacher;
use Illuminate\Support\Facades\Input;
use Zofe\Rapyd\DataEdit\DataEdit;
use Zofe\Rapyd\DataFilter\DataFilter;
use Zofe\Rapyd\DataForm\DataForm;
use Zofe\Rapyd\DataGrid\DataGrid;

class TeacherController extends BaseController{

    public function index(){
        $this->route = 'teachers';
        $title = '教师管理';
        $filter = DataFilter::source(Teacher::rapydGrid());
        $filter->add('id','教师ID','text')
            ->scope(function($query,$value){
                return $value?$query->where('tch.id',$value):$query;
            });
        $filter->add('status','审核状态','select')->options([''=>'全部状态']+self::$statusText)
            ->scope(function($query,$value){
                return ($value!==null&&$value!=='')?$query->where('tch.status',$value):$query;
            });
        $filter->submit('筛选');
        $filter->reset('重置');
        $filter->build();

        $grid = DataGrid::source($filter);
        $grid->attributes(array("class" => "table table-bordered table-striped table-hover"));
        $grid->add('id', 'ID', false);
        $grid->add('name','教师名称',false);
        $grid->add('thumb','教师头像',false);
        $grid->add('lesson_name','任教课程',false);
        $grid->add('description','教师简介',false);
        $grid->add('tag_name','教师标签',false);
        $grid->add('creator_name','创建人',false);
        $grid->add('created_at','创建时间',false);
        $grid->add('status', '审核状态', false);
        $grid->add('operation','操作',false);

        $grid->orderBy('id', 'desc');
        $grid->link('/'.config('admin.route.prefix') . '/'.$this->route.'/edit', '添加', 'TR', ['class' => 'btn btn-primary']);
        $grid->row(function($row){
            $link = '/'.config('admin.route.prefix') . '/'.$this->route.'/edit?modify='.$row->data->id;
            $lessonLink = '/'.config('admin.route.prefix') . '/'.$this->route.'/lesson?tid='.$row->data->id;
            $tagLink = '/'.config('admin.route.prefix') . '/'.$this->route.'/tag?tid='.$row->data->id;
            $row->cell('operation')->value .= $this->getFrameBtn($lessonLink,['btn_text'=>'课程管理','btn_class'=>'btn btn-warning'],true,1080,800);
            $row->cell('operation')->value .= $this->getFrameBtn($tagLink,['btn_text'=>'标签管理','btn_class'=>'btn btn-info'],true,1080,800);
            $row->cell('operation')->value .= "<a class='btn btn-primary' href='" . $link . "'>编辑</a> ";
            $row->cell('operation')->value .=  $this->getStatusBtn(self::$toStatus[$row->data->status]['text'],self::$toStatus[$row->data->status]['change'],$row->data->id,self::$toStatus[$row->data->status]['color']);
            $row->cell('status')->value = self::$statusText[$row->data->status];
            $row->cell('thumb')->value = "<img src='/".config('validate.upload.teacherImg')."{$row->data->thumb}' width='100px' height='100px'>";
        });
        $grid->paginate(self::DEFAULT_PER_PAGE);
        $grid->build();
        return view('rapyd.filtergrid', compact('filter', 'grid', 'title'));
    }

    public function edit(){
        $this->route = 'teachers';
        $link = config('admin.route.prefix') . '/'.$this->route;

        if($change = Input::get('change',null) ){
            $id = Input::get('id',null);
            if($id)
                Teacher::where('id',$id)->update(['status'=>$change]);
            return redirect($link);
        }

        $edit = DataEdit::source(new Teacher());
        $edit->label('教师信息');
        $edit->link($link, '返回列表', 'TR', ['class' => 'btn btn-info']);
        $edit->add('thumb','教师头像','image')
            ->move(config('validate.upload.teacherImg'))
            ->preview(320,240)
            ->rule('required');
        $edit->add('name','教师名称','text')->rule('required');
        $edit->add('description','教师简介','textarea')->rule('required');
        $edit->add('user_id','创建人','text')->placeholder('0')->attributes(['readonly'=>'true'])->insertValue('0');
        $edit->saved(function() use ($link){
            return redirect($link);
        });
        $edit->build();
        return $edit->view('rapyd.edit',compact('edit'));

    }

    public function lesson(){
        $this->route = 'teachers/lesson';
        $title = '任教课程管理';
        $tid = Input::get('tid',0);
        $grid = DataGrid::source(LessonToTeacher::rapydGrid($tid));
        $grid->attributes(array("class" => "table table-bordered table-striped table-hover"));
        $grid->add('id', 'ID', false);
        $grid->add('teacher_name','教师名称',false);
        $grid->add('lesson_name','任教课程',false);
        $grid->add('operation','操作',false);

        $grid->orderBy('id', 'desc');
        $grid->link('/'.config('admin.route.prefix') . '/'.$this->route.'/create?tch_id='.$tid, '添加关系', 'TR', ['class' => 'btn btn-primary']);

        $grid->row(function($row){
            $untieLink = '/'.config('admin.route.prefix') . '/'.$this->route.'/untie?lesson_untie='.$row->data->id;
            $row->cell('operation')->value =  '<button class="btn btn-info" onclick="layer.confirm( \'确定要解除关系吗？！\',{ btn: [\'确定\',\'取消\'] }, function(){ window.location.href = \''.$untieLink.'\'})">解除关系</button>';
        });
        $grid->paginate(self::DEFAULT_PER_PAGE);
        $grid->build();
        return view('rapyd.styledgrid', compact( 'grid', 'title'));
    }

    public function lessonUntie(){
        $untieId = Input::get('lesson_untie',0);
        if($untieId){
            LessonToTeacher::where('id',$untieId)->update(['status'=>-1]);
        }
        return redirect()->back();
    }

    public function lessonCreate(){
        $this->route = 'teachers/lesson';
        $teacher_id = Input::get('tch_id',0);
        if(!$teacher_id) return redirect()->back();
        $form = DataForm::source(new LessonToTeacher());
        $form->label('添加关系');
        $form->add('teacher_id','教师id','text')->attributes(['readonly'=>true])->insertValue($teacher_id);
        $form->add('lesson_id','任教课程','select')->options(Lesson::where('status',1)->pluck('name','id')->toArray());
        $form->saved(function() use ($form,$teacher_id){
            return redirect('/'.config('admin.route.prefix') . '/'.$this->route.'?tid='.$teacher_id);
        });
        $form->submit('保存');
        $form->build();


        return $form->view('rapyd.relateform',compact('form'));
    }

    public function tag(){
        $this->route = 'teachers/tag';
        $title = '教师标签管理';
        $tid = Input::get('tid',0);
        $grid = DataGrid::source(TagToTeacher::rapydGrid($tid));
        $grid->attributes(array("class" => "table table-bordered table-striped table-hover"));
        $grid->add('id', 'ID', false);
        $grid->add('teacher_name','教师名称',false);
        $grid->add('tag_name','标签',false);
        $grid->add('operation','操作',false);

        $grid->orderBy('id', 'desc');
        $grid->link('/'.config('admin.route.prefix') . '/'.$this->route.'/create?tch_id='.$tid, '添加关系', 'TR', ['class' => 'btn btn-primary']);

        $grid->row(function($row){
            $untieLink = '/'.config('admin.route.prefix') . '/'.$this->route.'/untie?tag_untie='.$row->data->id;
            $row->cell('operation')->value =  '<button class="btn btn-info" onclick="layer.confirm( \'确定要解除关系吗？！\',{ btn: [\'确定\',\'取消\'] }, function(){ window.location.href = \''.$untieLink.'\'})">解除关系</button>';
        });
        $grid->paginate(self::DEFAULT_PER_PAGE);
        $grid->build();
        return view('rapyd.styledgrid', compact( 'grid', 'title'));
    }

    public function tagUntie(){
        $untieId = Input::get('tag_untie',0);
        if($untieId){
            TagToTeacher::where('id',$untieId)->update(['status'=>-1]);
        }
        return redirect()->back();
    }

    public function tagCreate(){
        $this->route = 'teachers/tag';
        $teacher_id = Input::get('tch_id',0);
        if(!$teacher_id) return redirect()->back();
        $form = DataForm::source(new TagToTeacher());
        $form->label('添加关系');
        $form->add('teacher_id','教师id','text')->attributes(['readonly'=>true])->insertValue($teacher_id);
        $form->add('tag_id','教师标签','select')->options(Tag::where('status',1)->pluck('name','id')->toArray());
        $form->saved(function() use ($form,$teacher_id){
            return redirect('/'.config('admin.route.prefix') . '/'.$this->route.'?tid='.$teacher_id);
        });
        $form->submit('保存');
        $form->build();


        return $form->view('rapyd.relateform',compact('form'));
    }


}