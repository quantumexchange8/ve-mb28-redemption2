<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    use HasFactory;

    protected $fillable = [
        'redemption_code',
        'meta_login',
        'acc_name',
        'license_name',
        'expired_date',
        'status',
    ];

    protected $casts = [
        'expired_date' => 'datetime',
    ];

    
}
