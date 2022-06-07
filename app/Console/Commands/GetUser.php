<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class GetUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:user {user_id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ini dipakai untuk mendapatkan data user dari DB';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       $userId = $this->argument('user_id');
       $user = User::find($userId);

       if(!$user) {
           return $this->error('user not found!');
       }

       return $this->info('Name:' .$user->name);
    }
}
