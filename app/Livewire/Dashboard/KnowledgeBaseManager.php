<?php

namespace App\Livewire\Dashboard;

use App\Models\KnowledgeBase;
use Livewire\Component;

class KnowledgeBaseManager extends Component
{
    public string $title = '';
    public string $content = '';
    public ?int $editingId = null;

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        if ($this->editingId) {
            KnowledgeBase::find($this->editingId)->update([
                'title' => $this->title,
                'content' => $this->content,
            ]);
        } else {
            KnowledgeBase::create([
                'title' => $this->title,
                'content' => $this->content,
            ]);
        }

        $this->reset(['title', 'content', 'editingId']);
    }

    public function edit(int $id): void
    {
        $kb = KnowledgeBase::find($id);
        $this->editingId = $kb->id;
        $this->title = $kb->title;
        $this->content = $kb->content;
    }

    public function toggleActive(int $id): void
    {
        $kb = KnowledgeBase::find($id);
        $kb->update(['is_active' => ! $kb->is_active]);
    }

    public function delete(int $id): void
    {
        KnowledgeBase::find($id)->delete();
    }

    public function render()
    {
        return view('livewire.dashboard.knowledge-base-manager', [
            'items' => KnowledgeBase::latest()->get(),
        ])->layout('layouts.app');
    }
}
