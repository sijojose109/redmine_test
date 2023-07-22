<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        \App\Models\Category::factory()->count(5)->sequence([
            'name' => 'Project 1 Category 1',
            'status' => 1,
            'project_id'=>1
        ],[
            'name' => 'Project 1 Category 2',
            'status' => 1,
            'project_id'=>1
        ],[
            'name' => 'Project 2 Category 1',
            'status' => 1,
            'project_id'=>2
        ],[
            'name' => 'Project 2 Category 2',
            'status' => 1,
            'project_id'=>2
        ],[
            'name' => 'Project 2 Category 3',
            'status' => 1,
            'project_id'=>2
        ])->create();
    }
}
