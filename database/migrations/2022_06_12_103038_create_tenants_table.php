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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->index()->nullable();
            $table->string('mobile')->index();
            $table->integer('status')->nullable()->index()->comment('1-active,0-deactivated');
            $table->integer('gender')->nullable()->comment('1-male,2-female,3-others');
            $table->string('dob')->nullable();
            $table->string('nid')->nullable();
            $table->string('image')->nullable();
            $table->string('passport_no')->nullable();
            $table->integer('marital_status')->nullable()->comment('1-married,2-unmarried ');
            $table->integer('thana_id')->nullable();
            $table->integer('district_id')->nullable();
            $table->integer('division_id')->nullable();
            $table->string('postal_code')->nullable();
            $table->text('postal_address')->nullable();
            $table->text('physical_address')->nullable();
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
        Schema::dropIfExists('tenants');
    }
};
