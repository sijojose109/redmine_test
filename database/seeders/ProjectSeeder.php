<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        \App\Models\Project::factory()->count(5)->sequence([
            'name' => 'Project1',
            'description' => 'Test 1',
            'status'=>1
        ],[
            'name' => 'Project2',
            'description' => 'Test 2',
            'status'=>1
        ],[
            'name' => 'Project3',
            'description' => 'Test 3',
            'status'=>1
        ],[
            'name' => 'Project4',
            'description' => 'Test 4',
            'status'=>1
        ],[
            'name' => 'Project5',
            'description' => 'Test 5',
            'status'=>1
        ])->create();
    }
}
