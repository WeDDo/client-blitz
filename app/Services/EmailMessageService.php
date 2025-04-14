<?php

namespace App\Services;

use App\Mail\Email;
use App\Models\EmailInboxSetting;
use App\Models\EmailMessage;
use App\Models\EmailSetting;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Webklex\IMAP\Facades\Client;

class EmailMessageService
{
//    protected AttachmentService $attachmentService;
    protected EmailSettingService $emailSettingService;

//
    public function __construct()
    {
//        $this->attachmentService = new AttachmentService();
        $this->emailSettingService = new EmailSettingService();
    }

//
    public function show(EmailMessage $emailMessage): EmailMessage
    {
        $this->markThreadAsSeen($emailMessage);

//        $emailMessage->withAdditionalData();

        return $emailMessage;
    }
//
//    public function getAdditionalData(): array
//    {
//        return [
//            'email_inbox_settings' => EmailInboxSetting::where('created_by', auth()->user()->id)->pluck('name'),
//        ];
//    }
//
//    public function sendEmailUsingSMTP(
//        array         $toEmails = [],
//        array         $ccEmails = [],
//        array         $bccEmails = [],
//        ?string       $replyHtml = null,
//        ?EmailMessage $emailMessage = null,
//        ?string       $subject = null,
//        array         $files = [],
//    ): void
//    {
//        $emailSetting = auth()->user()->activeSmtpEmailSetting()->first();
//
//        $this->emailSettingService->setSmtpEmailConfig($emailSetting);
//
//        $email = (new Email([
//            'text' => $replyHtml
//        ], $emailMessage, files: $files));
//
//        DB::transaction(function () use ($email, $toEmails, $ccEmails, $bccEmails, $emailMessage, $replyHtml, $subject) {
//            Mail::mailer('smtp.users.' . auth()->user()->id)
//                ->to(['mantuxas001@gmail.com'])
////                ->to($toEmails)
////                ->cc($ccEmails)
////                ->bcc($bccEmails)
//                ->send($email);
//        });
//    }

    public function getEmailsUsingIMAP(): Collection
    {
        return DB::transaction(function () {
            $imapConfig = $this->emailSettingService->setImapEmailConfig();

            $emailInboxSettings = EmailInboxSetting::query()
                ->where('created_by', auth()->id())
                ->get();
            $allMessages = collect();

            foreach ($emailInboxSettings as $inboxSetting) {
                $inboxMessages = $this->fetchEmailsFromFolder($imapConfig, $inboxSetting->read_from_inbox_name, 7);
                $inboxMessages = $inboxMessages->map(function ($message) use ($inboxSetting) {
                    $message->folder_name = $inboxSetting->read_from_inbox_name;
                    return $message;
                });
                $allMessages = $allMessages->merge($inboxMessages);
            }

            $allMessages = $allMessages->toArray();

            usort($allMessages, function ($a, $b) {
                return strtotime($a->getDate()) - strtotime($b->getDate());
            });

            $allMessages = collect($allMessages);

            return $this->processEmails($allMessages);
        });
    }

    protected function fetchEmailsFromFolder(array $imapConfig, string $folderName, int $days): Collection
    {
        $client = Client::make($imapConfig);
        $client->connect();

        $folder = $client->getFolderByName($folderName);
        $messages = $folder?->query()?->since(now()->subDays($days))->get();

        return $messages ?? collect();
    }

    protected function processEmails(Collection $messages): Collection
    {
        $existingEmailMessages = EmailMessage::query()
            ->where('created_by', auth()->user()->id)
            ->get();
        $existingEmailMessageIds = $existingEmailMessages->pluck('message_id');

        $createdEmailMessages = collect();
        $emailMessageMap = [];

        foreach ($messages as $message) {
            $messageId = $message->getMessageId()->get();

            if (!$existingEmailMessageIds->contains($messageId)) {
                $encodedSubject = $message->getSubject()->get();
                $encodedSubject = imap_mime_header_decode($encodedSubject);

                $decodedSubject = '';
                foreach ($encodedSubject as $part) {
                    $decodedSubject .= $part->text;
                }
                $subject = $decodedSubject;

//                $purifierConfig = HTMLPurifier_Config::createDefault();
//                $purifier = new HTMLPurifier($purifierConfig);
//                $bodyHtml = $purifier->purify($message->getHTMLBody());

//                $fromEmails = collect($message->getFrom()?->get())->map(fn($fromEmailObject) => $fromEmailObject->mail);
//                $toEmails = collect($message->getTo()?->get())->map(fn($toEmailObject) => $toEmailObject->mail);
//                $ccEmails = collect($message->getCc()?->get())->map(fn($ccEmailObject) => $ccEmailObject->mail);
//                $bccEmails = collect($message->getBcc()?->get())->map(fn($bccEmailObject) => $bccEmailObject->mail);
//                $replyToEmails = collect($message->getReplyTo()?->get())->map(fn($replyToEmailObject) => $replyToEmailObject->mail);

                $emailExtractor = function ($addressList) {
                    return collect($addressList ?? [])
                        ->map(function ($address) {
                            return $address->mail ?? null;
                        })
                        ->filter()
                        ->implode(',');
                };

                $fromEmails = $emailExtractor($message->getFrom()?->toArray());
                $toEmails = $emailExtractor($message->getTo()?->toArray());
                $ccEmails = $emailExtractor($message->getCc()?->toArray());
                $bccEmails = $emailExtractor($message->getBcc()?->toArray());
                $bccEmails = $emailExtractor($message->getBcc()?->toArray());
                $replyToEmails = $emailExtractor($message->getReplyTo()?->toArray());

                $inReplyTo = $message->getInReplyTo();
                $inReplyTo = $inReplyTo ? trim($inReplyTo, '<>') : null;

                $replyToEmailMessage = null;
                if ($inReplyTo) {
                    $replyToEmailMessage = EmailMessage::query()
                        ->where('message_id', $inReplyTo)
                        ->first();
                }

                $emailMessage = EmailMessage::query()
                    ->create([
                        'message_id' => $messageId,
                        'subject' => $subject,
                        'from' => $fromEmails ?: null,
                        'to' => $toEmails ?: null,
                        'cc' => $ccEmails ?: null,
                        'bcc' => $bccEmails ?: null,
                        'reply_to' => $replyToEmails ?: null,
                        'date' => $message->getDate()?->get()?->toDateTimeString() ?? null,
                        'body_text' => $message->getTextBody() ?? null,
                        'body_html' => $message->getHTMLBody(),
                        'is_seen' => $message->getFlags()->has('Seen') ?? false,
                        'is_flagged' => $message->getFlags()->has('Flagged') ?? false,
                        'is_answered' => $message->getFlags()->has('Answered') ?? false,
                        'folder' => $message->folder_name ?? null,
                        'reply_to_email_message_id' => $replyToEmailMessage?->id,
                    ]);

                $emailMessageMap[$messageId] = $emailMessage;

//                foreach ($message->getAttachments() as $attachment) {
//                    $this->attachmentService->store([
//                        'name' => $attachment->name,
//                        'content' => $attachment->content,
//                        'related_name' => 'email_messages',
//                        'related_id' => $emailMessage->id,
//                    ]);
//                }

                $createdEmailMessages->push($emailMessage);
            }
        }

        foreach ($messages as $message) {
            $messageId = $message->getMessageId()?->get();
            $messageId = $messageId ? trim($messageId, '<>') : null;

            $inReplyTo = $message->getInReplyTo();
            $inReplyTo = $inReplyTo ? trim($inReplyTo, '<>') : null;

            if ($messageId && $inReplyTo && isset($emailMessageMap[$inReplyTo]) && isset($emailMessageMap[$messageId])) {
                $emailMessageMap[$messageId]->update([
                    'reply_to_email_message_id' => $emailMessageMap[$inReplyTo]->id,
                ]);
            }
        }

        return $createdEmailMessages;
    }

    protected function markThreadAsSeen(EmailMessage $emailMessage): void
    {
        if (!$emailMessage->is_seen) {
            $emailMessage->update(['is_seen' => true]);
        }

        if ($emailMessage->replyToEmailMessage) {
            $this->markThreadAsSeen($emailMessage->replyToEmailMessage);
        }
    }
}


