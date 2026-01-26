<?php

namespace App\Enums;

enum ItemStatus: string
{
    case AVAILABLE = 'available';
    case TRADING = 'trading';
    case SOLD = 'sold';
}
