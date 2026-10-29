<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiskAreaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['cell_key' => $this->cell_key, 'bbox' => ['south' => $this->min_lat, 'west' => $this->min_lng, 'north' => $this->max_lat, 'east' => $this->max_lng], 'score' => (float) $this->score, 'rank' => $this->rank_position, 'level' => $this->prone_level, 'hotspot_count' => $this->hotspot_count, 'report_count' => $this->report_count];
    }
}
