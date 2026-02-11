<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationUserMemberData extends Model
{
    protected $table = 'registration_user_member_data';

    protected $fillable = ['registration_user_id', 'field_key', 'value'];

    public function registrationUser()
    {
        return $this->belongsTo(RegistrationUser::class);
    }
}
