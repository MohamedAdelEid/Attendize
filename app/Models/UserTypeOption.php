<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTypeOption extends Model
{
    protected $fillable = ['user_type_id', 'name', 'slug', 'sort_order'];

    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }
}
