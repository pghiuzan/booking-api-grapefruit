<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripLocation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'city',
        'country',
    ];
}
