<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::published()->latest()->paginate();
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(StorePostRequest $request)
    {
        // dd("aaa");
        $data = $request->only('title', 'body');
        $data['slug'] = str_slug($data['title']);
        $data['user_id'] = Auth::user()->id;
        $post = Post::create($data);
        return redirect()->route('edit_post', ['id' => $post->id]);
    }    

    public function drafts()
    {
        $postsQuery = Post::unpublished();

        //如果 see-all-drafts 禁止
        if(Gate::denies('see-all-drafts')) {
            $postsQuery = $postsQuery->where('user_id', Auth::user()->id);
        }

        $posts = $postsQuery->paginate();
        return view('posts.drafts', compact('posts'));
    }    

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }
    
    public function update(Post $post, UpdatePostRequest $request)
    {
        $data = $request->only('title', 'body');
        $data['slug'] = str_slug($data['title']);
        $post->fill($data)->save();
        return back();
    }

    public function publish(Post $post)
    {
        $post->published = true;
        $post->save();
        return redirect()->route('show_post', ['post' => $post->id]);
    }
    
    public function unpublish(Post $post)
    {
        $post->published = false;
        $post->save();
        return back();
    }
    public function show(Post $post)
    {
        // $post = Post::findOrFail($id);
        // dd($post->id);
        if ($post->published || $post->user_id == Auth::user()->id) {
            return view('posts.show', compact('post'));
        }
        abort(403, 'Unauthorized.');
    }
    public function destory(Post $post)
    {
        $post->delete();
        return redirect()->route('list_post');;
    }            
    
}
