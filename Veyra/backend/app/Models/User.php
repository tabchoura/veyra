<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Auth
        'email',
        'password',
        'email_verified',
        'email_verification_token',
        
        // Company
        'company_name',
        'logo_url',
        'tva_number',
        
        // Contact
        'first_name',
        'last_name',
        'function',
        
        // Address
        'address1',
        'address2',
        'postal_code',
        'country',
        
        // Business
        'sector',
        'sector_other',
        'partner',
        'partner_other',
        
        // Preferences
        'language',
            'status',          // 👈 ICI

        // Tracking
        'ip_signup',
        'signup_at',
        
        // Admin
        'user_type',
        'dpp_access',
        'quota_dpp',
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified' => 'boolean',
            'dpp_access'     => 'integer',   // 0 / 1 / 2
            'quota_dpp'      => 'integer',
            'signup_at'      => 'datetime',
            'password'       => 'hashed',
        ];
    }

    /**
     * Accessor: full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if user is a super user.
     */
    public function isSuperUser(): bool
    {
        return $this->user_type === 'SUPER_USER';
    }

    /**
     * Check if user has any DPP access (full or read-only).
     */
    public function hasDppAccess(): bool
    {
        return in_array($this->dpp_access, [1, 2], true);
    }
}
