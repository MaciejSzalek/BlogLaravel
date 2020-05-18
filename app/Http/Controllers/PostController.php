<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostFromRequest;
use App\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Posts::where('active', 1)->orderBy('created_at', 'desc')->paginate(5);
        $title = 'Latest Posts';
        return view('home')->withPosts($posts)->withTitle($title);
    }

    public function create(Request $request)
    {
        if($request->user()->can_post()) {
            return view('posts.create');
        }
        return redirect('/')->withErrors('You have not sufficient permissions for writing post');

    }

    public function store(PostFromRequest $request)
    {
        $post = new Posts();
        $post->title = $request->get('title');
        $post->body = $request->get('body');
        $post->slug = Str::slug($post->title);

        $duplicate = Posts::where('slug', $post->slug)->first();
        if($duplicate) {
            return redirect('new-post')->withErrors('Title already exists.')->withInput();
        }
         $post->author_id = $request->user()->id;
        if($request->has('save')) {
            $post->active = 0;
            $message = 'Posta saved successfully';
        } else {
            $post->active = 1;
            $message = 'Post published successfully';
        }
        $post->save();
        return redirect('edit/'.$post->slug)->withMessage($message);
    }
}
