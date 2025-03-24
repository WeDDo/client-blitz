<?php

namespace App\DataTables\EmailSettings;

use App\DataTables\BaseDataTable;
use App\Models\DataTable;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class EmailSettingDataTable extends BaseDataTable
{
    public function getData(): LengthAwarePaginator
    {
        $sessionKey = "datatable_page_$this->name";
        $page = request('page');

        if (!$page) {
            $page = session($sessionKey, 1);
        }

        session([$sessionKey => $page]);

        return User::query()
            ->find(auth()->id())
            ->emailSettings()
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage, page: $page);
    }
}
