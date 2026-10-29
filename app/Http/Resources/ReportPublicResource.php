<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportPublicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'lat' => round((float) $this->lat, 3),
            'lng' => round((float) $this->lng, 3),
            'status' => $this->status->value,
            'priority_level' => $this->priority_level->value,
            'priority_score' => $this->priority_score,
            'land_type' => $this->land_type->value,
            'created_at' => $this->created_at?->toIso8601String(),
            'is_simulated' => $this->is_simulated,
        ];
    }
}
