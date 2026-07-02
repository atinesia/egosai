<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'message',
        'status',
        'total_contacts',
        'sent_count',
        'failed_count'
    ];

    public function logs()
    {
        return $this->hasMany(BroadcastLog::class);
    }
}
