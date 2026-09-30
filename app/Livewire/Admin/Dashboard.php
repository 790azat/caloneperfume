<?php

namespace App\Livewire\Admin;

use App\Models\Inquiry;
use App\Models\User;
use App\Models\Work;
use App\Models\WorkMedia;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Обзор')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'stats' => [
                ['Работ', Work::count(), 'photo', route('admin.works')],
                ['Фото', WorkMedia::where('type', 'image')->count(), 'camera', route('admin.works')],
                ['Видео', WorkMedia::whereIn('type', ['video', 'embed'])->count(), 'video', route('admin.works')],
                ['Новых заявок', Inquiry::where('status', 'new')->count(), 'inbox', route('admin.inquiries')],
                ['Пользователей', User::count(), 'users', route('admin.users')],
                ['Просмотров работ', (int) Work::sum('views'), 'eye', route('admin.works')],
            ],
            'inquiries' => Inquiry::latest()->take(6)->get(),
            'popular' => Work::orderByDesc('views')->take(5)->get(),
        ]);
    }
}
