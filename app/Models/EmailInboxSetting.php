<?php

namespace App\Models;

use App\Traits\CreateUpdateUserTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailInboxSetting extends Model
{
    use HasFactory, CreateUpdateUserTrait;

    protected $fillable = [
        'name',
        'read_from_inbox_name',
        'email_setting_id',
        'auto_set_is_seen',
    ];

//    public function getAdditionalData(): array
//    {
//        return [
//            'autocomplete_data' => $this->getAutocompleteData(),
//        ];
//    }

//    public function getAutocompleteData(): array
//    {
//        $config = [
//            'email_setting_id' => [
//                'table' => 'email_settings',
//                'search_fields' => ['username', 'host'],
//            ]
//        ];
//
//        $autocompleteData = [];
//        foreach ($config as $key => $settings) {
//            $autocompleteData[$key] = [
//                'table' => $settings['table'],
//                'search_fields' => $settings['search_fields'],
////                'item' => (new AutocompleteService())->searchById(
////                    $settings['table'],
////                    $this->email_setting_id,
////                    $settings['search_fields']
////                ),
//            ];
//        }
//
//        return $autocompleteData;
//    }

    public function emailSetting(): BelongsTo
    {
        return $this->belongsTo(EmailSetting::class);
    }

    public function emailMessages(): HasMany
    {
        return $this->hasMany(EmailMessage::class);
    }
}
