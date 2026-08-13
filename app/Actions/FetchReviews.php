<?php

namespace App\Actions;
use App\Models\Review;
use Illuminate\Http\Request;

class FetchReviews
{
    protected $recommendationsFilters = ['not_recommended', 'recommended', 'mixed', 'essential'];

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function execute(Request $request)
    {
        $filter = $request->filter;
        $reviews = !empty($request['game_id']) ? Review::with(['user', 'comments'])->where('game_id', $request['game_id']) : Review::with(['user', 'comments']);
        $reviewsHot = (clone $reviews)->withCount([
            'comments as recent_comments_count' => function ($query) {
                $query->where('created_at', '>=', now()->subDays(15));
            }
        ])->orderBy('recent_comments_count', 'desc');
        $reviewsHighest = (clone $reviews)->orderBy('rating', 'desc');

        if (in_array($filter, $this->recommendationsFilters)) {
            $reviews = (clone $reviews)->where('recommendation', $filter)->latest();
        } elseif (!empty($request->search)) {
            $reviews->where('title', 'LIKE', '%' . $request->search . '%');
        } else {
            switch ($filter) {
                case 'highest-rated':
                    $reviews = $reviewsHighest;
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
                    $reviews = $reviewsHot;
                    break;
                case 'spoiler':
                    $reviews = $reviews->where('contains_spoilers', false);
                    break;
                default:
                    $reviews = $reviews->latest();
                    break;
            }
        }

        $recommendations = Review::selectRaw('recommendation, COUNT(*) as total')->groupBy('recommendation')->pluck('total', 'recommendation');

        return [
            'reviews' => $reviews->paginate(15),
            'recommendationsTotal' => $recommendations,
        ];
    }
}
