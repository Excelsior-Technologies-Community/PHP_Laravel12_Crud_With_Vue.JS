<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\FilterPreset;
use App\Models\Post;
use App\Models\PostBookmark;
use App\Models\PostLike;
use App\Models\PostView;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
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
        $author = $request->input('author');
        $tag = $request->input('tag');
        $sort = $request->input('sort', 'latest');
        $perPage = (int) $request->input('per_page', 5);
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $showTrash = $request->boolean('trash', false);

        $allowedPerPage = [5, 10, 25, 50];

        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 5;
        }

        $query = Post::with(['category', 'user', 'tags']);

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
        | Author Filter
        |--------------------------------------------------------------------------
        */
        $query->when($author, function ($query, $author) {
            $query->where('user_id', $author);
        });

        /*
        |--------------------------------------------------------------------------
        | Tag Filter
        |--------------------------------------------------------------------------
        */
        $query->when($tag, function ($query, $tag) {
            $query->whereHas('tags', function ($q) use ($tag) {
                $q->where('tags.id', $tag);
            });
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
            case 'latest':
                $query->latest();
                break;

            case 'oldest':
                $query->oldest();
                break;

            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;

            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;

            case 'most_viewed':
                $query->orderByDesc('views_count');
                break;

            case 'most_liked':
                $query->withCount('likes')->orderByDesc('likes_count');
                break;

            default:
                $query->latest();
                break;
        }

        $posts = $query
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Post/Index', [
            'posts' => $posts,

            'categories' => Category::orderBy('name')->get(),
            'authors' => \App\Models\User::whereHas('posts')->orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),

            'filters' => [
                'search' => $search ?? '',
                'category' => $category ?? '',
                'status' => $status ?? '',
                'author' => $author ?? '',
                'tag' => $tag ?? '',
                'sort' => $sort,
                'per_page' => $perPage,
                'date_from' => $dateFrom ?? '',
                'date_to' => $dateTo ?? '',
                'trash' => $showTrash,
            ],

            'filterPresets' => Auth::user()?->filterPresets ?? [],
        ]);
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return Inertia::render('Post/Create', [
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
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
            'excerpt' => ['nullable', 'string', 'max:500'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:posts,slug'],
            'featured_image' => ['nullable', 'image', 'max:2048'],
            'category_id' => ['required', 'exists:categories,id'],
            'status' => ['required', 'in:draft,published,archived'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ]);

        $validated['user_id'] = Auth::id();
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);

        if ($request->hasFile('featured_image')) {
            $image = Image::make($request->file('featured_image'))
                ->orientate()
                ->fit(1200, 630, function ($constraint) {
                    $constraint->upsize();
                })
                ->encode('webp', 85);

            $path = 'posts/' . uniqid() . '.webp';
            Storage::disk('public')->put($path, $image);

            $validated['featured_image'] = $path;
        }

        $post = Post::create($validated);

        if (!empty($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post created successfully.');
    }

    /**
     * Show post.
     */
    public function show(Post $post)
    {
        $post->load('category', 'user', 'tags', 'likes', 'bookmarks', 'comments.user');

        $post->views_count = $post->views()->count();
        $post->likes_count = $post->likes()->count();
        $post->comments_count = $post->comments()->count();

        $userLiked = false;
        $userBookmarked = false;

        if (Auth::check()) {
            $userLiked = $post->likes()->where('user_id', Auth::id())->exists();
            $userBookmarked = $post->bookmarks()->where('user_id', Auth::id())->exists();
        }

        return Inertia::render('Post/Show', [
            'post' => $post,
            'userLiked' => $userLiked,
            'userBookmarked' => $userBookmarked,
            'comments' => $post->comments,
        ]);
    }

    /**
     * Show edit form.
     */
    public function edit(Post $post)
    {
        return Inertia::render('Post/Edit', [
            'post' => $post->load('category', 'tags'),
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
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
            'excerpt' => ['nullable', 'string', 'max:500'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:posts,slug,' . $post->id],
            'featured_image' => ['nullable', 'image', 'max:2048'],
            'category_id' => ['required', 'exists:categories,id'],
            'status' => ['required', 'in:draft,published,archived'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);

        if ($request->hasFile('featured_image')) {
            $image = Image::make($request->file('featured_image'))
                ->orientate()
                ->fit(1200, 630, function ($constraint) {
                    $constraint->upsize();
                })
                ->encode('webp', 85);

            $path = 'posts/' . uniqid() . '.webp';
            Storage::disk('public')->put($path, $image);

            $validated['featured_image'] = $path;
        }

        $post->update($validated);

        if (array_key_exists('tags', $validated)) {
            $post->tags()->sync($validated['tags'] ?? []);
        }

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

        $query = Post::with(['category', 'user']);

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
                'Author',
                'Status',
                'Created At',
            ]);

            foreach ($posts as $post) {
                fputcsv($handle, [
                    $post->id,
                    $post->title,
                    $post->body,
                    $post->category?->name ?? 'No category',
                    $post->user?->name ?? 'Unknown',
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

    /**
     * Track post view.
     */
    public function trackView(Request $request, Post $post)
    {
        $userId = Auth::id();
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        PostView::updateOrCreate(
            [
                'post_id' => $post->id,
                'user_id' => $userId,
                'ip_address' => $ipAddress,
            ],
            [
                'user_agent' => $userAgent,
                'viewed_at' => now(),
            ]
        );

        return response()->json([
            'views' => $post->views()->count(),
        ]);
    }

    /**
     * Toggle like on post.
     */
    public function toggleLike(Request $request, Post $post)
    {
        $user = Auth::user();
        $like = $post->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $post->likes()->count(),
        ]);
    }

    /**
     * Toggle bookmark on post.
     */
    public function toggleBookmark(Request $request, Post $post)
    {
        $user = Auth::user();
        $bookmark = $post->bookmarks()->where('user_id', $user->id)->first();

        if ($bookmark) {
            $bookmark->delete();
            $bookmarked = false;
        } else {
            $post->bookmarks()->create(['user_id' => $user->id]);
            $bookmarked = true;
        }

        return response()->json([
            'bookmarked' => $bookmarked,
        ]);
    }

    /**
     * Store comment.
     */
    public function storeComment(Request $request, Post $post)
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:comments,id'],
        ]);

        $comment = $post->comments()->create([
            'user_id' => Auth::id(),
            'body' => $validated['body'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        return response()->json([
            'comment' => $comment->load('user'),
        ]);
    }

    /**
     * Delete comment.
     */
    public function destroyComment(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully.',
        ]);
    }

    /**
     * Autocomplete search suggestions.
     */
    public function autocomplete(Request $request)
    {
        $search = $request->input('q', '');

        $posts = Post::where('title', 'like', "%{$search}%")
            ->where('status', 'published')
            ->limit(10)
            ->get(['id', 'title', 'slug']);

        return response()->json($posts);
    }

    /**
     * Get authors for filter.
     */
    public function getAuthors(Request $request)
    {
        $authors = \App\Models\User::whereHas('posts', function ($query) {
            $query->where('status', 'published');
        })
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($authors);
    }

    /**
     * Get tags for filter.
     */
    public function getTags(Request $request)
    {
        $tags = Tag::orderBy('name')->get(['id', 'name', 'slug']);

        return response()->json($tags);
    }

    /**
     * Get user filter presets.
     */
    public function getFilterPresets(Request $request)
    {
        $presets = Auth::user()->filterPresets()->orderByDesc('created_at')->get();

        return response()->json($presets);
    }

    /**
     * Save filter preset.
     */
    public function saveFilterPreset(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'filters' => ['required', 'array'],
        ]);

        $preset = FilterPreset::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'filters' => $validated['filters'],
        ]);

        return response()->json([
            'preset' => $preset,
        ]);
    }

    /**
     * Load filter preset.
     */
    public function loadFilterPreset(FilterPreset $filterPreset)
    {
        $this->authorize('view', $filterPreset);

        return response()->json([
            'filters' => $filterPreset->filters,
        ]);
    }

    /**
     * Delete filter preset.
     */
    public function deleteFilterPreset(FilterPreset $filterPreset)
    {
        $this->authorize('delete', $filterPreset);
        $filterPreset->delete();

        return response()->json([
            'message' => 'Filter preset deleted successfully.',
        ]);
    }

    /**
     * Get post views analytics.
     */
    public function getViewsAnalytics(Request $request)
    {
        $days = (int) $request->input('days', 30);

        $data = PostView::selectRaw('DATE(viewed_at) as date, COUNT(*) as views')
            ->where('viewed_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($data);
    }
}
