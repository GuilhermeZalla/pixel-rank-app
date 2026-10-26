<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Review;
use App\Notifications\CommentOnReviewNotification;
use App\Notifications\ReplyToCommentNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return redirect('/users/'.auth()->id().'/menu3/edit');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'comment' => ['required', 'string', 'min:5', 'max:2000'],
            'review' => ['required', 'integer', 'exists:reviews,id'],
            'reply_to' => [
                'nullable',
                'integer',
                Rule::exists('comments', 'id')->where('review_id', $request->input('review')),
            ],
        ]);

        $comment = Auth::user()->comments()->create([
            'body' => $validated['comment'],
            'review_id' => $validated['review'],
        ]);

        if ($validated['reply_to'] ?? null) {
            $repliedComment = Comment::where('review_id', $validated['review'])
                ->findOrFail($validated['reply_to']);
            $comment->replyTo()->associate($repliedComment);
            $comment->save();

            if ($repliedComment->user_id !== Auth::id()) {
                $repliedComment->user->notify(new ReplyToCommentNotification($comment));
            }
        }

        $review = Review::find($validated['review']);

        if ($review && $review->user_id !== Auth::id()) {
            $review->user->notify(new CommentOnReviewNotification($review, $comment));
        }

        $comment->load(['user:id,nickname,avatar', 'replyTo:id,user_id,created_at', 'replyTo.user:id,nickname,avatar'])
            ->loadCount('likes')
            ->setAttribute('liked_by_current_user', false);

        return response()->json(['comment' => $comment]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'comment' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $comment->update(['body' => $validated['comment']]);

        return response()->json(['comment' => $comment->load('user:id,nickname,avatar')]);
    }

    public function like(Comment $comment)
    {
        DB::table('comment_likes')->insertOrIgnore([
            'comment_id' => $comment->id,
            'user_id' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'likes_count' => $comment->likes()->count(),
            'liked_by_current_user' => true,
        ]);
    }

    public function unlike(Comment $comment)
    {
        DB::table('comment_likes')
            ->where('comment_id', $comment->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json([
            'likes_count' => $comment->likes()->count(),
            'liked_by_current_user' => false,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Comment $comment)
    {
        $comment->delete();

        if ($request->has('profile')) {
            return redirect('/users/'.auth()->user()->id.'/'.'menu3/'.'edit')->with('info', __('reviews.comment_deleted_status'));
        }

        return response()->json([
            'message' => __('reviews.comment_deleted_status'),
        ]);
    }
}
