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
        Schema::create('file_naming', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('standard_file_naming');
            $table->timestamps();
        });

        
    }

    /**->constrained('file_naming') ->constrained('contracts')
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_naming');
    }
};
