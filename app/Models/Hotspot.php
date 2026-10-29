<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotspot extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['lat', 'lng', 'confidence', 'detected_at', 'source', 'is_simulated'];

    protected function casts(): array
    {
        return ['detected_at' => 'datetime', 'is_simulated' => 'boolean'];
    }
}
