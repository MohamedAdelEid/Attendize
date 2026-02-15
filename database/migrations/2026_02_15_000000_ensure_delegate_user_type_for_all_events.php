<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class EnsureDelegateUserTypeForAllEvents extends Migration
{
    public function up()
    {
        $eventIds = DB::table('events')->pluck('id');
        foreach ($eventIds as $eventId) {
            $exists = DB::table('user_types')
                ->where('event_id', $eventId)
                ->where('name', 'Delegate')
                ->exists();
            if (!$exists) {
                DB::table('user_types')->insert([
                    'event_id' => $eventId,
                    'name' => 'Delegate',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down()
    {
    }
}
