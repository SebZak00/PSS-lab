<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['nazwa' => 'Administrator', 'aktywna' => true],
            ['nazwa' => 'TeamLeader', 'aktywna' => true],
            ['nazwa' => 'Pracownik', 'aktywna' => true],
        ]);
    }
}