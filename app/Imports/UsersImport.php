<?php

namespace App\Imports;

use App\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UsersImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!User::where('user_class', $row[1])->where('user_dormitory', $row[2])->where('user_name', $row[3])->where('user_sex', $row[4])->where('user_phone', $row[5])->first()) {
                User::insert([
                    'user_class' => $row[1],
                    'user_dormitory' => $row[2],
                    'user_name' => $row[3],
                    'user_sex' => $row[4],
                    'user_phone' => $row[5],
                ]);
            }
        }
    }
}
