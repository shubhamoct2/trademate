<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Enums\TxnStatus;
use App\Enums\AutoTaskType;
use Carbon\Carbon;

class AutoTask extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'type' => AutoTaskType::class,
        'status' => TxnStatus::class,
    ];
}
