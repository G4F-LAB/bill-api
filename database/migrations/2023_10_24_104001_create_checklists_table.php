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
        Schema::create('checklists', function (Blueprint $table) {
            $table->id('id_checklist');
            $table->string('contract');
            $table->date('date_checklist');
            $table->string('object_contract');
            $table->string('shipping_method');
            $table->string('obs')->nullable();
            $table->boolean('accept');
            $table->string('sector',100);
            $table->unsignedBigInteger('signed_by');
            $table->timestamps();
        });

        Schema::table('checklists', function (Blueprint $table) {
            $table->foreign('signed_by')->references('id_collaborator')->on('collaborators');
            $table->foreign('contract')->references('contract')->on('contracts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checklists', function (Blueprint $table) {
            $table->dropForeign('checklists_signed_by_foreign');
            $table->dropForeign('checklists_contract_foreign');
        });
        Schema::dropIfExists('checklists');
    }
};