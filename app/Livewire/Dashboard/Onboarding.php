<?php

namespace App\Livewire\Dashboard;

use App\Models\KnowledgeBase;
use App\Models\WhatsappSession;
use App\Services\WhatsappService;
use Illuminate\Support\Str;
use Livewire\Component;

class Onboarding extends Component
{
    public int $step = 1;
    public int $totalSteps = 4;

    // Step 1 - Tentang bisnis
    public string $business_name = '';
    public string $business_category = 'retail';
    public string $description = '';

    // Step 2 - Kepribadian AI
    public string $tone = 'friendly'; // friendly | formal
    public string $greeting_message = '';
    public string $system_prompt = '';

    // Step 3 - Knowledge base awal (maks 3 entri cepat)
    public array $faqs = [
        ['title' => '', 'content' => ''],
    ];

    // Step 4 - WhatsApp
    public ?WhatsappSession $waSession = null;
    public ?string $waErrorMessage = null;

    public function mount(): void
    {
        $tenant = auth()->user()->tenant;

        if ($tenant->isOnboarded()) {
            redirect()->route('dashboard.inbox');
            return;
        }

        $this->business_name = $tenant->name;
        $this->business_category = $tenant->business_category ?? 'retail';
        $this->description = $tenant->description ?? '';

        $aiSetting = $tenant->aiSetting;
        $this->system_prompt = $aiSetting->system_prompt ?? '';
        $this->greeting_message = $aiSetting->fallback_message ?? '';

        $this->waSession = $tenant->whatsappSession;
    }

    public function goToStep(int $step): void
    {
        if ($step < $this->step) {
            $this->step = $step;
        }
    }

    public function nextStep(): void
    {
        match ($this->step) {
            1 => $this->saveBusinessInfo(),
            2 => $this->saveAiPersonality(),
            3 => $this->saveFaqs(),
            default => null,
        };

        if ($this->step < $this->totalSteps) {
            $this->step++;
        }
    }

    public function prevStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    protected function saveBusinessInfo(): void
    {
        $this->validate([
            'business_name' => 'required|string|max:255',
            'business_category' => 'required|string',
            'description' => 'nullable|string|max:1000',
        ]);

        auth()->user()->tenant->update([
            'name' => $this->business_name,
            'business_category' => $this->business_category,
            'description' => $this->description,
        ]);

        // Generate draft prompt otomatis kalau user belum pernah isi
        if (blank($this->system_prompt)) {
            $this->system_prompt = $this->buildDefaultPrompt();
        }
    }

    protected function saveAiPersonality(): void
    {
        $this->validate([
            'system_prompt' => 'required|string',
            'greeting_message' => 'nullable|string|max:255',
        ]);

        auth()->user()->tenant->aiSetting->update([
            'system_prompt' => $this->system_prompt,
            'fallback_message' => $this->greeting_message ?: 'Mohon tunggu, tim kami akan segera membantu Anda.',
        ]);
    }

    protected function saveFaqs(): void
    {
        $tenantId = auth()->user()->tenant_id;

        foreach ($this->faqs as $faq) {
            if (blank($faq['title']) && blank($faq['content'])) {
                continue;
            }

            KnowledgeBase::create([
                'tenant_id' => $tenantId,
                'title' => $faq['title'] ?: 'Tanpa judul',
                'content' => $faq['content'],
            ]);
        }
    }

    public function addFaqRow(): void
    {
        if (count($this->faqs) < 3) {
            $this->faqs[] = ['title' => '', 'content' => ''];
        }
    }

    public function removeFaqRow(int $index): void
    {
        unset($this->faqs[$index]);
        $this->faqs = array_values($this->faqs);

        if (empty($this->faqs)) {
            $this->faqs = [['title' => '', 'content' => '']];
        }
    }

    public function connectWhatsapp(WhatsappService $wa): void
    {
        $this->waErrorMessage = null;
        $tenant = auth()->user()->tenant;

        if (! $this->waSession) {
            $this->waSession = WhatsappSession::create([
                'tenant_id' => $tenant->id,
                'session_id' => 'tenant-' . $tenant->id . '-' . Str::random(6),
                'status' => 'qr_pending',
            ]);
        } else {
            $this->waSession->update(['status' => 'qr_pending', 'qr_code' => null]);
        }

        $result = $wa->startSession($this->waSession->session_id);

        if (! $result['ok']) {
            $this->waSession->update(['status' => 'disconnected']);
            $this->waErrorMessage = $result['message'];
            return;
        }

        $this->waSession->refresh();
    }

    public function refreshWhatsappStatus(): void
    {
        $this->waSession?->refresh();
    }

    public function skipWhatsapp(): void
    {
        $this->finish();
    }

    public function finish(): void
    {
        auth()->user()->tenant->update(['onboarding_completed_at' => now()]);

        redirect()->route('dashboard.inbox');
    }

    protected function buildDefaultPrompt(): string
    {
        $namaBisnis = $this->business_name;
        $kategori = $this->business_category;
        $deskripsi = $this->description ?: 'belum ada deskripsi tambahan';

        $gaya = $this->tone === 'formal'
            ? 'profesional, sopan, dan menggunakan bahasa baku'
            : 'ramah, santai, dan hangat seperti teman, tetap sopan';

        return "Kamu adalah asisten customer service untuk \"{$namaBisnis}\", sebuah bisnis di kategori {$kategori}. "
            . "Tentang bisnis ini: {$deskripsi}. "
            . "Gaya bicara kamu {$gaya}. Jawab pertanyaan pelanggan dengan singkat, jelas, dan akurat "
            . "berdasarkan informasi referensi yang diberikan. Jangan mengarang informasi yang tidak ada di referensi.";
    }

    public function updatedTone(): void
    {
        $this->system_prompt = $this->buildDefaultPrompt();
    }

    public function render()
    {
        return view('livewire.dashboard.onboarding')->layout('layouts.onboarding');
    }
}
