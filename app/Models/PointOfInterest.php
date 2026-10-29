<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointOfInterest extends Model
{
    protected $table = 'points_of_interest';

    public $timestamps = false;

    protected $fillable = ['name', 'kind', 'lat', 'lng', 'is_simulated'];

    protected function casts(): array
    {
        return ['is_simulated' => 'boolean'];
    }
}
