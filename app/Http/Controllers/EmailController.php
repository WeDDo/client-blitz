<?php

namespace App\Http\Controllers;

use App\DataTables\EmailSettings\EmailSettingDataTable;
use Inertia\Response;

class EmailController extends Controller
{
    public function index(): Response
    {
        return inertia('modules/email-messages/index', [
            'data_table' => (new EmailSettingDataTable())->getData(),
        ]);
    }
}
