<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrackerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        \App\Models\Tracker::factory()->count(5)->sequence([
                'name' => 'Test tracker 1',
                'default_status_id' => 1
            ],[
                'name' => 'Test tracker 2',
                'default_status_id' => 1
            ],[
                'name' => 'Test tracker 3',
                'default_status_id' => 1
            ],[
                'name' => 'Test tracker 4',
                'default_status_id' => 1
            ],[
                'name' => 'Test tracker 5',
                'default_status_id' => 1
            ]
        )->create();
    }
}
