<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Work;
use Livewire\Attributes\Url;
use Livewire\Component;

class PortfolioGrid extends Component
{
    #[Url(as: 'category', except: '')]
    public string $category = '';

    public int $perPage = 9;

    public int $limit = 0;

    public function mount(int $perPage = 9): void
    {
        $this->perPage = $perPage;
        $this->limit = $perPage;
    }

    public function updatedCategory(): void
    {
        $this->limit = $this->perPage;
    }

    public function filter(string $slug): void
    {
        $this->category = $slug;
        $this->limit = $this->perPage;
    }

    public function loadMore(): void
    {
        $this->limit += $this->perPage;
    }

    public function render()
    {
        $query = Work::published()
            ->with(['media', 'category'])
            ->when($this->category, fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $this->category)))
            ->orderByDesc('is_featured')
            ->latest('event_date')
            ->latest('id');

        return view('livewire.portfolio-grid', [
            'categories' => Category::orderBy('position')->has('works')->get(),
            'works' => (clone $query)->take($this->limit)->get(),
            'total' => (clone $query)->reorder()->count(),
        ]);
    }
}
