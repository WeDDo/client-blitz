<?php

namespace App\Services;

use App\Models\EmailInboxSetting;
use Illuminate\Support\Facades\DB;
use Webklex\IMAP\Facades\Client;
use Webklex\PHPIMAP\Support\FolderCollection;

class EmailInboxSettingService
{
    public function store(array $data): EmailInboxSetting
    {
        return EmailInboxSetting::query()->create($data);
    }

    public function update(array $data, EmailInboxSetting $emailInboxSetting): void
    {
        $emailInboxSetting->update($data);
    }

    public function destroy(EmailInboxSetting $emailInboxSetting): void
    {
        $emailInboxSetting->delete();
    }

    public function getInboxesImap(): array
    {
        $initialImapEmailSetting = auth()->user()->imapEmailSettings()->first();
        if (!$initialImapEmailSetting) {
            return [[], []];
        }

        $imapConfig = (new EmailSettingService())->setImapEmailConfig($initialImapEmailSetting);

        $client = Client::make($imapConfig);
        $client->connect();

        $folderNames = $this->extractMailboxNames($client->getFolders());

        $formattedFolders = [
            [],
            [],
        ];

        $emailInboxSettingNames = auth()->user()->emailInboxSettings()->pluck('read_from_inbox_name');

//        dd($emailInboxSettingNames);

        foreach ($folderNames as $key => $folderName) {
            if (!$emailInboxSettingNames->contains($folderName)) {
                $formattedFolders[0][] = [
                    'id' => $key,
                    'name' => $folderName,
                ];
            }
        }

        return [
            'imap_email_setting_id' => $initialImapEmailSetting?->id,
            'folders' => $formattedFolders,
        ];
    }

    protected function extractMailboxNames(FolderCollection $folders): array
    {
        $mailboxNames = [];

        foreach ($folders as $folder) {
            if ($folder->name && $folder->name !== '[Gmail]') {
                $mailboxNames[] = $folder->name;
            }

            if ($folder->hasChildren()) {
                $mailboxNames = array_merge($mailboxNames, $this->extractMailboxNames($folder->children));
            }
        }

        return $mailboxNames;
    }

    public function createInboxes(array $data): void
    {
        DB::transaction(function () use ($data) {
            foreach ($data['folders'][1] as $inbox) {
                $this->store([
                    'name' => $inbox['name'],
                    'read_from_inbox_name' => $inbox['name'],
                    'email_setting_id' => $data['imap_email_setting_id'],
                ]);
            }
        });
    }
}


