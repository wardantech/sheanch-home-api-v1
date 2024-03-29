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
        Schema::create('property_deeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landlord_id')->constrained('users');
            $table->foreignId('tenant_id')->constrained('users');
            $table->foreignId('property_id')->constrained('properties');
            $table->foreignId('property_ad_id')->constrained('property_ads');
            $table->tinyInteger('status')->comment('0-decline, 1-pending, 2-view, 3-Accept, 4-info-submit, 5-approved');
            $table->string('start_date')->index()->nullable();
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
        Schema::dropIfExists('property_deeds');
    }
};
