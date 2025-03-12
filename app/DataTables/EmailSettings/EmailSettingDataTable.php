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
        $page = request('page');
        if (!$page) {
            $dataTable = DataTable::query()
                ->where('name', $this->name)
                ->where('user_id', auth()->id())
                ->first();

            $page = $dataTable->page ?? 1;
        }

        DataTable::query()->updateOrCreate(
                [
                    'name' => $this->name,
                    'user_id' => auth()->id(),
                ],
                [
                    'page' => $page,
                ]
            );

        return User::query()
            ->find(auth()->id())
            ->emailSettings()
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage, page: $page);
    }
}
