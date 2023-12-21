<?php

namespace App\Enums;

enum KycStatus: int
{
    case Draft = 0;
    case Verified = 1;
    case Pending = 2;
    case Failed = 3;
}
