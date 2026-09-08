<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    /**
     * Display posts with live search and filtering.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $status = $request->input('status');

        $posts = Post::with('category')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('body', 'like', "%{$search}%");
                });
            })
            ->when($category, function ($query, $category) {
                $query->where('category_id', $category);
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->get();

        return Inertia::render('Post/Index', [
            'posts' => $posts,
            'categories' => Category::orderBy('name')->get(),
            'filters' => [
                'search' => $search ?? '',
                'category' => $category ?? '',
                'status' => $status ?? '',
            ],
        ]);
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return Inertia::render('Post/Create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Store post.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);

        Post::create($validated);

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(Post $post)
    {
        return Inertia::render('Post/Edit', [
            'post' => $post->load('category'),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Update post.
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);

        $post->update($validated);

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post updated successfully.');
    }

    /**
     * Delete post.
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post deleted successfully.');
    }

    /**
     * Post statistics dashboard.
     */
    public function statistics()
    {
        $totalPosts = Post::count();

        $publishedPosts = Post::where('status', 'published')->count();

        $draftPosts = Post::where('status', 'draft')->count();

        $archivedPosts = Post::where('status', 'archived')->count();

        $categoryStatistics = Category::withCount('posts')
            ->orderByDesc('posts_count')
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'count' => $category->posts_count,
                ];
            });

        return Inertia::render('PostStatistics', [
            'stats' => [
                'total' => $totalPosts,
                'published' => $publishedPosts,
                'draft' => $draftPosts,
                'archived' => $archivedPosts,
            ],
            'categoryStatistics' => $categoryStatistics,
        ]);
    }
}