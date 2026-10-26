<?php

namespace App\Http\Controllers;

use App\Actions\FetchReviews;
use App\Http\Requests\ReviewRequest;
use App\Models\ProsCons;
use App\Models\Review;
use App\Services\GameApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{

    /**
     * Display a listing of the resource.
     */

    public function index(Request $request, FetchReviews $fetch)
    {
        $data = $fetch->execute($request);

        return view('reviews.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, GameApiService $gameapi)
    {
        $platforms = $gameapi->getPlatforms();

        return view('reviews.create', [
            'review' => $request->review,
            'platforms' => $platforms,
            'gameApiAvailable' => $gameapi->isConfigured() && ! empty($platforms),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReviewRequest $request)
    {
        $validated = $request->validated();

        $review = DB::transaction(function () use ($validated) {
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

            return $review;
        });

        return redirect('/reviews/' . $review->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, GameApiService $gameapi)
    {
        $review = Review::with(['user', 'pros_cons'])->find($request->review);

        if (! $review) {
            abort(404);
        }

        $games = $gameapi->getGame((string) $review->game_id);
        $info = $review->getGameInfo($games[0] ?? []);

        // game_cover deve ser um image_id do IGDB (apenas alfanumérico, sem hífens/UUID).
        // Valores inválidos resultam em URL quebrada no images.igdb.com.
        $gameCover = $review->game_cover;
        if (! is_string($gameCover) || ! preg_match('/^[a-zA-Z0-9_-]{3,40}$/', $gameCover)) {
            $gameCover = null;
        }

        $pros_cons = $review->pros_cons->groupBy('type');
        $commentCount = $review->comments()->count();
        $comments = $review->comments()
            ->with(['user:id,nickname,avatar', 'replyTo:id,user_id,created_at', 'replyTo.user:id,nickname,avatar'])
            ->withCount('likes')
            ->withExists(['likes as liked_by_current_user' => fn ($query) => $query->where('users.id', Auth::id() ?? 0)])
            ->orderByDesc('created_at')->orderByDesc('id')->limit(20)->get();

        $review->increment('views');

        return view('reviews.show', [
            'review' => $review,
            'comments' => $comments,
            'commentCount' => $commentCount,
            'commentsHasMore' => $commentCount > $comments->count(),
            'pros_cons' => $pros_cons,
            'game' => $info,
            'gameApiAvailable' => $gameapi->isConfigured() && ! empty($games),
            'gameCover' => $gameCover,
        ]);
    }

    public function comments(Request $request, Review $review)
    {
        $validated = $request->validate([
            'sort' => ['sometimes', 'string', Rule::in(['newest', 'oldest'])],
            'offset' => ['sometimes', 'integer', 'min:0'],
        ]);

        $sort = $validated['sort'] ?? 'newest';
        $offset = (int) ($validated['offset'] ?? 0);
        $query = $review->comments()
            ->with(['user:id,nickname,avatar', 'replyTo:id,user_id,created_at', 'replyTo.user:id,nickname,avatar'])
            ->withCount('likes')
            ->withExists(['likes as liked_by_current_user' => fn ($likes) => $likes->where('users.id', Auth::id() ?? 0)]);
        $total = (clone $query)->count();

        if ($sort === 'oldest') {
            $query->orderBy('created_at')->orderBy('id');
        } else {
            $query->orderByDesc('created_at')->orderByDesc('id');
        }

        $items = $query->offset($offset)->limit(15)->get();
        $nextOffset = $offset + $items->count();

        return response()->json([
            'comments' => $items,
            'total' => $total,
            'next_offset' => $nextOffset,
            'has_more' => $nextOffset < $total,
            'sort' => $sort,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        return view('reviews.edit', ['review' => $review->load('pros_cons')]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReviewRequest $request, Review $review)
    {
        $validated = $request->validated();
        $review->fill(Arr::except($validated, ['pros', 'cons', 'game_id', 'game_name', 'game_cover']));
        $review->contains_spoilers = $request->boolean('contains_spoilers');
        $review->save();

        return redirect('/reviews/'.$review->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $deleted = $review->title;
        $review->delete();
        return redirect('/')->with('info', __('reviews.review_deleted', ['title' => $deleted]));
    }
}
