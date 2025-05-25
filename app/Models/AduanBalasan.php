<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AduanBalasan extends Model
{
    protected $fillable = [
        'aduan_id',
        'user_id',
        'content',
    ];

    public function aduan()
    {
        return $this->belongsTo(Aduan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
