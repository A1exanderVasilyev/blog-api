<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $params = $this->getPaginationParams($request);
        $posts = Post::with('user')
            ->orderBy($params['sortBy'], $params['sortOrder'])
            ->skip($params['offset'])
            ->take($params['limit'])
            ->get();

        return response()->json([
            'data' => $posts,
        ]);
    }

    /**
     * Display posts of current user
     */
    public function userPosts(Request $request): JsonResponse
    {
        $params = $this->getPaginationParams($request);
        $user = $request->user();
        $posts = Post::where('user_id', $user->id)
            ->orderBy($params['sortBy'], $params['sortOrder'])
            ->skip($params['offset'])
            ->take($params['limit'])
            ->get();

        return response()->json([
            'data' => $posts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();
        $post = Post::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'text' => $validated['text'],
        ]);

        return response()->json([
            'data' => $post
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return $post;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        if ($request->user()->id !== $post->user_id)
        {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $validated = $request->validated();

        $post->update([
            'title' => $validated['title'],
            'text' => $validated['text'],
        ]);

        return response()->json([
            'data' => $post->fresh('user')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Post $post): JsonResponse
    {
        if ($request->user()->id !== $post->user_id)
        {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $post->delete();

        return response()->json([
            'message' => 'Post deleted'
        ]);
    }

    private function getPaginationParams(Request $request): array
    {
        $limit = min($request->input('limit', 10), 100);
        $sortOrder = in_array($request->input('sort_order'), ['asc', 'desc'])
            ? $request->input('sort_order')
            : 'desc';
        $allowedSorts = ['created_at', 'title'];
        $sortByInput = $request->input('sort_by');
        $sortBy = in_array($sortByInput, $allowedSorts)
            ? $sortByInput
            : 'created_at';
        $offset = max($request->input('offset', 0), 0);
        return [
            'limit' => $limit,
            'offset' => $offset,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder
        ];
    }
}
