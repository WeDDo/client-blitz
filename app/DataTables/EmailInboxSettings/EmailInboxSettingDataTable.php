<?php

namespace App\DataTables\EmailInboxSettings;

use App\DataTables\BaseDataTable;
use App\Models\DataTable;
use App\Models\EmailInboxSetting;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class EmailInboxSettingDataTable extends BaseDataTable
{
    public function getData(): LengthAwarePaginator
    {
        $sessionKey = "datatable_page_$this->name";
        $page = request('page');

        if (!$page) {
            $page = session($sessionKey, 1);
        }

        session([$sessionKey => $page]);

        return EmailInboxSetting::query()
            ->orderBy('name', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage, page: $page);
    }
}
