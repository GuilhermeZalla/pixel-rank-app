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

    protected $recommendations = ['not_recommended', 'recommended', 'mixed', 'essential'];

    public function index(Request $request)
    {
        $filter = $request->filter;
        $reviews = Review::with(['game', 'user', 'comments']);

        if (in_array($filter, $this->recommendations)) {
            $reviews->where('recommendation', $filter);
        } else {
            switch ($filter) {
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
                    $reviews = $reviews->withCount([
                        'comments as recent_comments_count' => function ($query) {
                            $query->where('created_at', '>=', now()->subDays(7));
                        }
                    ])->orderBy('recent_comments_count', 'desc');
                    break;
                default:
                    $reviews = $reviews->latest();
                    break;
            }
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
        $pros_cons = $review->pros_cons->groupBy('type');

        return view('reviews.show', ['review' => $review, 'pros_cons' => $pros_cons, 'game' => $info]);
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
