<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'knowledge_bases';

    protected $fillable = ['tenant_id', 'title', 'content', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
