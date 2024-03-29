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
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('property_id')->nullable()->constrained('properties');
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts');
            $table->foreignId('mobile_bank_account_id')->nullable()->constrained('mobile_bank_accounts');
            $table->foreignId('property_deed_id')->nullable()->constrained('property_deeds');
            $table->foreignId('expanse_item_id')->nullable()->constrained('expanse_items');
            $table->string('transaction_id')->nullable();
            $table->tinyInteger('transaction_purpose')->comment('1 = Revenue | 2 = Expanse');
            $table->double('cash_in')->default(0);
            $table->double('cash_out')->default(0);
            $table->double('due_amount')->default(0);
            $table->text('remark')->nullable();
            $table->tinyInteger('payment_method')->comment('1 = Cash | 2 = Bank | 3 = Mobile Bank');
            $table->tinyInteger('is_initial')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
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
