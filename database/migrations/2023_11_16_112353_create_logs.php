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
            $table->id('id');
            $table->unsignedInteger('collaborator_id');
            $table->ipAddress('origin_ip');
            $table->string('action',10);
            $table->string('route',100);
            $table->datetime('created_at');
        });

        Schema::table('logs', function(Blueprint $table){
            $table->foreign('collaborator_id')->references('id')->on('collaborators');
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
            $table->dropForeign('logs_collaborator_id_foreign');
        });
        
        Schema::dropIfExists('logs');
    }
};
