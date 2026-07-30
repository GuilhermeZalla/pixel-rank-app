<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Models\ProsCons;
use App\Models\Review;
use App\Services\GameApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $recommendations = ['not_recommended', 'recommended', 'mixed', 'essential'];

    public function index(Request $request, GameApiService $gameapi)
    {
        $filter = $request->filter;
        $reviews = Review::with(['user', 'comments']);

        if (in_array($filter, $this->recommendations)) {
            $reviews = $reviews->where('recommendation', $filter);
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
                case 'spoiler':
                     $reviews = $reviews->where('contains_spoilers', false);
                break;
                default:
                    $reviews = $reviews->latest();
                    break;
            }
        }

        $recommendations = Review::selectRaw('recommendation, COUNT(*) as total')->groupBy('recommendation')->pluck('total', 'recommendation');

        return view('reviews.index', [
            'reviews' => $reviews->paginate(12),
            'recommendationsTotal' => $recommendations
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
    public function store(ReviewRequest $request)
    {
        $validated = $request->validated();
        $review = Auth::user()->reviews()->create(Arr::except($validated, ['pros', 'cons']));

        $review->pros_cons()->createMany(
            collect($validated['pros'] ?? [])->map(fn($pro) => [
                'type' => 'pros',
                'content' => $pro,
            ])->all()
        );

        $review->pros_cons()->createMany(
            collect($validated['cons'] ?? [])->map(fn($con) => [
                'type' => 'cons',
                'content' => $con,
            ])->all()
        );

        return redirect('/reviews/'.$review->id);
    }

    /**
     * Display the specified resource.
     */
   public function show(Request $request, GameApiService $gameapi)
    {
        $review = Review::with(['user', 'comments', 'comments.user'])->find($request->review);
        $info = $review->getGameInfo($gameapi->getGame($review->game_id)[0]);
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
