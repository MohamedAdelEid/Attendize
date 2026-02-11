<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationPayment extends Model
{
    protected $table = 'registration_payments';

    protected $fillable = [
        'registration_user_id',
        'payment_gateway',
        'transaction_id',
        'checkout_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'payment_response',
        'resource_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_response' => 'array',
    ];

    public function registrationUser()
    {
        return $this->belongsTo(RegistrationUser::class);
    }
}
