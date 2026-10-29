<?php

namespace App\Enums;

enum LandType: string
{
    case Gambut = 'gambut';
    case Hutan = 'hutan';
    case Semak = 'semak';
    case Kebun = 'kebun';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
