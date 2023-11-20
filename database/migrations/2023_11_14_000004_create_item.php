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
        Schema::create('itens', function (Blueprint $table) {
            $table->id('id_item');
            $table->unsignedBigInteger('id_checklist');
            $table->unsignedBigInteger('id_file_naming');
            $table->unsignedBigInteger('id_file_type');
            $table->boolean('status');
            $table->unsignedSmallInteger('competence');
            $table->timestamps();
        });

        Schema::table('itens', function(Blueprint $table){
            //$table->foreign('id_arquivo')->references('id_arquivo')->on('files');
            $table->foreign('id_file_naming')->references('id_file_naming')->on('file_naming');
            $table->foreign('id_checklist')->references('id_checklist')->on('checklists');
            $table->foreign('id_file_type')->references('id_file_type')->on('file_types');
        });

        Schema::table('files', function(Blueprint $table) {
            $table->foreign('id_item')->references('id_item')->on('itens');
        });
    }

    /**->constrained('collaborators') ->constrained('contracts')
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('itens', function (Blueprint $table) {
            $table->dropForeign('itens_id_file_naming_foreign');
            $table->dropForeign('itens_id_checklist_foreign');
        });

        Schema::table('files', function (Blueprint $table) {
            $table->dropForeign('files_id_item_foreign');
        });

        Schema::dropIfExists('itens');
    }
};
