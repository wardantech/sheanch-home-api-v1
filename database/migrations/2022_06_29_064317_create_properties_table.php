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
            $table->integer('status')->default(1)->index();
            $table->string('name');
            $table->foreignId('user_id')->constrained('users');
            $table->integer('property_category')->index()->nullable()->comment('1 for commercial 2 for residential');
            $table->integer('property_type_id')->index();
            $table->integer('sale_type')->index()->nullable()->comment('1 for rent 2 for sale');
            $table->integer('bed_rooms')->nullable();
            $table->integer('balcony')->nullable();
            $table->string('floor')->nullable();
            $table->integer('bath_rooms')->nullable();
            $table->integer('area_size')->nullable()->comment('square feet');
            $table->text('video_link')->nullable();
            $table->double('rent_amount');
            $table->double('security_money')->nullable();
            $table->longText('utilities')->nullable();
            $table->longText('facilities')->nullable();
            $table->string('house_no')->index()->nullable();
            $table->string('zip_code')->index()->nullable();
            $table->integer('thana_id')->index();
            $table->integer('district_id')->index();
            $table->integer('division_id')->index();
            $table->text('address');
            $table->text('google_map_location')->nullable();
            $table->text('description')->nullable();
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
