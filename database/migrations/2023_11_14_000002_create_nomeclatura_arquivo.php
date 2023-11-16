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
        Schema::create('nomeclatura_arquivo', function (Blueprint $table) {
            $table->id('id_nomeclatura_arquivo');
            $table->string('nome_arquivo');
            $table->string('nomeclatura_padrao_arquivo');
            $table->timestamps();
        });

        
    }

    /**->constrained('nomeclatura_arquivo') ->constrained('contracts')
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nomeclatura_arquivo');
    }
};
