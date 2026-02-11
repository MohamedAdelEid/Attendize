<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrationPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registration_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('registration_user_id');
            $table->string('payment_gateway')->default('HyperPay');
            $table->string('transaction_id')->nullable();
            $table->string('checkout_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('SAR');
            $table->string('status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->text('payment_response')->nullable();
            $table->string('resource_path')->nullable();
            $table->timestamps();

            $table->foreign('registration_user_id')
                ->references('id')
                ->on('registration_users')
                ->onDelete('cascade');

            $table->index('transaction_id');
            $table->index('checkout_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registration_payments', function (Blueprint $table) {
            $table->dropForeign(['registration_user_id']);
        });
        Schema::dropIfExists('registration_payments');
    }
}
