<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
protected $fillable = [
    'intent_id',
    'method',
    'amount',
    'status',
    'buyer_id',
];

}
