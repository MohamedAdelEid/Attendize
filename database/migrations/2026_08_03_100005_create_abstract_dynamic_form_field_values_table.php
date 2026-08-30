<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbstractDynamicFormFieldValuesTable extends Migration
{
    public function up()
    {
        Schema::create('abstract_dynamic_form_field_values', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('abstract_submission_id');
            $table->unsignedInteger('abstract_dynamic_form_field_id');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->foreign('abstract_submission_id', 'adf_values_submission_fk')
                ->references('id')->on('abstract_submissions')->onDelete('cascade');
            $table->foreign('abstract_dynamic_form_field_id', 'adf_values_field_fk')
                ->references('id')->on('abstract_dynamic_form_fields')->onDelete('cascade');
            $table->index('abstract_submission_id', 'adf_values_submission_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('abstract_dynamic_form_field_values');
    }
}
