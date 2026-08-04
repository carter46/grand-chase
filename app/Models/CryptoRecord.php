<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CryptoRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'source',
        'dest',
        'amount',
        'quantity',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
