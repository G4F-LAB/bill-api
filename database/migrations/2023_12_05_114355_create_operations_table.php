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
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedInteger('manager_id')->nullable();
            $table->unsignedInteger('reference')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('operations', function (Blueprint $table) {
            $table->foreign('manager_id')->references('id')->on('collaborators');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->foreign('operation_id')->references('id')->on('operations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign('contracts_id_operation_foreign');
        });

        Schema::table('operations', function (Blueprint $table) {
            $table->dropForeign('contracts_id_manager_foreign');
        });
        Schema::dropIfExists('operations');
    }
};
