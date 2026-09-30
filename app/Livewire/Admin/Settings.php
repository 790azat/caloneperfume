<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts::admin')]
#[Title('Настройки')]
class Settings extends Component
{
    use WithFileUploads;

    public array $values = [];

    public $heroVideo = null;

    public function mount(): void
    {
        $this->values = array_map(fn ($v) => (string) $v, Setting::values());
    }

    public function setHeroVideo(string $kind, string $url): void
    {
        abort_unless($kind === 'video' && \App\Support\VercelBlob::owns($url), 422);
        $this->values['hero_video'] = $url;
        $this->save();
    }

    public function save(): void
    {
        $this->validate([
            'values.phone' => 'nullable|string|max:50',
            'values.email' => 'nullable|email|max:150',
            'values.address' => 'nullable|string|max:200',
            'values.hours' => 'nullable|string|max:100',
            'values.instagram' => 'nullable|url|max:300',
            'values.facebook' => 'nullable|url|max:300',
            'values.whatsapp' => 'nullable|string|max:50',
            'values.telegram' => 'nullable|string|max:100',
            'values.hero_video' => 'nullable|url|max:500',
            'values.stat_*' => 'nullable|integer|min:0',
            'heroVideo' => 'nullable|mimetypes:video/mp4,video/webm|max:102400',
        ]);

        if ($this->heroVideo) {
            $disk = config('filesystems.media_disk');
            $this->values['hero_video'] = \Illuminate\Support\Facades\Storage::disk($disk)
                ->url($this->heroVideo->store('site', $disk));
            $this->heroVideo = null;
        }

        Setting::put(collect($this->values)->only(Setting::KEYS)->all());
        $this->dispatch('toast', message: 'Настройки сохранены');
    }

    public function render()
    {
        return view('livewire.admin.settings');
    }
}
