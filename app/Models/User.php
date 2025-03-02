<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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

    public function emailSettings(): HasMany
    {
        return $this->hasMany(EmailSetting::class, 'created_by');
    }

    public function activeSmtpEmailSetting(): HasMany
    {
        return $this->hasMany(EmailSetting::class, 'created_by')
            ->where('protocol', EmailSetting::$smtpProtocol)
            ->where('active', true);
    }

    public function activeImapEmailSetting(): HasMany
    {
        return $this->hasMany(EmailSetting::class, 'created_by')
            ->where('protocol', EmailSetting::$imapProtocol)
            ->where('active', true);
    }

    public function emailMessages(): HasMany
    {
        return $this->hasMany(EmailMessage::class, 'created_by');
    }

    public function emailInboxSettings(): HasMany
    {
        return $this->hasMany(EmailInboxSetting::class, 'created_by');
    }
}
