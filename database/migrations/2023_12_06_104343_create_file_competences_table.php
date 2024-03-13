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
        Schema::create('file_competences', function (Blueprint $table) {
            $table->id();
            $table->string('competence');
        });

        Schema::table('itens', function(Blueprint $table){
            $table->foreign('file_competence_id')->references('id')->on('file_competences');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('itens', function (Blueprint $table) {
            $table->dropForeign('itens_id_file_competence_foreign');
        });

        Schema::dropIfExists('file_competences');
    }
};
