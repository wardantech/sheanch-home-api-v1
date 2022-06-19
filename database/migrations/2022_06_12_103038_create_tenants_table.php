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
            $table->tinyInteger('type');
            $table->string('first_name');
            $table->string('last_name');
            $table->tinyInteger('gender')->comment('1-male,2-female,3-others');
            $table->string('dob');
            $table->string('nid')->nullable();
            $table->string('passport_no')->nullable();
            $table->tinyInteger('marital_status')->nullable()->comment('1-married,2-unmarried ');
            $table->tinyInteger('thana_id');
            $table->tinyInteger('district_id');
            $table->tinyInteger('division_id');
            $table->string('postal_code')->nullable();
            $table->text('postal_address')->nullable();
            $table->text('physical_address')->nullable();
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
