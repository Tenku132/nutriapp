<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'admin_id', 'field', 'old_value', 'new_value',
    ];
  
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

}
