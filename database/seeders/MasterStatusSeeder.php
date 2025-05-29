<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MasterStatus;

class MasterStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $masterStatus = collect(
        [
            [
                'status_name' => 'CreateCase',
            ],
            [
                'status_name' => 'Indenting',
            ],
            [
                'status_name' => 'Tendering',
            ],
            [
                'status_name' => 'Miscellaneous',
            ],
        ]);

        $masterStatus->each(function($masterStatu){
            MasterStatus::create($masterStatu);
        });
    }
}
