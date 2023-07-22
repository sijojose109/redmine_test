<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;
    protected $table='issues';
    protected $primaryKey = 'id';

    public function project()
    {
        return $this->hasOne(Project::class, 'id', 'project_id');   
    }

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'project_id');   
    }

    public function status()
    {
        return $this->hasOne(Status::class, 'id', 'status');   
    }

    public function tracker()
    {
        return $this->hasOne(Tracker::class, 'id', 'tracker');   
    }
}