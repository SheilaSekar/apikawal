<?php

namespace App\Enums;

enum PriorityLevel: string
{
    case Rendah = 'rendah';
    case Sedang = 'sedang';
    case Tinggi = 'tinggi';
    case Kritis = 'kritis';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
