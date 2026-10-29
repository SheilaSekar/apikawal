<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportPhoto extends Model
{
    protected $fillable = ['report_id', 'mime', 'data_base64', 'size_bytes'];

    protected $hidden = ['data_base64'];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
