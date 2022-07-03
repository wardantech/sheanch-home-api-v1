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
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->integer('status');
            $table->integer('property_id')->index();
            $table->integer('landlord_id')->index();
            $table->integer('lease_type')->index()->comment('1 for commercial, 2 for residential');
            $table->integer('sale_type')->index()->comment('1 for rent, 2 for sale');
            $table->double('lease_amount')->index();
            $table->double('security_money')->nullable();
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
        Schema::dropIfExists('leases');
    }
};