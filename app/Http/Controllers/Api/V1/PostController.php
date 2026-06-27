<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $request = request();
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);

        $query = Post::with('user')->orderBy($sortBy, $sortOrder);
        $posts = $query->skip($offset)->take($limit)->get();

        return response()->json([
            'data' => $posts,
        ]);
    }

    /**
     * Display posts of current user
     */
    public function userPosts()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
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
        if ($post->user_id !== request()->user()->id) {
            return response([
                'message' => 'Forbidden'
            ], 403);
        }

        return $post;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}
