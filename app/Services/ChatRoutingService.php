<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ChatRoutingService
{
    /**
     * Alokasikan percakapan ke agen yang paling senggang (Round Robin Beban Kerja).
     */
    public function assignToNextAvailableAgent(Conversation $conversation): ?int
    {
        // 1. Ambil semua user/agent yang se-tenant
        // Di skala lanjutan, Anda bisa menambahkan filter 'is_online' jika mengintegrasikan tracker online
        $agents = User::where('tenant_id', $conversation->tenant_id)
            ->where('role', '!=', 'admin') // Opsional: jika admin tidak ikut membalas chat
            ->get();

        if ($agents->isEmpty()) {
            return null;
        }

        // 2. Hitung beban kerja (jumlah chat open & pending) masing-masing agen saat ini
        $agentLoad = Conversation::select('assigned_user_id', DB::raw('count(*) as total_chats'))
            ->where('tenant_id', $conversation->tenant_id)
            ->whereIn('status', ['open', 'pending'])
            ->whereNotNull('assigned_user_id')
            ->groupBy('assigned_user_id')
            ->pluck('total_chats', 'assigned_user_id')
            ->toArray();

        $selectedAgentId = null;
        $minLoad = 999999;

        foreach ($agents as $agent) {
            $currentLoad = $agentLoad[$agent->id] ?? 0;

            // Cari yang beban kerjanya paling kecil
            if ($currentLoad < $minLoad) {
                $minLoad = $currentLoad;
                $selectedAgentId = $agent->id;
            }
        }

        if ($selectedAgentId) {
            $conversation->update([
                'assigned_user_id' => $selectedAgentId
            ]);

            return $selectedAgentId;
        }

        return null;
    }
}
