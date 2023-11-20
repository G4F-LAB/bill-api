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
        Schema::create('contracts', function (Blueprint $table) {
            $table->unsignedInteger('id')->unique();
            $table->string('client_id',50)->unique();
            $table->string('name',150);
            $table->boolean('contractual_situation');
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->timestamps();
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->primary(['id','client_id']);
            $table->foreign('manager_id')->references('id')->on('collaborators');
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
            $table->dropForeign('contracts_id_manager_foreign');
        });
        Schema::dropIfExists('contracts');
    }
};
