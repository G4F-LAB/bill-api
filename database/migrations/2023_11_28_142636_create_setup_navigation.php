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
        Schema::create('setup_navigations', function (Blueprint $table) {
            $table->id('id');
            $table->integer('parent_id')->nullable();
            $table->string('name',70);
            $table->string('slug',130)->nullable();
            $table->string('icon',70)->nullable();
            $table->string('path',130)->nullable();
            $table->integer('sort')->default(0);
            $table->jsonb('permission_ids')->nullable();

            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setup_navigations');
    }
};
