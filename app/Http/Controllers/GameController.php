<?php

namespace App\Http\Controllers;

use App\Services\GameApiService;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function __invoke(Request $request, GameApiService $gameapi){
       return response()->json(
           $gameapi->getGame($request['query'])
       );
    }
}
