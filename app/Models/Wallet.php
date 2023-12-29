<?php

namespace App\Models;

use App\Enums\WalletStatus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'currency',
        'address',
        'status',
    ];

    protected $casts = [
        'status' => WalletStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
