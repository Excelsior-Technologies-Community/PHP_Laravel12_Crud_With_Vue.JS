<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    // Show all posts
    public function index()
    {
        return Inertia::render('Post/Index', [
            'posts' => Post::latest()->get()
        ]);
    }

    // Show create form
    public function create()
    {
        return Inertia::render('Post/Create');
    }

    // Store post
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'body'  => 'required',
        ]);

        Post::create($request->all());

        return redirect()->route('posts.index');
    }

    // Show edit form
    public function edit(Post $post)
    {
        return Inertia::render('Post/Edit', [
            'post' => $post
        ]);
    }

    // Update post
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required',
            'body'  => 'required',
        ]);

        $post->update($request->all());

        return redirect()->route('posts.index');
    }

    // Delete post
    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->back();
    }
}
