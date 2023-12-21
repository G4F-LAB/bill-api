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
        Schema::table('file_naming', function (Blueprint $table) {
            $table->unsignedSmallInteger('file_type_id')->after('standard_file_naming')->default(1);
            $table->foreign('file_type_id')->references('id')->on('file_types');
        });

        Schema::table('itens', function(Blueprint $table) {
            $table->dropForeign('itens_file_type_id_foreign');
            $table->dropColumn('file_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('file_naming', function (Blueprint $table) {
            $table->dropForeign('file_naming_file_type_id_foreign');
        });
    }
};
