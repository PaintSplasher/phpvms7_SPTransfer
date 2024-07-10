<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Insert the column reject_reason
        if (Schema::hasTable('sptransfer')) {
            Schema::table('sptransfer', function (Blueprint $table) {
                $table->mediumText('reject_reason')->nullable()->after('reason');
            });
        }
    }
};