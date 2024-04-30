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
        Schema::create('status_checklist', function(Blueprint $table){
            $table->id();
            $table->string('name',50);
        });

        Schema::table('checklists', function(Blueprint $table) {
            $table->unsignedSmallInteger('status_id')->default(1);
        });

        // Schema::table('checklists', function(Blueprint $table) {
        //     $table->foreign('status_id')->references('id')->on('status_checklist');
        // });

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checklists', function(Blueprint $table) {
            //$table->dropForeign('checklist_status_id_foreign');
            $table->dropColumn('status_id');
        });

        Schema::dropIfExists('status_checklist');
    }
};
