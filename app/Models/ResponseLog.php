<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResponseLog extends Model
{
    protected $fillable = ['report_id', 'actor_id', 'from_status', 'to_status', 'note'];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
