<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Package;
use App\Models\UserPremium;
use Illuminate\Support\Carbon;

class WebHookController extends Controller
{
    public function handler(Request $request)
    {
        \Midtrans\Config::$isProduction = (bool)env('MIDTRANS_IS_PRODUCTION');
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        $notif = new \Midtrans\Notification();

        $transactionStatus = $notif->transaction_status;
        $orderId = $notif->order_id;
        $fraudStatus = $notif->fraud_status;

        $status = '';

        if ($transactionStatus == 'capture'){
            if ($fraudStatus == 'challenge'){
                $status = 'CHALLENGE';
            } else if ($fraudStatus == 'accept'){
                $status = 'SUCCESS';
            }
        } else if ($transactionStatus == 'settlement'){
            $status = 'SUCCESS';
        } else if ($transactionStatus == 'cancel' ||
          $transactionStatus == 'deny' ||
          $transactionStatus == 'expire'){
          $status = 'FAILURE';
        } else if ($transactionStatus == 'pending'){
          $status = 'PENDING';
        }

        $transaction = Transaction::with('package')
            ->where('transaction_code', $orderId)
            ->first();

        if($status === 'SUCCESS') {  
            $userPremium = UserPremium::where('user_id', $transaction->user_id)->first();
            
            if ($userPremium) {
                // renewal subscription (langganan perpanjangan)
                $endOfSubscription = $userPremium->end_of_subscription; // -> string "2022,05,19"
                $date = Carbon::createFromFormat('Y-m-d', $endOfSubscription); // -> jadi object (2022,05,19)
                $newEndOfSubscription = $date->addDays($transaction->package->max_days)->format('Y-m-d');
                
                $userPremium->update([
                    'package_id' => $transaction->package_id,
                    'end_of_subscription' => $newEndOfSubscription
                ]);
            
            } else {
                // new subscriber (user yg baru pertama kali subscribe)
                UserPremium::create([
                    'package_id' => $transaction->package->id,
                    'user_id' => $transaction->user_id,
                    'end_of_subscription' => now()->addDays($transaction->package->max_days)
                ]); 
            }
        }

        $transaction->update(['status' => $status]);

        return response()->json(null);
    }
}
