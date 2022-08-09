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
            $table->integer('status');
            $table->string('start_date')->index()->nullable();
            $table->integer('property_id')->index();
            $table->integer('property_ad_id')->index()->nullable();
            $table->integer('landlord_id')->index();
            $table->integer('tenant_id')->index();
            $table->integer('lease_type')->nullable()->index()->comment('1 for commercial, 2 for residential');
            $table->integer('sale_type')->nullable()->index()->comment('1 for rent, 2 for sale');
            $table->double('lease_amount')->nullable()->index();
            $table->double('security_money')->nullable()->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable()->nullable();
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
        Schema::dropIfExists('property_deeds');
    }
};
