<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    /**
     * Display posts with search, filters, sorting and pagination.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $status = $request->input('status');
        $sort = $request->input('sort', 'oldest');
        $perPage = (int) $request->input('per_page', 5);
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $showTrash = $request->boolean('trash', false);

        $allowedPerPage = [5, 10, 25, 50];

        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 5;
        }

        $query = Post::with('category');

        /*
        |--------------------------------------------------------------------------
        | Trash
        |--------------------------------------------------------------------------
        */
        if ($showTrash) {
            $query->onlyTrashed();
        }

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */
        $query->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        });

        /*
        |--------------------------------------------------------------------------
        | Category Filter
        |--------------------------------------------------------------------------
        */
        $query->when($category, function ($query, $category) {
            $query->where('category_id', $category);
        });

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */
        $query->when($status, function ($query, $status) {
            $query->where('status', $status);
        });

        /*
        |--------------------------------------------------------------------------
        | Date From
        |--------------------------------------------------------------------------
        */
        $query->when($dateFrom, function ($query, $dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        });

        /*
        |--------------------------------------------------------------------------
        | Date To
        |--------------------------------------------------------------------------
        */
        $query->when($dateTo, function ($query, $dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        });

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;

            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;

            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;

            case 'oldest':
            default:
                $query->oldest();
                break;
        }

        $posts = $query
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Post/Index', [
            'posts' => $posts,

            'categories' => Category::orderBy('name')->get(),

            'filters' => [
                'search' => $search ?? '',
                'category' => $category ?? '',
                'status' => $status ?? '',
                'sort' => $sort,
                'per_page' => $perPage,
                'date_from' => $dateFrom ?? '',
                'date_to' => $dateTo ?? '',
                'trash' => $showTrash,
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
     * Soft delete post.
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post moved to trash successfully.');
    }

    /**
     * Restore deleted post.
     */
    public function restore($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);

        $post->restore();

        return redirect()
            ->back()
            ->with('success', 'Post restored successfully.');
    }

    /**
     * Bulk delete posts.
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:posts,id'],
        ]);

        Post::whereIn('id', $validated['ids'])->delete();

        return redirect()
            ->back()
            ->with('success', count($validated['ids']) . ' post(s) moved to trash.');
    }

    /**
     * Export posts to CSV.
     */
    public function export(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $status = $request->input('status');
        $sort = $request->input('sort', 'oldest');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Post::with('category');

        $query->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        });

        $query->when($category, function ($query, $category) {
            $query->where('category_id', $category);
        });

        $query->when($status, function ($query, $status) {
            $query->where('status', $status);
        });

        $query->when($dateFrom, function ($query, $dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        });

        $query->when($dateTo, function ($query, $dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        });

        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;

            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;

            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;

            default:
                $query->oldest();
                break;
        }

        $posts = $query->get();

        $fileName = 'posts-' . now()->format('Y-m-d-H-i-s') . '.csv';

        return response()->streamDownload(function () use ($posts) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'Title',
                'Body',
                'Category',
                'Status',
                'Created At',
            ]);

            foreach ($posts as $post) {
                fputcsv($handle, [
                    $post->id,
                    $post->title,
                    $post->body,
                    $post->category?->name ?? 'No category',
                    ucfirst($post->status),
                    $post->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
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

        $deletedPosts = Post::onlyTrashed()->count();

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
                'deleted' => $deletedPosts,
            ],

            'categoryStatistics' => $categoryStatistics,
        ]);
    }
}
