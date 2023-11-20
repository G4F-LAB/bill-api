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
        Schema::create('logs', function (Blueprint $table) {
            $table->id('id_log');
            $table->unsignedInteger('id_collaborator');
            $table->ipAddress('origin_ip');
            $table->string('action',10);
            $table->string('route',100);
            $table->datetime('created_at');
        });

        Schema::table('logs', function(Blueprint $table){
            $table->foreign('id_collaborator')->references('id_collaborator')->on('collaborators');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropForeign('logs_id_colaborador_foreign');
        });
        
        Schema::dropIfExists('logs');
    }
};
