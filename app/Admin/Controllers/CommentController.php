<?php
namespace App\Admin\Controllers;

use App\Models\Comment;
use Illuminate\Support\Facades\Input;
use Zofe\Rapyd\DataFilter\DataFilter;
use Zofe\Rapyd\DataGrid\DataGrid;

class CommentController extends BaseController{

    public function index(){
        $this->route = 'comments';
        $title = '评论审核';
        $filter = DataFilter::source(Comment::rapydGrid());
        $filter->add('id','评论ID','text')
            ->scope(function($query,$value){
                return $value?$query->where('c.id',$value):$query;
            });
        $filter->add('status','审核状态','select')->options([''=>'全部状态']+self::$statusText)
            ->scope(function($query,$value){
                return ($value!==null&&$value!=='')?$query->where('c.status',$value):$query;
            });
        $filter->submit('筛选');
        $filter->reset('重置');
        $filter->build();

        $grid = DataGrid::source($filter);
        $grid->attributes(array("class" => "table table-bordered table-striped table-hover"));
        $grid->add('id', 'ID', false);
        $grid->add('author','作者',false);
        $grid->add('relate_teacher','相关教师',false);
        $grid->add('relate_lesson','相关课程',false);
        $grid->add('content','评论内容',false);
        $grid->add('img_path','评论图片',false);
        $grid->add('quality_score','课堂质量',false);
        $grid->add('attitude_score','教学态度',false);
        $grid->add('pass_score','考核难易',false);
        $grid->add('like','赞数',false);
        $grid->add('unlike','踩数',false);
        $grid->add('status', '审核状态', false);
        $grid->add('updated_at', '编辑时间', true);
        $grid->add('operation','操作',false);

        $grid->orderBy('id', 'desc');
        $grid->row(function($row){
            $row->cell('operation')->value .=  $this->getStatusBtn(self::$toStatus[$row->data->status]['text'],self::$toStatus[$row->data->status]['change'],$row->data->id,self::$toStatus[$row->data->status]['color']);
            $row->cell('status')->value = self::$statusText[$row->data->status];
            $row->cell('img_path')->value = "<img src='/".config('validate.upload.commentImg').$row->data->img_path."' width='100' height='100'>";
        });
        $grid->paginate(self::DEFAULT_PER_PAGE);
        $grid->build();
        return view('rapyd.filtergrid', compact('filter', 'grid', 'title'));
    }

    public function edit(){
        $this->route = 'comments';
        $link = config('admin.route.prefix') . '/'.$this->route;

        if($change = Input::get('change',null) ){
            $id = Input::get('id',null);
            if($id)
                Comment::where('id',$id)->update(['status'=>$change]);
            return redirect($link);
        }

    }
}