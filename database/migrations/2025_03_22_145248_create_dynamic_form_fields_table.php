<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDynamicFormFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dynamic_form_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('registration_id');
            $table->string('label');
            $table->string('name');
            $table->enum('type', ['text', 'email', 'number', 'select', 'checkbox', 'radio', 'textarea', 'date', 'file', 'tel', 'time', 'datetime-local', 'url', 'external_payment']);
            $table->text('options')->nullable(); // For select, checkbox, radio options (JSON)
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('registration_id')->references('id')->on('registrations')->onDelete('cascade');
        });

        Schema::create('dynamic_form_field_values', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('registration_user_id');
            $table->unsignedInteger('dynamic_form_field_id');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->foreign('registration_user_id')->references('id')->on('registration_users')->onDelete('cascade');
            $table->foreign('dynamic_form_field_id')->references('id')->on('dynamic_form_fields')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dynamic_form_field_values');
        Schema::dropIfExists('dynamic_form_fields');
    }
}