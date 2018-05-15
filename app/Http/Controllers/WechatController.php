<?php
/**
 * Created by PhpStorm.
 * User: liangweibin
 * Date: 18/5/14
 * Time: 下午4:32
 */
namespace App\Http\Controllers;

class WechatController extends BaseController{

    public function serve(){
        $app = app('wechat.official_account.lwb_test');
        $app->server->push(function($message){
            return var_export($message,true);
        });
        return $app->server->serve();
    }

    public function user(){
        $user = session('wechat.oauth_user.default');
        var_dump($user);
    }
}