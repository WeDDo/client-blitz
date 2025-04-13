<?php

namespace App\DataTables\EmailMessages;

use App\DataTables\BaseDataTable;
use App\Models\DataTable;
use App\Models\EmailInboxSetting;
use App\Models\EmailMessage;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class EmailMessageDataTable extends BaseDataTable
{
    public function getData(): LengthAwarePaginator
    {
        $sessionKey = "datatable_page_$this->name";
        $page = request('page');

        if (!$page) {
            $page = session($sessionKey, 1);
        }

        session([$sessionKey => $page]);

        return EmailMessage::query()
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage, page: $page);
    }
}
