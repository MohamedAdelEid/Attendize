<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWhatsappTemplateToRegistrationsTable extends Migration
{
    public function up()
    {
        Schema::table('registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('registrations', 'whatsapp_message_template')) {
                $table->text('whatsapp_message_template')->nullable()->after('private_slug');
            }
            if (!Schema::hasColumn('registrations', 'whatsapp_attach_ticket')) {
                $table->boolean('whatsapp_attach_ticket')->default(false)->after('whatsapp_message_template');
            }
        });
    }

    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            if (Schema::hasColumn('registrations', 'whatsapp_attach_ticket')) {
                $table->dropColumn('whatsapp_attach_ticket');
            }
            if (Schema::hasColumn('registrations', 'whatsapp_message_template')) {
                $table->dropColumn('whatsapp_message_template');
            }
        });
    }
}
