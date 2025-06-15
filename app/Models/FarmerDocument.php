<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FarmerDocument extends Model
{
    protected $fillable = [
        'farmer_id',
        'document_path',
        'status',
        'admin_note',
    ];

    public function farmer()
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }
}
