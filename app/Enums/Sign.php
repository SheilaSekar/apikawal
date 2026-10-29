<?php

namespace App\Enums;

enum Sign: string
{
    case Asap = 'asap';
    case Api = 'api';
    case Bara = 'bara';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
