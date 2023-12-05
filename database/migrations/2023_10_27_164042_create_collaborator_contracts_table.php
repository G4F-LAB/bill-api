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
        Schema::create('collaborator_contracts', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedInteger('collaborator_id');
            $table->unsignedInteger('contract_id');
            $table->timestamps();
        });

        Schema::table('collaborator_contracts', function(Blueprint $table){
            $table->foreign('collaborator_id')->references('id')->on('collaborators');
            $table->foreign('contract_id')->references('id')->on('contracts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('collaborator_contracts', function (Blueprint $table) {
            $table->dropForeign('collaborator_contracts_contract_foreign');
            $table->dropForeign('collaborator_contracts_id_collaborator_foreign');
        });
        Schema::dropIfExists('collaborator_contracts');
    }
};
