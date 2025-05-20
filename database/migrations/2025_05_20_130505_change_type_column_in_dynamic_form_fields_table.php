<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTypeColumnInDynamicFormFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dynamic_form_fields', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        Schema::table('dynamic_form_fields', function (Blueprint $table) {
            $table->enum('type', ['text', 'email', 'number', 'select', 'checkbox', 'radio',
                'textarea', 'date', 'file', 'tel', 'time', 'datetime-local', 'url', 'country', 'city']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dynamic_form_fields', function (Blueprint $table) {
            $table->enum('type', ['text', 'email', 'number', 'select', 'checkbox', 'radio',
                'textarea', 'date', 'file', 'tel', 'time', 'datetime-local', 'url'])->change();
        });
    }
}
