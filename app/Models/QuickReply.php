<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuickReply extends Model
{
    use BelongsToTenant; // Otomatis menyaring data berdasarkan tenant_id

    protected $fillable = [
        'tenant_id',
        'shortcut',
        'message'
    ];
}
