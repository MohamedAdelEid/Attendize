    <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLandingAndMembersFlagsToRegistrationsTable extends Migration
{
    public function up()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->boolean('show_on_landing')->default(false)->after('status');
            $table->boolean('is_members_form')->default(false)->after('show_on_landing');
        });
    }

    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['show_on_landing', 'is_members_form']);
        });
    }
}
