<?php

namespace App\Livewire\Dashboard;

use App\Models\AiSetting;
use Livewire\Component;

class AiSettings extends Component
{
    public string $model = 'llama-3.3-70b-versatile';
    public string $system_prompt = '';
    public float $temperature = 0.4;
    public bool $is_ai_globally_active = true;
    public string $fallback_message = '';

    public function mount(): void
    {
        $setting = auth()->user()->tenant->aiSetting
            ?? AiSetting::create(['tenant_id' => auth()->user()->tenant_id]);

        $this->model = $setting->model;
        $this->system_prompt = $setting->system_prompt;
        $this->temperature = $setting->temperature;
        $this->is_ai_globally_active = $setting->is_ai_globally_active;
        $this->fallback_message = $setting->fallback_message;
    }

    public function save(): void
    {
        $this->validate([
            'model' => 'required|string',
            'system_prompt' => 'required|string',
            'temperature' => 'required|numeric|min:0|max:1',
            'fallback_message' => 'required|string',
        ]);

        auth()->user()->tenant->aiSetting->update([
            'model' => $this->model,
            'system_prompt' => $this->system_prompt,
            'temperature' => $this->temperature,
            'is_ai_globally_active' => $this->is_ai_globally_active,
            'fallback_message' => $this->fallback_message,
        ]);

        session()->flash('saved', 'Pengaturan AI berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.dashboard.ai-settings')->layout('layouts.app');
    }
}
