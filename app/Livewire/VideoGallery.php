<?php

namespace App\Livewire;

use App\Models\Work;
use App\Models\WorkMedia;
use Livewire\Component;

class VideoGallery extends Component
{
    public int $limit = 12;

    public function mount(int $limit = 12): void
    {
        $this->limit = $limit;
    }

    public function loadMore(): void
    {
        $this->limit += 12;
    }

    public function render()
    {
        $query = WorkMedia::query()
            ->whereIn('type', ['video', 'embed'])
            ->whereHas('work', fn ($q) => $q->where('is_published', true))
            ->with('work.category')
            ->orderByDesc(Work::select('event_date')->whereColumn('works.id', 'work_media.work_id'))->latest('id');

        return view('livewire.video-gallery', [
            'videos' => (clone $query)->take($this->limit)->get(),
            'total' => (clone $query)->reorder()->count(),
        ]);
    }
}
