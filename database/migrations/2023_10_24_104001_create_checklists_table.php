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
            $table->id();
            $table->unsignedInteger('contract_id');
            $table->date('date_checklist');
            $table->string('object_contract');
            $table->string('shipping_method');
            $table->string('obs')->nullable();
            $table->boolean('accept');
            $table->string('sector', 100);
            $table->unsignedBigInteger('signed_by');
            $table->unsignedSmallInteger('completion')->default(0);
            $table->timestamps();
        });

        Schema::table('checklists', function (Blueprint $table) {
            $table->foreign('signed_by')->references('id')->on('collaborators');
            $table->foreign('contract_id')->references('id')->on('contracts');
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
