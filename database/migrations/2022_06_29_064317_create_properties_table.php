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
            $table->string('name');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('thana_id')->constrained('thanas');
            $table->foreignId('district_id')->constrained('districts');
            $table->foreignId('division_id')->constrained('divisions');
            $table->foreignId('property_type_id')->constrained('property_types');
            $table->tinyInteger('property_category')->comment('1 for commercial 2 for residential');
            $table->tinyInteger('sale_type')->comment('1 for rent 2 for sale');
            $table->integer('bed_rooms');
            $table->integer('balcony');
            $table->string('floor');
            $table->integer('bath_rooms');
            $table->string('holding_number')->index();
            $table->string('road_number')->index();
            $table->string('zip_code')->index();
            $table->text('address');
            $table->double('rent_amount');
            $table->double('security_money')->nullable();
            $table->double('total_amount')->comment('Total amount with utilities and rent_amount');
            $table->integer('area_size')->nullable()->comment('square feet');
            $table->text('video_link')->nullable();
            $table->text('utilities')->nullable();
            $table->text('facilitie_ids')->nullable();
            $table->text('google_map_location')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1-active, 2-inactive');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
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
