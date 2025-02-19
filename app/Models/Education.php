<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Education extends Model
{
    protected $fillable = ['name', 'slug', 'image', 'description', 'location', 'start_date', 'end_date', 'link'];

    use HasFactory;
}
