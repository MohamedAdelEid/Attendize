<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateDynamicFormFieldsAddConferenceProfession extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update the enum to include conference and profession
        DB::statement("ALTER TABLE dynamic_form_fields MODIFY COLUMN type ENUM('text', 'email', 'number', 'select', 'checkbox', 'radio', 'textarea', 'date', 'file', 'tel', 'time', 'datetime-local', 'url', 'country', 'city', 'user_types', 'conference', 'profession')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to previous enum
        DB::statement("ALTER TABLE dynamic_form_fields MODIFY COLUMN type ENUM('text', 'email', 'number', 'select', 'checkbox', 'radio', 'textarea', 'date', 'file', 'tel', 'time', 'datetime-local', 'url', 'country', 'city', 'user_types')");
    }
}
