<?php

use App\Contracts\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

/**
 * Class CreateSPTransferTable
 */
class CreateSPTransferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sptransfer')) {
        Schema::create('sptransfer', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('hub_initial', 5);
            $table->string('hub_request', 5);
            $table->timestamps();
            $table->mediumText('reason')->nullable();
            $table->integer('state');
        });
        }

        if (!Schema::hasTable('sptransfer_settings')) {
            Schema::create('sptransfer_settings', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('price')->default(0);
                $table->unsignedInteger('limit')->default(0);
                $table->timestamps();
            });

            DB::table('sptransfer_settings')->insert([
                'price' => 0,
                'limit' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sptransfer');
        Schema::dropIfExists('sptransfer_settings');
    }
}
