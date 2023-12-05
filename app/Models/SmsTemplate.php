<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function getMessageBodyAttribute($value)
    {
        return strip_tags($value);
    }
}
