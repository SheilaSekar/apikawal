<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbuseFlag extends Model
{
    protected $fillable = ['report_id', 'flagged_by', 'reason', 'resolved'];

    protected function casts(): array
    {
        return ['resolved' => 'boolean'];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'flagged_by');
    }
}
