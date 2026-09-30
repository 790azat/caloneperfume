<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Пользователи')]
class Users extends Component
{
    use WithPagination;

    public function toggleAdmin(int $id): void
    {
        abort_if($id === auth()->id(), 400, 'Нельзя снять права с самого себя.');
        $user = User::findOrFail($id);
        $user->update(['is_admin' => ! $user->is_admin]);
    }

    public function render()
    {
        return view('livewire.admin.users', ['users' => User::withCount('inquiries')->latest()->paginate(25)]);
    }
}
