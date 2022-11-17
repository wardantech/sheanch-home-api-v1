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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties');
            $table->foreignId('revenue_id')->nullable()->constrained('revenues')->onDelete('cascade');
            $table->foreignId('expanse_id')->nullable()->constrained('expanses');
            $table->tinyInteger('transaction_purpose')->comment('1 = Revenue | 2 = Expanse');
            $table->double('cash_in')->default(0);
            $table->double('cash_out')->default(0);
            $table->text('remark')->nullable();
            $table->date('date');
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
        Schema::dropIfExists('transactions');
    }
};
