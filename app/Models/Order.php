<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'reference',
        'merchant_ref',
        'plan',
        'amount',
        'payment_method',
        'status',
        'checkout_url',
        'qr_url'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
