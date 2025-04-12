<?php

namespace App\Services;

use App\Models\EmailSetting;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Throwable;
use Webklex\IMAP\Facades\Client;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;

class EmailSettingService
{
    public function show(EmailSetting $emailSetting): EmailSetting
    {
        $emailSetting->password = Crypt::decryptString($emailSetting->password);

        return $emailSetting;
    }

    public function store(array $data): EmailSetting
    {
        $data['password'] = Crypt::encryptString($data['password']);

        $emailSetting = auth()->user()->emailSettings()->create($data);

        $this->checkActiveEmailSettings($data, $emailSetting);

        return $emailSetting;
    }

    public function update(array $data, EmailSetting $emailSetting): void
    {
        if (isset($data['password'])) {
            $data['password'] = Crypt::encryptString($data['password']);
        }

        $emailSetting->update($data);

        $this->checkActiveEmailSettings($data, $emailSetting);
    }

    public function checkActiveEmailSettings(array $data, EmailSetting $emailSetting): void
    {
        if (isset($data['active']) && $data['active']) {
            auth()->user()->emailSettings()
                ->where('id', '<>', $emailSetting->id)
                ->where('protocol', $emailSetting->protocol)
                ->update(['active' => false]);
        }
    }

    public function copy(EmailSetting $emailSetting): EmailSetting
    {
        $newEmailSetting = $emailSetting->replicate();
        $newEmailSetting->active = false;
        $newEmailSetting->save();
        return $newEmailSetting;
    }

    public function checkConnection(EmailSetting $emailSetting): bool
    {
        if ($emailSetting->protocol === EmailSetting::$imapProtocol) {
            $this->checkImapConnection($emailSetting);
        } elseif ($emailSetting->protocol === EmailSetting::$smtpProtocol) {
            $this->checkSmtpConnection($emailSetting);
        } else {
            throw new \Exception('unsupported_protocol', 400);
        }

        return true;
    }

    public function checkImapConnection(EmailSetting $emailSetting): void
    {
        try {
            $client = Client::make($this->setImapEmailConfig($emailSetting));
            $client->connect();
        } catch (Throwable $e) {
            dd($e);
            throw new \Exception('Imap check failed', 400);
        }
    }

    public function checkSmtpConnection(EmailSetting $emailSetting): void
    {
        $smtpConfig = $this->setSmtpEmailConfig($emailSetting);

        $transport = new EsmtpTransport(
            $smtpConfig['host'],
            $smtpConfig['port'],
            false
        );

        $transport->setUsername($smtpConfig['username']);
        $transport->setPassword($smtpConfig['password']);

        try {
            $transport->start();
        } catch (Throwable) {
            throw new \Exception('Smtp check failed', 400);
        }
    }

    public function setImapEmailConfig(?EmailSetting $emailSetting = null): array
    {
        $user = auth()->user();
        if (!$emailSetting) {
            $emailSetting = $user->emailSettings()->where('protocol', EmailSetting::$imapProtocol)->first();
        }

        config([
            "imap.users.$user->id" => [
                'host' => $emailSetting->host,
                'port' => $emailSetting->port,
                'encryption' => $emailSetting->encryption,
                'validate_cert' => true,
                'username' => $emailSetting->username,
                'password' => Crypt::decryptString($emailSetting->password),
                'protocol' => $emailSetting->protocol,
            ]
        ]);

        return config("imap.users.$user->id");
    }

    public function setSmtpEmailConfig(?EmailSetting $emailSetting = null): array
    {
        $user = auth()->user();
        if (!$emailSetting) {
            $emailSetting = $user->activeImapEmailSetting()->first();
        }

        $config = [
            'transport' => 'smtp',
            'host' => $emailSetting->host,
            'port' => $emailSetting->port,
            'encryption' => $emailSetting->encryption,
            'username' => $emailSetting->username,
            'password' => Crypt::decryptString($emailSetting->password),
            'auth_mode' => 'LOGIN',
//            'authentication' => 'LOGIN',
//            'timeout' => null,
//            'validate_cert' => $emailSetting->validate_cert ?? true,
        ];

        config(["mail.mailers.smtp.users.$user->id" => $config]);

        return config("mail.mailers.smtp.users.$user->id");
    }
}


