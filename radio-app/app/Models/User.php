<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PragmaRX\Google2FALaravel\Support\Authenticator;
use Illuminate\Support\Facades\Log;



class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'two_factor_code', // For 2FA
        'two_factor_expires_at', // For 2FA expiration
        'google2fa_secret',
        'two_factor_enabled',
        'role', // Assuming role is a string like 'patient', 'medecin', 'admin'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
    'two_factor_enabled' => 'boolean',
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

public function generateTwoFactorCode(): void
{
    $this->two_factor_code = rand(100000, 999999);
    $this->two_factor_expires_at = now()->addMinutes(10);
    $this->save();
}
public function resetTwoFactorCode()
{
    $this->two_factor_code = null;
    $this->two_factor_expires_at = null;
    $this->save();
}
// app/Models/User.php


public function getGoogle2faSecretAttribute($value)
{
      if (!$value) {
        return null;
    }

    try {
        return decrypt($value);
    } catch (\Exception $e) {
        Log::error('Erreur de déchiffrement 2FA : ' . $e->getMessage());
        return null;
    }
}

public function setGoogle2faSecretAttribute($value)
{
    $this->attributes['google2fa_secret'] = encrypt($value);
}


}
