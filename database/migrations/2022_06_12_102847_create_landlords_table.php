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
        Schema::create('landlords', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->index()->nullable();
            $table->string('mobile')->nullable()->index();
            $table->string('nid')->nullable();
            $table->string('status')->index()->comment('1-active,0-deactivated');
            $table->string('image')->nullable();
            $table->integer('thana_id')->nullable();
            $table->integer('district_id')->nullable();
            $table->integer('division_id')->nullable();
            $table->text('postal_address')->nullable();
            $table->text('residential_address')->nullable();
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
        Schema::dropIfExists('landlords');
    }
};
