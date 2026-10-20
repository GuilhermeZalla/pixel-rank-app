<?php

namespace App\Http\Controllers;

use App\Services\GameApiService;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function __invoke(Request $request, GameApiService $gameapi){
        $query = (string) $request->input('query', '');

        if (trim($query) === '') {
            return response()->json([]);
        }

        return response()->json($gameapi->getGame($query));
    }
}
