<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::app')]
class Account extends Component
{
    public function render()
    {
        return view('livewire.account', [
            'inquiries' => auth()->user()->inquiries()->latest()->get(),
        ])->title(__('site.account.title'));
    }
}
