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
        Schema::create('files', function (Blueprint $table) {
            $table->id('id_file');
            $table->string('complementary_name',100);
            $table->string('path');
            $table->unsignedBigInteger('id_item');
            $table->unsignedBigInteger('id_file_type');
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

        Schema::dropIfExists('files');
    }
};
