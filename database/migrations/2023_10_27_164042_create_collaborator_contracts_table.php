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
            $table->smallInteger('id_colaborador');
            $table->string('id_contrato')->unique();
            $table->primary(['id_colaborador', 'id_contrato']);
            $table->timestamps();
        });

        Schema::table('collaborator_contracts', function(Blueprint $table){
            $table->foreign('id_colaborador')->references('id_colaborador')->on('collaborators');
            $table->foreign('id_contrato')->references('id_contrato')->on('contracts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collaborator_contracts');
    }
};
