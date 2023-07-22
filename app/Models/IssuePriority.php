<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssuePriority extends Model
{
    use HasFactory;
    protected $table='issue_priorities';
    protected $primaryKey = 'id';
}
