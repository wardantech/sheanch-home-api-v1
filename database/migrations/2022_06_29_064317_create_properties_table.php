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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->integer('thana_id')->index();
            $table->integer('district_id')->index();
            $table->integer('division_id')->index();
            $table->integer('property_type_id')->index();
            $table->integer('landlord_id')->index();
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('zip_code')->index();
            $table->text('address');
            $table->integer('bed_rooms')->nullable();
            $table->integer('bath_rooms')->nullable();
            $table->integer('units')->nullable();
            $table->integer('area_size')->nullable()->comment('square feet');
            $table->double('rent_amount');
            $table->text('description')->nullable();
            $table->integer('status')->default(1)->index();
            $table->double('security_money')->nullable();
            $table->longText('utilities_paid_by_landlord')->nullable();
            $table->longText('utilities_paid_by_tenant')->nullable();
            $table->longText('facilities')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('properties');
    }
};
