<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Handlers\ImageUploadHandler;
use Illuminate\Auth\Access\AuthorizationException;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['show']]);
    }

    //由于 show() 方法传参时声明了类型 —— Eloquent 模型 User，对应的变量名 $user 会匹配路由片段中的 {user}，
    //这样，Laravel 会自动注入与请求 URI 中传入的 ID 对应的用户模型实例。
    //此功能称为 『隐性路由模型绑定』，是『约定优于配置』设计范式的体现
    public function show(User $user) {
        //将用户对象变量 $user 通过 compact 方法转化为一个关联数组
        return view('users.show',compact('user'));
    }

    public function edit(User $user) {
        try {
            $this->authorize('update', $user);
        } catch (AuthorizationException $e) {
            return abort(403, '无权访问');
        }
        return view('users.edit',compact('user'));
    }

    public function update(UserRequest $request, ImageUploadHandler $uploader, User $user) {
        $oldImg = explode('/', $user->avatar)[8];
        $data = $request->all();
        if($request->avatar) {
            $result = $uploader->save($request->avatar, 'avatars', $user->id,362);
            if ($result) {
                $data['avatar'] = $result['path'];
                $oldImgPath = $result['upload_path'].'/'.$oldImg;
            }
        }
        if($user->update($data)) {
            unlink($oldImgPath);
        };
        return redirect()->route('users.show',$user->id)->with('success','个人资料更新成功');
    }
}
