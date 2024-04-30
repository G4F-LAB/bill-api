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
        Schema::table('files', function(Blueprint $table) {
            $table->dropForeign('files_item_id_foreign');
            $table->dropColumn('item_id');
        });

        Schema::create('files_itens', function(Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('file_id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('files_itens', function (Blueprint $table) {
            $table->foreign('item_id')->references('id')->on('itens');
            $table->foreign('file_id')->references('id')->on('files');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files_itens', function(Blueprint $table) {
            $table->dropForeign('files_itens_item_id_foreign');
        });

        Schema::table('files_itens', function(Blueprint $table) {
            $table->dropForeign('files_itens_file_id_foreign');
        });

        Schema::dropIfExists('files_itens');
    }
};
