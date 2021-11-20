<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trip extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slug',
        'title',
        'description',
        'start_date',
        'end_date',
        'location_id',
        'price',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(TripLocation::class, 'location_id');
    }
}
