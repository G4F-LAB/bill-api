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
        Schema::create('collaborators', function (Blueprint $table) {
            $table->id('id_collaborator');
            $table->string('name',150);
            $table->string('objectguid');
            $table->unsignedSmallInteger('id_permission');
            $table->timestamps();
        });
    }

    /**->constrained('collaborators') ->constrained('contracts')
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collaborators');
    }
};
