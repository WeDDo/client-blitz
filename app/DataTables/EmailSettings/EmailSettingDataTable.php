<?php

namespace App\DataTables\EmailSettings;

use App\DataTables\BaseDataTable;

class EmailSettingDataTable extends BaseDataTable
{
    public function getData()
    {
        return auth()->user()->emailSettings()->paginate(50);
    }
}
