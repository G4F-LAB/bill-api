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
            $table->id('id_contract');
            $table->string('contract',50)->unique();
            $table->string('name',150);
            $table->boolean('contractual_situation');
            $table->unsignedBigInteger('id_manager')->nullable();
            $table->timestamps();
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->foreign('id_manager')->references('id_collaborator')->on('collaborators');
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
