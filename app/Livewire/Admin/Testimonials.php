<?php

namespace App\Livewire\Admin;

use App\Models\Testimonial;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Отзывы')]
class Testimonials extends Component
{
    public ?int $editing = null;

    public string $author = '';

    public string $event = '';

    public int $rating = 5;

    public array $text = ['hy' => '', 'ru' => '', 'en' => ''];

    public function edit(?int $id = null): void
    {
        $this->resetValidation();
        $item = Testimonial::find($id);
        $this->editing = $id ?? 0;
        $this->author = (string) $item?->author;
        $this->event = (string) $item?->event;
        $this->rating = $item->rating ?? 5;
        $this->text = array_merge(['hy' => '', 'ru' => '', 'en' => ''], (array) $item?->text);
    }

    public function save(): void
    {
        $this->validate([
            'author' => 'required|string|max:100',
            'event' => 'nullable|string|max:100',
            'rating' => 'integer|min:1|max:5',
            'text.ru' => 'required_without_all:text.hy,text.en|nullable|string|max:1000',
            'text.*' => 'nullable|string|max:1000',
        ]);

        Testimonial::findOrNew($this->editing)->fill([
            'author' => $this->author,
            'event' => $this->event ?: null,
            'rating' => $this->rating,
            'text' => $this->text,
        ])->save();

        $this->editing = null;
        $this->dispatch('toast', message: 'Сохранено');
    }

    public function toggle(int $id): void
    {
        $item = Testimonial::findOrFail($id);
        $item->update(['is_active' => ! $item->is_active]);
    }

    public function delete(int $id): void
    {
        Testimonial::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.admin.testimonials', ['items' => Testimonial::latest()->get()]);
    }
}
