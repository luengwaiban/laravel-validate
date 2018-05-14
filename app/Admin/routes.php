<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->get('tags','TagController@index');
    $router->any('tags/create','TagController@edit');
    $router->any('tags/edit','TagController@edit');

    $router->get('lessons','LessonController@index');
    $router->any('lessons/create','LessonController@edit');
    $router->any('lessons/edit','LessonController@edit');

    $router->get('teachers','TeacherController@index');
    $router->any('teachers/create','TeacherController@edit');
    $router->any('teachers/edit','TeacherController@edit');
    $router->get('teachers/lesson','TeacherController@lesson');
    $router->any('teachers/lesson/create','TeacherController@lessonCreate');
    $router->any('teachers/lesson/untie','TeacherController@lessonUntie');
    $router->get('teachers/tag','TeacherController@tag');
    $router->any('teachers/tag/create','TeacherController@tagCreate');
    $router->any('teachers/tag/untie','TeacherController@tagUntie');

    $router->get('comments','CommentController@index');
    $router->any('comments/edit','CommentController@edit');

});
