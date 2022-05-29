<?php

namespace App\Http\Controllers\Member;

use Carbon\Carbon;
use App\Models\Movie;
use App\Models\UserPremium;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MovieController extends Controller
{
    public function show($id)
    {
        $movie = Movie::findOrFail($id);

        return view('member.movie-detail', [
            'movie' => $movie
        ]);
    }

    public function watch($id)
    {
        $userId = Auth::user()->id;

        $userPremium = UserPremium::where('user_id', $userId)->first();

        if($userPremium) {
            $endOfSubsription = $userPremium->end_of_subscription;
            $date = Carbon::createFromFormat('Y-m-d', $endOfSubsription);

            $isValidSubscription = $date->greaterThan(now()); // cek .. apakah tanggal di database lebih besar dari tanggal sekarang ? jika iya : false
            if($isValidSubscription) {
                $movie = Movie::findOrFail($id);

                return view('member.movie-watching', [
                    'movie' => $movie
                ]);
            }
        }

        return redirect()->route('pricing');
        
    }
}
