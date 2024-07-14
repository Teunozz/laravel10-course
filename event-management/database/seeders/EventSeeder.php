<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        Event::factory()->count(200)
            ->state(function () use ($users) {
                return ['user_id' => $users->random()->id];
            })
            ->create();
    }
}
