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
        Schema::create('property_ads', function (Blueprint $table) {
            $table->id();
            $table->integer('status');
            $table->integer('landlord_id')->index();
            $table->string('start_date')->index();
            $table->string('end_date')->index();
            $table->integer('property_id')->index();
            $table->integer('property_category')->index();
            $table->integer('property_type')->index();
            $table->integer('sale_type')->index();
            $table->double('security_money')->nullable();
            $table->double('rent_amount');
            $table->integer('division_id');
            $table->integer('district_id');
            $table->integer('thana_id');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_ads');
    }
};
