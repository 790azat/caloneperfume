<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Категории')]
class Categories extends Component
{
    public ?int $editing = null;

    public array $name = ['hy' => '', 'ru' => '', 'en' => ''];

    public function edit(?int $id = null): void
    {
        $this->resetValidation();
        $this->editing = $id ?? 0;
        $this->name = array_merge(['hy' => '', 'ru' => '', 'en' => ''], (array) Category::find($id)?->name);
    }

    public function save(): void
    {
        $this->validate(['name.ru' => 'required|string|max:100', 'name.hy' => 'nullable|max:100', 'name.en' => 'nullable|max:100']);

        $category = Category::findOrNew($this->editing);
        $category->name = $this->name;
        if (! $category->exists) {
            $slug = Str::slug($this->name['en'] ?: $this->name['ru']) ?: 'category';
            $category->slug = Category::where('slug', $slug)->exists() ? $slug.'-'.Str::lower(Str::random(4)) : $slug;
            $category->position = (int) Category::max('position') + 1;
        }
        $category->save();

        $this->editing = null;
        $this->dispatch('toast', message: 'Сохранено');
    }

    public function delete(int $id): void
    {
        Category::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.admin.categories', ['categories' => Category::withCount('works')->orderBy('position')->get()]);
    }
}
