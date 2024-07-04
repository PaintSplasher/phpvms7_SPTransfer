<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Change the column names
        if (Schema::hasTable('sptransfer')) {
            Schema::table('sptransfer', function (Blueprint $table) {
                $table->renameColumn('hub_initial', 'hub_initial_id');
                $table->renameColumn('hub_request', 'hub_request_id');
            });
        }
    }
};
