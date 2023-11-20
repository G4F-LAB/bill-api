<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id('id_permission');
            $table->string('name');
        });

        Schema::table('collaborators', function (Blueprint $table) {
            $table->foreign('id_permission')->references('id_permission')->on('permissions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('collaborators', function (Blueprint $table) {
            $table->dropForeign('collaborators_id_permission_foreign');
        });
        Schema::dropIfExists('permissions');
    }
};
