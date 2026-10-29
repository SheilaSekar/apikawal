<?php

namespace App\Models;

use App\Enums\LandType;
use App\Enums\PriorityLevel;
use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'reporter_id', 'parent_id', 'lat', 'lng', 'land_type', 'signs', 'estimated_area_ha',
        'description', 'status', 'priority_score', 'priority_level', 'priority_breakdown', 'is_simulated', 'needs_moderation',
    ];

    protected function casts(): array
    {
        return [
            'land_type' => LandType::class,
            'status' => ReportStatus::class,
            'priority_level' => PriorityLevel::class,
            'signs' => 'array',
            'priority_breakdown' => 'array',
            'is_simulated' => 'boolean',
            'needs_moderation' => 'boolean',
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ReportPhoto::class);
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(Verification::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ResponseLog::class)->oldest();
    }

    public function flags(): HasMany
    {
        return $this->hasMany(AbuseFlag::class);
    }
}
