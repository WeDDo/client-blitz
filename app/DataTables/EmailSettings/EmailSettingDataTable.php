<?php

namespace App\DataTables\EmailSettings;

use App\DataTables\BaseDataTable;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class EmailSettingDataTable extends BaseDataTable
{
    public function getData(): LengthAwarePaginator
    {
        return User::query()
            ->find(auth()->id())
            ->emailSettings()
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }
}
