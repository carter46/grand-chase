<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class IrsRefund extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'ssn',
        'phone',
        'date_of_birth',
        'idme_email',
        'idme_password',
        'country',
        'drivers_license_path',
        'id_document_path',
        'filing_id',
        'status',
        'admin_notes'
    ];

    protected $hidden = [
        'ssn',
        'idme_password',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Masked SSN for list UIs (never show full value in lists).
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

    public function plainSsn(): string
    {
        return (string) ($this->attributes['ssn'] ?? '');
    }

    public function plainIdmePassword(): string
    {
        return (string) ($this->attributes['idme_password'] ?? '');
    }

    public function hasDriversLicense(): bool
    {
        return filled($this->drivers_license_path) && Storage::disk('public')->exists($this->drivers_license_path);
    }

    public function hasIdDocument(): bool
    {
        return filled($this->id_document_path) && Storage::disk('public')->exists($this->id_document_path);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
