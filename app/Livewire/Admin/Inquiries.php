<?php

namespace App\Livewire\Admin;

use App\Models\Inquiry;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Заявки')]
class Inquiries extends Component
{
    use WithPagination;

    #[Url]
    public string $status = '';

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function setStatus(int $id, string $status): void
    {
        abort_unless(in_array($status, ['new', 'in_progress', 'done'], true), 400);
        Inquiry::whereKey($id)->update(['status' => $status]);
    }

    public function delete(int $id): void
    {
        Inquiry::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.admin.inquiries', [
            'inquiries' => Inquiry::with('user')->when($this->status, fn ($q) => $q->where('status', $this->status))->latest()->paginate(20),
            'counts' => Inquiry::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }
}
