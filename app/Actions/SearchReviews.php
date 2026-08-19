<?php

namespace App\Actions;
use App\Models\Review;
use Illuminate\Http\Request;

class SearchReviews
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

        $reviews = Review::where('title', 'LIKE', '%' . $request->search . '%')->with(['user', 'comments']);

        $recommendationsCount = (clone $reviews)->selectRaw('recommendation, COUNT(*) as total')->groupBy('recommendation')->pluck('total', 'recommendation');

        if (in_array($filter, $this->recommendationsFilters)) {
            $reviews = (clone $reviews)->where('recommendation', $filter)->latest();
        } else {
            switch ($filter) {
                case 'highest-rated':
                    $reviews = (clone $reviews)->orderBy('rating', 'desc');
                    break;
                case 'lowest-rated':
                    $reviews = $reviews->orderBy('rating', 'asc');
                    break;
                case 'popular':
                    $reviews = $reviews->orderBy('views', 'desc');
                    break;
                case 'oldest':
                    $reviews = $reviews->oldest();
                    break;
                case 'hot-reviews':
                    $reviews = (clone $reviews)->withCount([
                        'comments as recent_comments_count' => function ($query) {
                            $query->where('created_at', '>=', now()->subDays(15));
                        }
                    ])->orderBy('recent_comments_count', 'desc');
                    break;
                case 'spoiler':
                    $reviews = $reviews->where('contains_spoilers', false);
                    break;
                default:
                    $reviews = $reviews->latest();
                    break;
            }
        }


        return [
            'reviews' => $reviews->paginate(10),
            'recommendationsTotal' => $recommendationsCount
        ];
    }
}
