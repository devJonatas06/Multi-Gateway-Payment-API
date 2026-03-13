<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'client_id',
        'gateway',
        'external_id',
        'status',
        'amount',
        'card_last_numbers'
    ];
}