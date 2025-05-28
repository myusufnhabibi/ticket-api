<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aduan extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'title',
        'description',
        'status',
        'priority',
        'completed_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aduan_balasans()
    {
        return $this->hasMany(AduanBalasan::class);
    }
}
