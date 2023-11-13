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
        Schema::create('checklists', function (Blueprint $table) {
            $table->id('id_checklist');
            $table->string('id_contrato');
            $table->date('data_checklist');
            $table->string('objeto_contrato');
            $table->string('forma_envio');
            $table->string('obs');
            $table->boolean('aceite');
            $table->string('setor');
            $table->string('assinado_por');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checklists');
    }
};
