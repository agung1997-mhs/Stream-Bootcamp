<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailTest;

class sendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim email testing aja';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $emailTo = "agungwidyatmoko92@gmail.com";
        mail::to($emailTo)->send(new EmailTest($emailTo));

        return $this->info('email already sent to '.$emailTo);
    }
}
