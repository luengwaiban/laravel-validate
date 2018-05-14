<?php
namespace App\Admin\Controllers;

use App\Models\Lesson;
use Illuminate\Support\Facades\Input;
use Zofe\Rapyd\DataEdit\DataEdit;
use Zofe\Rapyd\DataFilter\DataFilter;
use Zofe\Rapyd\DataGrid\DataGrid;

class lessonController extends BaseController{

    public function index(){
        $this->route = 'lessons';
        $title = '课程管理';
        $filter = DataFilter::source(Lesson::rapydGrid());
        $filter->add('id','课程ID','text')
            ->scope(function($query,$value){
                return $value?$query->where('l.id',$value):$query;
            });
        $filter->add('status','审核状态','select')->options([''=>'全部状态']+self::$statusText)
            ->scope(function($query,$value){
                return ($value!==null&&$value!=='')?$query->where('l.status',$value):$query;
            });
        $filter->submit('筛选');
        $filter->reset('重置');
        $filter->build();

        $grid = DataGrid::source($filter);
        $grid->attributes(array("class" => "table table-bordered table-striped table-hover"));
        $grid->add('id', 'ID', false);
        $grid->add('name','课程名称',false);
        $grid->add('user_name','创建课程用户',false);
        $grid->add('status', '审核状态', false);
        $grid->add('created_at', '创建时间', true);
        $grid->add('operation','操作',false);

        $grid->orderBy('id', 'desc');
        $grid->link('/'.config('admin.route.prefix') . '/'.$this->route.'/edit', '添加', 'TR', ['class' => 'btn btn-primary']);
        $grid->row(function($row){
            $link = '/'.config('admin.route.prefix') . '/'.$this->route.'/edit?modify='.$row->data->id;
            $row->cell('operation')->value = "<a class='btn btn-primary' href='" . $link . "'>编辑</a>";
            $row->cell('operation')->value .=  $this->getStatusBtn(self::$toStatus[$row->data->status]['text'],self::$toStatus[$row->data->status]['change'],$row->data->id,self::$toStatus[$row->data->status]['color']);
            $row->cell('status')->value = self::$statusText[$row->data->status];
        });
        $grid->paginate(self::DEFAULT_PER_PAGE);
        $grid->build();
        return view('rapyd.filtergrid', compact('filter', 'grid', 'title'));
    }

    public function edit(){
        $this->route = 'lessons';
        $link = config('admin.route.prefix') . '/'.$this->route;

        if($change = Input::get('change',null) ){
            $id = Input::get('id',null);
            if($id)
                Lesson::where('id',$id)->update(['status'=>$change]);
            return redirect($link);
        }

        $edit = DataEdit::source(new Lesson());
        $edit->label('课程信息');
        $edit->link($link, '返回列表', 'TR', ['class' => 'btn btn-info']);
        $edit->add('name','课程名称','text')->rule('required');
        $edit->add('user_id','创建人','text')->placeholder('0')->attributes(['readonly'=>'true'])->insertValue('0');
        $edit->saved(function() use ($link){
            return redirect($link);
        });
        $edit->build();
        return $edit->view('rapyd.edit',compact('edit'));

    }
}