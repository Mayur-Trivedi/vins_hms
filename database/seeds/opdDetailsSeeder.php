<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class opdDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
          //
        DB::table('opd_details')->truncate();

        DB::table('opd_details')->insert([
          ['patient_id' => 1,
          'uhid_no'=>'VN18001',
          'admit_datetime' => NULL,
          'discharge_datetime' => NULL,
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),],

        ]);
    }
}
