<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up()
    {
        // Insert the column reject_reason
        if (Schema::hasTable('sptransfer_settings')) {        
            DB::table('sptransfer_settings')->insert([
                'id'         => 2,
                'price'      => 0,
                'limit'      => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};