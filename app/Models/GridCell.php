<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GridCell extends Model
{
    protected $fillable = ['cell_key', 'min_lat', 'min_lng', 'max_lat', 'max_lng', 'hotspot_count', 'report_count', 'score', 'rank_position', 'prone_level'];
}
