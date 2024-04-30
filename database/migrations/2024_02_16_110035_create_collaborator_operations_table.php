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
        Schema::create('collaborator_operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('collaborator_id');
            $table->unsignedInteger('operation_id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('collaborator_operations', function(Blueprint $table){
            $table->foreign('collaborator_id')->references('id')->on('collaborators');
            $table->foreign('operation_id')->references('id')->on('operations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('collaborator_operations', function (Blueprint $table) {
            $table->dropForeign('collaborator_operations_collaborator_id_foreign');
            $table->dropForeign('collaborator_operations_operation_id_foreign');
        });
        Schema::dropIfExists('collaborator_operations');
    }
};
