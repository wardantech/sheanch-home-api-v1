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
        Schema::create('deed_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_deed_id')->constrained('property_deeds');
            $table->string('image');
            $table->string('tenant_name');
            $table->string('fathers_name');
            $table->date('date_of_birth');
            $table->tinyInteger('marital_status')->comment('1-single,2-married,3-divorced');
            $table->string('present_address');
            $table->string('occupation');
            $table->string('office_address')->nullable();
            $table->string('religion');
            $table->string('edu_qualif')->comment('Education Qualification');
            $table->string('phone');
            $table->string('nid');
            $table->string('passport')->nullable();
            $table->text('emergency_contact')->comment('name,relation,address,phone');
            $table->text('family_members')->comment('name,age,occupation,phone');
            $table->text('home_servant')->nullable()->comment('name,nid,phone,address');
            $table->text('driver')->nullable()->comment('name,nid,phone,address');
            $table->text('previus_landlord')->nullable()->comment('name,phone,address');
            $table->text('leaving_home')->nullable()->comment('Reasons for leaving previous home');
            $table->date('issue_date');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deed_information');
    }
};
