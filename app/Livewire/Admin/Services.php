<?php

namespace App\Livewire\Admin;

use App\Models\Service;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Услуги')]
class Services extends Component
{
    public const ICONS = ['sparkles', 'star', 'heart', 'gift', 'truck', 'beaker', 'camera', 'briefcase', 'users', 'calendar'];

    public ?int $editing = null;

    public string $icon = 'sparkles';

    public array $title = ['hy' => '', 'ru' => '', 'en' => ''];

    public array $description = ['hy' => '', 'ru' => '', 'en' => ''];

    public function edit(?int $id = null): void
    {
        $this->resetValidation();
        $service = Service::find($id);
        $this->editing = $id ?? 0;
        $this->icon = $service->icon ?? 'sparkles';
        $this->title = array_merge(['hy' => '', 'ru' => '', 'en' => ''], (array) $service?->title);
        $this->description = array_merge(['hy' => '', 'ru' => '', 'en' => ''], (array) $service?->description);
    }

    public function save(): void
    {
        $this->validate([
            'title.ru' => 'required|string|max:150',
            'title.*' => 'nullable|string|max:150',
            'description.*' => 'nullable|string|max:1000',
            'icon' => 'in:'.implode(',', self::ICONS),
        ]);

        $service = Service::findOrNew($this->editing);
        $service->fill(['icon' => $this->icon, 'title' => $this->title, 'description' => $this->description]);
        if (! $service->exists) {
            $service->position = (int) Service::max('position') + 1;
        }
        $service->save();

        $this->editing = null;
        $this->dispatch('toast', message: 'Сохранено');
    }

    public function toggle(int $id): void
    {
        $service = Service::findOrFail($id);
        $service->update(['is_active' => ! $service->is_active]);
    }

    public function move(int $id, int $direction): void
    {
        $items = Service::orderBy('position')->get()->values();
        $index = $items->search(fn ($s) => $s->id === $id);
        $target = $index + $direction;
        if ($index === false || $target < 0 || $target >= $items->count()) {
            return;
        }
        $ordered = $items->all();
        [$ordered[$index], $ordered[$target]] = [$ordered[$target], $ordered[$index]];
        foreach ($ordered as $position => $service) {
            $service->update(['position' => $position]);
        }
    }

    public function delete(int $id): void
    {
        Service::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.admin.services', ['services' => Service::orderBy('position')->get(), 'icons' => self::ICONS]);
    }
}
