<?php

use App\User;
use App\Content;
use App\Comment;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//! USUARIOS
//* Método POST /cadastro
Route::post('/cadastro', "UserController@cadastro" );

//* Método POST /login
Route::post('/login', "UserController@login" );

//* Método PUT /perfil
Route::middleware('auth:api')->put('/perfil', "UserController@perfil");

//* Método GET /users
Route::get('/users', "UserController@users");

//* Método GET /user
Route::middleware('auth:api')->get('/user', "UserController@user");

//! CONTEUDO
//* Método POST adicionar conteúdo
Route::middleware('auth:api')->post('/content/add', "ContentController@add");
//*
Route::middleware('auth:api')->get('/content/list', "ContentController@list");
//*
Route::middleware('auth:api')->put('/content/like/{id}', "ContentController@like");
Route::middleware('auth:api')->put('/content/likePage/{id}', "ContentController@likePage");
Route::middleware('auth:api')->put('/content/comment/{id}', "ContentController@comment");
Route::middleware('auth:api')->put('/content/commentPage/{id}', "ContentController@commentPage");
//* Pagina
Route::middleware('auth:api')->get('/content/page/list/{id}', "ContentController@page");
//* Amigo
Route::middleware('auth:api')->post('/user/friend', "UserController@friend");
Route::middleware('auth:api')->get('/user/listFriends', "UserController@listFriends");
Route::middleware('auth:api')->get('/user/listFriendsPage', "UserController@listFriendsPage");

Route::get('/testes', function(){
    $user = User::find(1);
    $user2 = User::find(2);

    // $contents = Content::all();
    // foreach ($contents as $key => $value) {
    //     $value->delete();
    // }

    //? Conteudo
    // $user->contents()->create([
    //     'title' => 'Conteudo 3',
    //     'text' => 'Aqui texto',
    //     'image' => 'url',
    //     'link' => 'link dado',
    //     'date' => date('2008-09-09')
    // ]);

    //return $user->contents;

    //? Amigos
    // $user->friends()->attach($user2->id);
    // $user->friends()->detach($user2->id);
    // $user->friends()->toggle($user2->id);
    // return $user->friends;

    //? Likes
    $content = Content::find(23);
    // $user->likes()->toggle($content->id);

    // return $content->likes()->count();

    //? Comentários
    // $user->comments()->create([
    //     'content_id' => $content->id,
    //     'text' => 'Aqui texto',
    //     'date' => date('Y-m-d')
    // ]);
    // $user2->comments()->create([
    //     'content_id' => $content->id,
    //     'text' => 'Asmei',
    //     'date' => date('Y-m-d H:i:s')
    // ]);

    // return $content->comments;
    //return Content::all();
});
