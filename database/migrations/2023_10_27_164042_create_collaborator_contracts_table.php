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
            $table->unsignedBigInteger('id_collaborator');
            $table->string('contract',50)->unique();
            $table->primary(['id_collaborator', 'contract']);
            $table->timestamps();
        });

        Schema::table('collaborator_contracts', function(Blueprint $table){
            $table->foreign('id_collaborator')->references('id_collaborator')->on('collaborators');
            $table->foreign('contract')->references('contract')->on('contracts');
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
