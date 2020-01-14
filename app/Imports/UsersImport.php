<?php

namespace App\Imports;

use App\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UsersImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $importDataCount = count($rows);
        $importSuccessCount = 0;
        $importFailCount = 0;
        foreach ($rows as $row) {
            if (!User::where('user_class', $row[0])->where('user_dormitory', $row[1])->where('user_name', $row[2])->where('user_sex', $row[3])->where('user_phone', $row[4])->first()) {
                User::insert([
                    'user_class' => $row[0],
                    'user_dormitory' => $row[1],
                    'user_name' => $row[2],
                    'user_sex' => $row[3],
                    'user_phone' => $row[4],
                ]);
                $importSuccessCount += 1;
            } else {
                $importFailCount += 1;
            }
        }
        throw new \Exception("一共有：$importDataCount 条，成功：$importSuccessCount 条，失败：$importFailCount 条", 200);
    }
}
