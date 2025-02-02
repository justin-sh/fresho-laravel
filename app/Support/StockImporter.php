<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithColumnLimit;
use Maatwebsite\Excel\Concerns\WithSkipDuplicates;

class StockImporter implements ToModel, WithSkipDuplicates, WithCalculatedFormulas, SkipsEmptyRows, WithColumnLimit
{

    public function model(array $row)
    {
        if (empty($row[0])) {
            return null;
        }

        return [
            'name'=>$row[0],
            'stock'=>$row[1],
        ];
    }

    public function isEmptyWhen(array $row): bool
    {
        return empty($row[0]);
    }

    public function endColumn(): string
    {
        return "F";
    }
}
