<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;

class BaseController extends Controller {
    const DEFAULT_PER_PAGE = 30;
    public $route = '';


    const STATUS_NOT_YET = 0;
    const STATUS_FAILED = -1;
    const STATUS_SUCCESS = 1;

    static $statusText = [
        self::STATUS_SUCCESS=>'审核成功',
        self::STATUS_FAILED=>'审核失败',
        self::STATUS_NOT_YET=>'未审核',
    ];

    static $toStatus = [
        self::STATUS_SUCCESS=>['change'=>self::STATUS_FAILED,'text'=>'不通过','color'=>'btn-danger'],
        self::STATUS_FAILED=>['change'=>self::STATUS_SUCCESS,'text'=>'通过','color'=>'btn-success'],
        self::STATUS_NOT_YET=>['change'=>self::STATUS_SUCCESS,'text'=>'通过','color'=>'btn-success']
    ];

    public function getStatusBtn($statusText,$changeStatus,$id,$btnColor){
        return '<button class="btn  '.$btnColor.'" onclick="layer.confirm( \'确定将状态改为' . $statusText . '吗？！\',{ btn: [\'确定\',\'取消\'] }, function(){ window.location.href = \'/' .config('admin.route.prefix') .'/'. $this->route . "/edit?change=" . $changeStatus . "&id=" . $id . '\'})">' . $statusText . '</button>';
    }

    public function getFrameBtn($link,$options = [],$refresh = false,$width = 1360,$height = 900)
    {
        $btnText = '详情';
        isset($options['btn_text']) && $btnText = $options['btn_text'];

        $btnClass = '';
        isset($options['btn_class']) && $btnClass = $options['btn_class'];

        if($refresh){
            $endFresh = "window.location.reload();
                            return false; ";
        }else{
            $endFresh = '';
        }
        $btn = "<a style='cursor:pointer' class=\"" . $btnClass . "\" onclick=\"layer.open({
                                                                                type: 2, 
                                                                                title: ['', false], 
                                                                                area: ['{$width}px', '{$height}px'], 
                                                                                shadeClose: true,
                                                                                scrollbar: false,
                                                                                content: '" . $link . "',
                                                                                end: function(index, layero){".
            $endFresh.
            "}".
            "})\">" . $btnText . "</a>";

        return $btn;
    }
}