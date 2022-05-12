<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Agung Widyatmoko',
            'email' => 'agungwidyatmoko92@gmail.com',
            'password' => Hash::make('aldacr123'),
            'phone_number' => '081293027216',
            'avatar' => '',
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
