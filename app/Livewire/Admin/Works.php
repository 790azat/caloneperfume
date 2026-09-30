<?php

namespace App\Livewire\Admin;

use App\Models\Work;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Работы и видео')]
class Works extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function toggle(int $id, string $field): void
    {
        abort_unless(in_array($field, ['is_published', 'is_featured'], true), 400);
        $work = Work::findOrFail($id);
        $work->update([$field => ! $work->{$field}]);
    }

    public function delete(int $id): void
    {
        $work = Work::with('media')->findOrFail($id);
        $work->media->each->delete(); // removes files from storage
        $work->delete();
        $this->dispatch('toast', message: 'Работа удалена');
    }

    /** Titles are JSON with escaped unicode, so match them in PHP (the list is small). */
    private function matchingIds(): array
    {
        $needle = mb_strtolower($this->search);

        return Work::query()->get(['id', 'title', 'location'])
            ->filter(fn (Work $work) => str_contains(
                mb_strtolower(implode(' ', (array) $work->title).' '.$work->location),
                $needle,
            ))
            ->modelKeys();
    }

    public function render()
    {
        return view('livewire.admin.works', [
            'works' => Work::with(['media', 'category'])
                ->when($this->search, fn ($q) => $q->whereKey($this->matchingIds()))
                ->latest()
                ->paginate(12),
        ]);
    }
}
