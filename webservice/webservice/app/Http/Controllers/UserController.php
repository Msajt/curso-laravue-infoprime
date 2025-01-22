<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User; // Importando o model User
use Auth;
use Illuminate\Support\Facades\Validator; // Validar as entradas feitas pelo usuário
use Illuminate\Validation\Rule; // Regras de validação

class UserController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->all(); // Recebe os dados do request JSON
        $validateUser = Validator::make($data, [ // Validar entradas do usuário
            'email' => 'required|string|email|max:255',
            'password' => 'required|string',
        ]);

        // Verificar se está tudo ok
        if ($validateUser->fails()) {
            return ['status' => false, "validation" => true, "validationErrors" => $validateUser->errors()];
        }

        // Tentando fazer o login do usuário
        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            $user = auth()->user();
            $user->token = $user->createToken($user->email)->accessToken; // Token de autenticação
            //$user->image = asset($user->image);
            return ['status' => true, 'user' => $user];
        } else {
            return ['status' => false];
        }
    }

    public function cadastro(Request $request)
    {
        $data = $request->all(); // Recebe os dados do request JSON
        $validateUser = Validator::make($data, [ // Validar entradas do usuário
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Verificar se está tudo ok
        if ($validateUser->fails()) {
            return ['status' => false, "validation" => true, "validationErrors" => $validateUser->errors()];
        }

        $user = User::create([ // Criação do usuário
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);
        $user->token = $user->createToken($user->email)->accessToken; // Token de autenticação

        return ['status' => true, "user" => $user];
    }

    public function users()
    {
        $users = User::all();
        return $users;
    }

    public function user(Request $request) {
        return $request->user();
    }

    public function perfil(Request $request) {
        $user = $request->user();
        $data = $request->all();
    
        if (isset($data['password'])) {
            $validateUser = Validator::make($data, [ // Validar entradas do usuário
                'name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'password' => 'required|string|min:6|confirmed',
            ]);
            // Verificar se está tudo ok
            if ($validateUser->fails()) {
                return ['status' => false, "validation" => true, "validationErrors" => $validateUser->errors()];
            }
            $user->password = $data['password'] = bcrypt($data['password']);
        } else {
            $validateUser = Validator::make($data, [ // Validar entradas do usuário
                'name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            ]);
    
            // Verificar se está tudo ok
            if ($validateUser->fails()) {
                return ['status' => false, "validation" => true, "validationErrors" => $validateUser->errors()];
            }
    
            $user->name = $data['name'];
            $user->email = $data['email'];
        }
    
        if (isset($data['image'])) {
            $time = time();
            $parentDirectory = 'perfis';
            $imageDirectory = $parentDirectory . DIRECTORY_SEPARATOR . 'perfil_id' . $user->id;
            $ext = substr($data['image'], 11, strpos($data['image'], ';') - 11);
    
            $urlImage = $imageDirectory . DIRECTORY_SEPARATOR . $time . '.' . $ext;
    
            $file = str_replace('data:image/' . $ext . ';base64,', '', $data['image']);
            $file = base64_decode($file);
    
            if (!file_exists($parentDirectory)) {
                mkdir($parentDirectory, 0700);
            }
            if ($user->image) {
                $imgUser = str_replace(asset('/'),'',$user->image);
                if (!file_exists($user->image)) {
                    unlink($user->image);
                }
            }
            if (!file_exists($imageDirectory)) {
                mkdir($imageDirectory, 0700);
            }
    
            file_put_contents($urlImage, $file);
    
            $user->image = $urlImage;
        }
    
        $user->save();
    
        //$user->image = asset($user->image);
        $user->token = $user->createToken($user->email)->accessToken;
        return ['status' => true, 'user' => $user];
    }

    public function friend(Request $request){
        $user = $request->user();
        $friend = User::find($request->id);

        if($friend && ($user->id != $friend->id)){
            $user->friends()->toggle($friend->id);
            return ['status' => true, 'friends' => $user->friends, "followers" => $friend->followers];
        }
        else return ['status' => false, 'error' => "Usuário não existe"];

        
    }

    public function listFriends(Request $request){
        $user = $request->user();
        if($user){
            return ['status' => true, 'friends' => $user->friends, "followers" => $user->followers];
        } else ['status' => false, 'error' => "Usuário não existe"];
    }

    public function listFriendsPage($id, Request $request){
        $user = User::find($id);
        $loggedUser = $request->user();
        if($user){
            return ['status' => true, 'friends' => $user->friends, "loggedFriends" => $loggedUser->friends, "followers" => $user->followers];
        } else ['status' => false, 'error' => "Usuário não existe"];
    }

}
