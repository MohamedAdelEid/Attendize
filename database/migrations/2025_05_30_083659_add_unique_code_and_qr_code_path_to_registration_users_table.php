<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueCodeAndQrCodePathToRegistrationUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('registration_users', function (Blueprint $table) {
            $table->string('unique_code', 10)->nullable()->unique();
            $table->string('qr_code_path')->nullable();
            $table->string('ticket_token', 32)->nullable()->unique();
            $table->string('ticket_pdf_path')->nullable();
            $table->timestamp('ticket_generated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('registration_users', function (Blueprint $table) {
            $table->dropColumn([
                'unique_code',
                'qr_code_path',
                'ticket_token',
                'ticket_pdf_path',
                'ticket_generated_at'
            ]);
        });
    }
}
