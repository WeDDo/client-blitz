<?php

namespace App\Http\Controllers;

use App\DataTables\EmailMessages\EmailMessageDataTable;
use App\Services\EmailMessageService;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class EmailMessageController extends Controller
{
    public function __construct(private EmailMessageService $emailMessageService)
    {
    }

    public function index(): Response
    {
        return inertia('modules/email-messages/index', [
            'data_table' => (new EmailMessageDataTable())->getData(),
        ]);
    }

    public function getEmailsUsingImap(): RedirectResponse
    {
        $emailMessages = $this->emailMessageService->getEmailsUsingImap();

        return back()->with('success', 'success');
    }
}
