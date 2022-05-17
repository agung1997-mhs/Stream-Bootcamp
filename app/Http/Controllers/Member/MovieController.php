<?php

namespace App\Http\Controllers\Member;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserPremium;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MovieController extends Controller
{
    public function show($id)
    {
        return view('member.movie-detail');
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
                return view('member.movie-watching');
            }
        }

        return redirect()->route('pricing');
        
    }
}
