<?php

namespace App\Http\Controllers\Api;

use App\Models\Movie;
use App\Models\UserPremium;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Controller;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $movies = Movie::where('title', 'like', '%' . $search . '%')
        ->orderBy('featured', 'ASC')
        ->orderBy('created_at', 'DESC')
        ->paginate(2);

        return response()->json($movies);
    }

    public function show(Request $request, $id)
    {
        $user = $request->get('user');

        $userPremium = UserPremium::where('user_id', $user->id)->first();
      
        $movie = Redis::get('movie-'.$id);

        if(!$movie) { 
            $movie = Movie::findOrFail($id);
            Redis::set('movie-'.$id, $movie);
        } else {
            $movie = json_decode($movie);
        }


        if(!$movie) {
             return response()->json([
                'message' => 'movie not found'
            ], 404);
        }

        if($userPremium) {
            $endOfSubsription = $userPremium->end_of_subscription;
            $date = Carbon::createFromFormat('Y-m-d', $endOfSubsription);

            $isValidSubscription = $date->greaterThan(now()); // cek .. apakah tanggal di database lebih besar dari tanggal sekarang ? jika iya : false
            if($isValidSubscription) {
                return response()->json($movie);
            }
        }

        return response()->json(['message' => 'you dont have subscription plan']);
    }
}
