<?php

namespace App\DataTables;

abstract class BaseDataTable
{
    protected string $name;
    protected int $perPage = 5;

    public function __construct()
    {
        $this->name = static::class;
    }
}
