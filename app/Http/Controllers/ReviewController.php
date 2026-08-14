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
        return view('reviews.create', ['review' => $request->review, 'platforms' => $gameapi->getPlatforms()]);
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
        $deleted = $review->title;
        $review->delete();
        return redirect('/')->with('info', 'Review deleted: "'.$deleted.'"');
    }
}
