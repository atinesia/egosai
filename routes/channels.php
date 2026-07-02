<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('tenant.{tenantId}', function (User $user, $tenantId) {
    // User hanya boleh mendengarkan jika tenantId miliknya cocok dengan channel tersebut
    return (int) $user->tenant_id === (int) $tenantId;
});
