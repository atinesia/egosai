<?php

namespace App\Livewire\Dashboard;

use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\WhatsappSession;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Reports extends Component
{
    public string $period = '7'; // '7', '30', '90'

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;
        $days     = (int) $this->period;
        $from     = now()->subDays($days)->startOfDay();
        $now      = now();

        // ── Stat cards ──────────────────────────────────────────────────────

        $totalMessages = Message::whereHas('conversation', fn ($q) =>
                $q->where('tenant_id', $tenantId))
            ->where('created_at', '>=', $from)
            ->count();

        $incomingMessages = Message::whereHas('conversation', fn ($q) =>
                $q->where('tenant_id', $tenantId))
            ->where('sender_type', 'contact')
            ->where('created_at', '>=', $from)
            ->count();

        $aiReplies = Message::whereHas('conversation', fn ($q) =>
                $q->where('tenant_id', $tenantId))
            ->where('sender_type', 'ai')
            ->where('created_at', '>=', $from)
            ->count();

        $agentReplies = Message::whereHas('conversation', fn ($q) =>
                $q->where('tenant_id', $tenantId))
            ->where('sender_type', 'agent')
            ->where('created_at', '>=', $from)
            ->count();

        $newContacts = Contact::where('tenant_id', $tenantId)
            ->where('created_at', '>=', $from)
            ->count();

        $resolvedConversations = Conversation::where('tenant_id', $tenantId)
            ->where('status', 'resolved')
            ->where('updated_at', '>=', $from)
            ->count();

        $openConversations = Conversation::where('tenant_id', $tenantId)
            ->where('status', 'open')
            ->count();

        // ── Rata-rata waktu respons pertama (AI atau agent) ─────────────────
        // Hitung selisih created_at antara pesan pertama dari contact dan
        // balasan pertama (ai/agent) per conversation, lalu rata-ratakan.
        $avgResponseSeconds = $this->calcAvgResponseTime($tenantId, $from);

        // ── Chart: pesan per hari (7/30/90 hari terakhir) ───────────────────
        $dailyData = $this->getDailyMessageData($tenantId, $from, $days);

        // ── Chart: distribusi pengirim (pie) ────────────────────────────────
        $senderDist = [
            'Pelanggan' => $incomingMessages,
            'AI'        => $aiReplies,
            'Agent'     => $agentReplies,
        ];

        // ── Top 5 kontak paling aktif ────────────────────────────────────────
        $topContacts = Message::select('contacts.name', 'contacts.wa_number',
                DB::raw('COUNT(*) as total'))
            ->join('conversations', 'messages.conversation_id', '=', 'conversations.id')
            ->join('contacts', 'conversations.contact_id', '=', 'contacts.id')
            ->where('conversations.tenant_id', $tenantId)
            ->where('messages.sender_type', 'contact')
            ->where('messages.created_at', '>=', $from)
            ->groupBy('contacts.id', 'contacts.name', 'contacts.wa_number')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // ── Performa per nomor WhatsApp ──────────────────────────────────────
        $sessionStats = WhatsappSession::where('tenant_id', $tenantId)
            ->get()
            ->map(function ($sess) use ($tenantId, $from) {
                $msgs = Message::whereHas('conversation', fn ($q) =>
                        $q->where('tenant_id', $tenantId)
                          ->where('whatsapp_session_id', $sess->id))
                    ->where('created_at', '>=', $from)
                    ->selectRaw("sender_type, COUNT(*) as cnt")
                    ->groupBy('sender_type')
                    ->pluck('cnt', 'sender_type');

                return [
                    'label'    => $sess->label,
                    'phone'    => $sess->phone_number ?? '-',
                    'status'   => $sess->statusLabel(),
                    'incoming' => $msgs['contact'] ?? 0,
                    'ai'       => $msgs['ai'] ?? 0,
                    'agent'    => $msgs['agent'] ?? 0,
                ];
            });

        return view('livewire.dashboard.reports', compact(
            'totalMessages', 'incomingMessages', 'aiReplies', 'agentReplies',
            'newContacts', 'resolvedConversations', 'openConversations',
            'avgResponseSeconds', 'dailyData', 'senderDist',
            'topContacts', 'sessionStats', 'days'
        ))->layout('layouts.app');
    }

    protected function calcAvgResponseTime(int $tenantId, Carbon $from): ?float
    {
        // Ambil conversation yang ada balasan dalam periode
        $results = DB::select("
            SELECT
                AVG(TIMESTAMPDIFF(SECOND, first_contact.created_at, first_reply.created_at)) as avg_seconds
            FROM (
                SELECT conversation_id, MIN(created_at) AS created_at
                FROM messages
                WHERE sender_type = 'contact'
                GROUP BY conversation_id
            ) AS first_contact
            JOIN (
                SELECT conversation_id, MIN(created_at) AS created_at
                FROM messages
                WHERE sender_type IN ('ai', 'agent')
                GROUP BY conversation_id
            ) AS first_reply ON first_reply.conversation_id = first_contact.conversation_id
            JOIN conversations c ON c.id = first_contact.conversation_id
            WHERE c.tenant_id = ?
              AND first_contact.created_at >= ?
              AND first_reply.created_at > first_contact.created_at
        ", [$tenantId, $from->toDateTimeString()]);

        return $results[0]->avg_seconds ?? null;
    }

    protected function getDailyMessageData(int $tenantId, Carbon $from, int $days): array
    {
        // Buat array semua tanggal dalam periode, default 0
        $dates = collect();
        for ($i = $days - 1; $i >= 0; $i--) {
            $dates[now()->subDays($i)->format('Y-m-d')] = ['incoming' => 0, 'replies' => 0];
        }

        // Isi dengan data nyata dari DB
        $rows = DB::select("
            SELECT
                DATE(m.created_at) as date,
                SUM(CASE WHEN m.sender_type = 'contact' THEN 1 ELSE 0 END) as incoming,
                SUM(CASE WHEN m.sender_type IN ('ai','agent') THEN 1 ELSE 0 END) as replies
            FROM messages m
            JOIN conversations c ON c.id = m.conversation_id
            WHERE c.tenant_id = ?
              AND m.created_at >= ?
            GROUP BY DATE(m.created_at)
            ORDER BY date
        ", [$tenantId, $from->toDateTimeString()]);

        foreach ($rows as $row) {
            if (isset($dates[$row->date])) {
                $dates[$row->date] = [
                    'incoming' => (int) $row->incoming,
                    'replies'  => (int) $row->replies,
                ];
            }
        }

        return [
            'labels'   => $dates->keys()->map(fn ($d) =>
                Carbon::parse($d)->locale('id')->isoFormat($days <= 7 ? 'ddd, D MMM' : 'D MMM')
            )->values()->all(),
            'incoming' => $dates->pluck('incoming')->values()->all(),
            'replies'  => $dates->pluck('replies')->values()->all(),
        ];
    }
}
