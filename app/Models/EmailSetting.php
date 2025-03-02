<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailSetting extends Model
{
    protected $fillable = [
        'name',
        'type',
        'host',
        'port',
        'encryption',
        'validate_cert',
        'username',
        'password',
        'protocol',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public static string $personalType = 'personal';
    public static string $sharedType = 'shared';

    public static function getTypes(): array
    {
        return [
            self::$personalType,
            self::$sharedType,
        ];
    }

    public static string $smtpProtocol = 'smtp';
    public static string $imapProtocol = 'imap';
    public static string $graphProtocol = 'graph'; // todo implement later

    public static function getProtocols(): array
    {
        return [
            self::$smtpProtocol,
            self::$imapProtocol,
        ];
    }

    public function getAdditionalData(): array
    {
        return [
//            'email_inbox_settings_data_table' => (new EmailSettingEmailInboxSettingDataTable([
//                'email_setting_id' => $this->id,
//            ]))->get(),
        ];
    }

    public function emailInboxSettings(): HasMany
    {
        return $this->hasMany(EmailInboxSetting::class);
    }
}
