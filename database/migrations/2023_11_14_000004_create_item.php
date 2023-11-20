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
            $table->id();
            $table->unsignedBigInteger('checklist_id');
            $table->unsignedBigInteger('file_naming_id');
            $table->unsignedBigInteger('file_type_id');
            $table->boolean('status')->default(0);
            $table->unsignedSmallInteger('competence');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('itens', function(Blueprint $table){
            //$table->foreign('id_arquivo')->references('id_arquivo')->on('files');
            $table->foreign('file_naming_id')->references('id')->on('file_naming');
            $table->foreign('checklist_id')->references('id')->on('checklists');
            $table->foreign('file_type_id')->references('id')->on('file_types');
        });

        Schema::table('files', function(Blueprint $table) {
            $table->foreign('item_id')->references('id')->on('itens');
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
