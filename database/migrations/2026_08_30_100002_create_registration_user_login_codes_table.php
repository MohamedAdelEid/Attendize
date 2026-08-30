<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrationUserLoginCodesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('registration_user_login_codes')) {
            $this->ensureIndex();
            return;
        }

        Schema::create('registration_user_login_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('registration_user_id');
            $table->unsignedInteger('event_id');
            $table->string('code', 6);
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->foreign('registration_user_id')
                ->references('id')
                ->on('registration_users')
                ->onDelete('cascade');
            $table->foreign('event_id')
                ->references('id')
                ->on('events')
                ->onDelete('cascade');
            $table->index(['registration_user_id', 'event_id'], 'ru_login_codes_user_event_idx');
        });
    }

    protected function ensureIndex(): void
    {
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $indexes = $sm->listTableIndexes('registration_user_login_codes');
        if (!isset($indexes['ru_login_codes_user_event_idx'])) {
            Schema::table('registration_user_login_codes', function (Blueprint $table) {
                $table->index(['registration_user_id', 'event_id'], 'ru_login_codes_user_event_idx');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('registration_user_login_codes');
    }
}
