<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'comment' => ['required', 'string', 'min:5', 'max:2000']
        ]);

        $comment = Auth::user()->comments()->create(['body' => $validated['comment'], 'review_id' => $request->review]);
        return response()->json([
            'comment' => $comment->load('user')
        ]);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Comment $comment)
    {
        $comment->delete();

        if ($request->has('profile')) {
            return redirect('/users/' . auth()->user()->id . '/'.'menu3/'.'edit')->with('info', 'Comentário deletado.');
        }

        return response()->json([
            'message' => 'Comentário deletado'
        ]);
    }
}
