<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IssuePrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        \App\Models\IssuePriority::factory()->count(5)->sequence([
            'name' => 'Test priority 1',
            'is_default' => 0,
            'active'=>1
        ],[
            'name' => 'Test priority 2',
            'is_default' => 0,
            'active'=>1
        ],[
            'name' => 'Test priority 3',
            'is_default' => 0,
            'active'=>1
        ],[
            'name' => 'Test priority 4',
            'is_default' => 1,
            'active'=>1
        ],[
            'name' => 'Test priority 5',
            'is_default' => 0,
            'active'=>1
        ])->create();
    }
}
