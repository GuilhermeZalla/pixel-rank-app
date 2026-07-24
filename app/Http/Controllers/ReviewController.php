<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Services\GameApiService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $reviews = Review::with(['game', 'user', 'comments']);

        switch ($request->filter) {
            case 'highest-rated':
                $reviews = $reviews->orderBy('rating', 'desc');
                break;
            case 'lowest-rated':
                $reviews = $reviews->orderBy('rating', 'asc');
                break;
            case 'popular':
                break;
            case 'oldest':
                $reviews = $reviews->oldest();
                break;
              case 'hot-reviews':
                $reviews = $reviews->withCount('comments')->orderByDesc('comments_count');
                break;
            default:
                $reviews = $reviews->latest();
                break;
        }

        return view('reviews.index', [
            'reviews' => $reviews->paginate(12)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('reviews.create', ['review' => $request->review]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, GameApiService $gameapi)
    {
        $review = Review::with(['game', 'user', 'comments', 'comments.user'])->find($request->review);
        $info = $review->getGameInfo($gameapi->getGame($review->game->title)[0]);

        return view('reviews.show', ['review' => $review, 'game' => $info]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $review->delete();
        return redirect('/');
    }
}
