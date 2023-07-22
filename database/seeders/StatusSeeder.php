<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        \App\Models\Status::factory()->count(5)->sequence([
            'name' => 'Open',
            'is_closed' => 0,
            'status'=>1
        ],[
            'name' => 'In development',
            'is_closed' => 0,
            'status'=>1
        ],[
            'name' => 'In QA',
            'is_closed' => 0,
            'status'=>1
        ],[
            'name' => 'Done',
            'is_closed' => 1,
            'status'=>1
        ],[
            'name' => 'Reassigned',
            'is_closed' => 0,
            'status'=>1
        ])->create();

    }
}
