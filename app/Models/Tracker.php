<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracker extends Model
{
    use HasFactory;
    protected $table='trackers';
    protected $primaryKey = 'id';

    public function default_status()
    {
        return $this->hasOne(Status::class, 'id', 'default_status_id');   
    }
}