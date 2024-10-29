
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Insert the column charge_type
	if (Schema::hasTable('sptransfer_settings')) {
            Schema::table('sptransfer_settings', function (Blueprint $table) {
                $table->unsignedInteger('charge_type')->default(0);
            });
        }
    }
};