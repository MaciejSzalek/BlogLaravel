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

    public function show($slug)
    {
        $post = Posts::where('slug', $slug)->first();
        if(!$post)
        {
            return redirect('/')->withErrors('Requested page not found');
        }
        $comments = $post->comments;
        return view('posts.show')->withPost($post)->withComments($comments);
    }

    public function edit(Request $request, $slug)
    {
        $post = Posts::where('slug', $slug)->first();
        if($post && ($request->user()->id == $post->author_id || $request->user()->is_admin()))
        {
            return view('posts.edit')->with('post', $post);
        }
        return redirect('/')->withErrors('You have not sufficient permission');
    }

    public function update(Request $request)
    {
        $post_id = Posts::find('post_id');
        $post = Posts::find($post_id);
        if($post && ($post->author_id == $request->user()->id || $request->user()->is_admin()))
        {
            $title = $request->input('title');
            $slug = Str::slug($title);
            $duplicate = Posts::where('slug', $slug)->first();
            if($duplicate)
            {
                if($duplicate->id != $post_id)
                {
                    return redirect('edit/'.$post->slug)->withErrors('Title already exists.')->withInput();
                } else {
                    $post->slug = $slug;
                }
            }
            $post->title = $title;
            $post->body = $request->input('body');

            if($request->has('save'))
            {
                $post->active = 0;
                $message = 'Post saved successfully';
                $landing = $post->slug;
            } else {
                $post->active = 1;
                $message = 'Post update successfully';
                $landing = $post->slug;
            }
            $post->save();
            return redirect($landing)->withMessage($message);
        } else {
            return redirect('/')->withErrors('You have not sufficient permission');
        }
    }

    public function destroy(Request $request, $id)
    {
        $post = Posts::find($id);
        if($post && ($post->author_id == $request->user()->id || $request->user()->is_admin()))
        {
            $post->delete();
            $data['message'] = 'Post deleted successfully';
        } else {
            $data['errors'] = 'Invalid Operation. You have not sufficient permission';
        }
        return redirect('/')->with($data);
    }
}
