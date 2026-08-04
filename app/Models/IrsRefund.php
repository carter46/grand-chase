<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IrsRefund extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'ssn',
        'idme_email',
        'idme_password',
        'country',
        'filing_id',
        'status',
        'admin_notes'
    ];

    protected $hidden = [
        'ssn',
        'idme_password',
    ];

    /**
     * Masked SSN for admin UI (never show full value in Blade).
     */
    public function maskedSsn(): string
    {
        $ssn = (string) ($this->attributes['ssn'] ?? '');
        if ($ssn === '') {
            return 'N/A';
        }
        $digits = preg_replace('/\D/', '', $ssn);
        if (strlen($digits) < 4) {
            return '***-**-****';
        }
        return '***-**-' . substr($digits, -4);
    }

    public function hasIdmePasswordOnFile(): bool
    {
        return filled($this->attributes['idme_password'] ?? null);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 