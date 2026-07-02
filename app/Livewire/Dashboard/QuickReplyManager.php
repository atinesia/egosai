<?php

namespace App\Livewire\Dashboard;

use App\Models\Contact;
use App\Models\QuickReply;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class QuickReplyManager extends Component
{
    use WithPagination;

    public $quickReplies;
    public string $shortcut, $message;
    public string|null $selectedReplyId;
    public $isModalOpen = false;
    public $isEditMode = false;

    protected function rules()
    {
        return [
            'shortcut' => [
                'required',
                'string',
                'alpha_dash', // Hanya boleh huruf, angka, dash, dan underscore
                'max:50',
                // Unik per tenant_id
                Rule::unique('quick_replies', 'shortcut')
                    ->where('tenant_id', Auth::user()->tenant_id)
                    ->ignore($this->selectedReplyId)
            ],
            'message' => 'required|string|max:5000',
        ];
    }

    protected $messages = [
        'shortcut.required' => 'Pintasan wajib diisi.',
        'shortcut.alpha_dash' => 'Pintasan hanya boleh berisi huruf, angka, strip (-), dan garis bawah (_).',
        'shortcut.unique' => 'Pintasan ini sudah digunakan.',
        'message.required' => 'Isi teks balasan wajib diisi.',
    ];

    public function render()
    {
        // Otomatis terfilter per tenant berkat trait BelongsToTenant pada Model
        $this->quickReplies = QuickReply::orderBy('shortcut')->get();

        return view('livewire.dashboard.quick-reply-manager')
            ->layout('layouts.app');
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->shortcut = '';
        $this->message = '';
        $this->selectedReplyId = null;
        $this->isEditMode = false;
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function store()
    {
        $this->validate();

        QuickReply::create([
            'tenant_id' => Auth::user()->tenant_id,
            'shortcut' => strtolower($this->shortcut),
            'message' => $this->message,
        ]);

        session()->flash('success', 'Balasan cepat berhasil ditambahkan.');
        $this->closeModal();
    }

    public function edit(int $id)
    {
        $this->resetValidation();
        $reply = QuickReply::findOrFail($id);

        $this->selectedReplyId = $id;
        $this->shortcut = $reply->shortcut;
        $this->message = $reply->message;
        $this->isEditMode = true;

        $this->openModal();
    }

    public function update()
    {
        $this->validate();

        if ($this->selectedReplyId) {
            $reply = QuickReply::findOrFail($this->selectedReplyId);
            $reply->update([
                'shortcut' => strtolower($this->shortcut),
                'message' => $this->message,
            ]);

            session()->flash('success', 'Balasan cepat berhasil diperbarui.');
            $this->closeModal();
        }
    }

    public function delete(int $id)
    {
        QuickReply::findOrFail($id)->delete();
        session()->flash('success', 'Balasan cepat berhasil dihapus.');
    }
}
