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

        $recentCommentsSince = now()->subDays(15);
        $featuredReviews = Review::query()
            ->withCount([
                'comments as recent_comments_count' => function ($query) use ($recentCommentsSince) {
                    $query->where('created_at', '>=', $recentCommentsSince);
                },
            ])
            ->whereHas('comments', function ($query) use ($recentCommentsSince) {
                $query->where('created_at', '>=', $recentCommentsSince);
            })
            ->orderByDesc('recent_comments_count')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $featuredReviewMetric = 'recent_comments';

        if ($featuredReviews->isEmpty()) {
            $featuredReviewMetric = 'views';
            $featuredReviews = Review::query()
                ->orderByDesc('views')
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->limit(5)
                ->get();
        }

        if (in_array($filter, $this->recommendationsFilters)) {
            $reviews = (clone $reviews)->where('recommendation', $filter)->latest();
        } elseif (!empty($request->search)) {
            $reviews->where('title', 'LIKE', '%' . $request->search . '%');
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
                        'comments as recent_comments_count' => function ($query) use ($recentCommentsSince) {
                            $query->where('created_at', '>=', $recentCommentsSince);
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

        $recommendations = Review::selectRaw('recommendation, COUNT(*) as total')->groupBy('recommendation')->pluck('total', 'recommendation');
        return [
            'reviews' => $reviews->paginate(10),
            'recommendationsTotal' => $recommendations,
            'featuredReviews' => $featuredReviews,
            'featuredReviewMetric' => $featuredReviewMetric,
        ];
    }
}
