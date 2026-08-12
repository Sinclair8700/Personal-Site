<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Education extends Model
{
    protected $fillable = ['name', 'slug', 'image', 'description', 'location', 'start_date', 'end_date', 'link'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    use HasFactory;

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('nav.education'));
        static::deleted(fn () => Cache::forget('nav.education'));
    }

    /**
     * Cached list of education entries for the nav dropdown.
     */
    public static function navList()
    {
        return Cache::remember('nav.education', now()->addHours(6), fn () => static::all());
    }
}
