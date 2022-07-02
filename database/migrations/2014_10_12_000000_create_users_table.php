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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('mobile')->index()->nullable();
            $table->string('name');
            $table->string('email')->index();
            $table->string('status')->index()->default(0)->comment('1-active,0-deactivated');
            $table->integer('type')->index()->comment('1-Admin,2-Landlord,3-Tenant ');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('landlord_id')->nullable();
            $table->integer('tenant_id')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
