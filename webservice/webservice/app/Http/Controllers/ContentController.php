<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Content;

class ContentController extends Controller
{
    public function list(Request $request)
    {
        $contents = Content::with('user')->orderBy('date', 'DESC')->paginate(5);
        $user = $request->user();

        foreach ($contents as $key => $content) {
            $content->totalLikes = $content->likes()->count();
            $content->postComments = $content->comments()->with('user')->get();
            $userLiked = $user->likes()->find($content->id);
            if ($userLiked)
                $content->userLiked = true;
            else
                $content->userLiked = false;

        }

        return ["status" => true, "contents" => $contents];
    }

    public function add(Request $request)
    {
        $data = $request->all();
        $user = $request->user();
        //return ["status" => true, "contents" => $data];

        // Validação
        $validateUser = Validator::make($data, [ // Validar entradas do usuário
            'title' => 'required',
            'text' => 'string',
        ]);

        // Verificar se está tudo ok
        if ($validateUser->fails()) {
            return ['status' => false, "validation" => true, "validationErrors" => $validateUser->errors()];
        }

        $content = new Content;
        $content->title = $data['title'];
        $content->text = $data['text'];
        $content->link = $data['link'] ?: '#';
        $content->image = $data['image'] ?: '#';
        $content->date = date('Y-m-d H:i:s');

        $user->contents()->save($content);

        $contents = Content::with('user')->orderBy('date', 'DESC')->paginate(5);

        return ["status" => true, "contents" => $contents];
    }

    public function like(Request $request, $id)
    {
        $content = Content::find($id);

        if ($content) {
            $user = $request->user();
            $user->likes()->toggle($content->id);

            // return $content->likes()->count();
            return [
                "status" => true,
                "likes" => $content->likes()->count(),
                "list" => $this->list($request)
            ];
        } else {
            return ["status" => false, "error" => "Conteúdo não existe"];
        }
    }

    public function comment(Request $request, $id)
    {
        $content = Content::find($id);

        if ($content) {
            $user = $request->user();
            $user->comments()->create([
                'content_id' => $content->id,
                'text' => $request->text,
                'date' => date('Y-m-d')
            ]);
            return [
                "status" => true,
                "list" => $this->list($request)
            ];
        } else {
            return ["status" => false, "error" => "Conteúdo não existe"];
        }
    }
}
