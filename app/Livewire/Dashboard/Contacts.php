<?php

namespace App\Livewire\Dashboard;

use App\Models\Contact;
use Livewire\Component;
use Livewire\WithPagination;

class Contacts extends Component
{
    use WithPagination;

    public string $search = '';

    public function render()
    {
        $contacts = Contact::query()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('wa_number', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(15);

        return view('livewire.dashboard.contacts', compact('contacts'))->layout('layouts.app');
    }
}
