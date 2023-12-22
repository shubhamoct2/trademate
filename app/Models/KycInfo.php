<?php

namespace App\Models;

use App\Enums\KycStatus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'data',
        'status',
    ];

    protected $casts = [
        'data' => 'array',
        'status' => KycStatus::class,
    ];
}
