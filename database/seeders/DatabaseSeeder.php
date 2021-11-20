<?php

namespace Database\Seeders;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');
        User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        Trip::factory()->create();
        Trip::factory()->create();
        Trip::factory()->create();
        Trip::factory()->create();
        Trip::factory()->create();
    }
}
