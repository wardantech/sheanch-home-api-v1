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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->integer('reviewer_type')->nullable()->comment('2-landlord, 3-tenant');
            $table->integer('reviewer_type_id')->nullable()->comment('landlord or tenant Id');
            $table->integer('review_type')->nullable()->comment('1-property,2-landlord,3-tenant');
            $table->integer('review_type_id')->nullable()->comment('which review - Id');
            $table->longText('review');
            $table->double('rating')->nullable();
            $table->integer('status')->comment('0-inactive,1-active');
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
        Schema::dropIfExists('reviews');
    }
};
