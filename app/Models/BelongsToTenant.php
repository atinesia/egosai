<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Trait sederhana untuk multi-tenancy berbasis kolom tenant_id (single database).
 * Pasang trait ini di setiap model yang punya kolom tenant_id.
 */
trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        // Auto-scope query ke tenant user yang sedang login
        static::addGlobalScope(function (Builder $builder) {
            if (Auth::check() && Auth::user()->tenant_id) {
                $builder->where($builder->getModel()->getTable() . '.tenant_id', Auth::user()->tenant_id);
            }
        });

        // Auto-isi tenant_id saat membuat record baru
        static::creating(function ($model) {
            if (empty($model->tenant_id) && auth()->check()) {
                $model->tenant_id = Auth::user()->tenant_id;
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
