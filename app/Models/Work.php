<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Work extends Model
{
    use HasTranslations;

    protected $fillable = [
        'category_id', 'slug', 'source_key', 'title', 'description', 'location',
        'event_date', 'is_featured', 'is_published',
    ];

    protected array $translatable = ['title', 'description'];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Work $work) {
            if (blank($work->slug)) {
                $base = Str::slug($work->tr('title', 'en') ?: $work->tr('title'))
                    ?: ($work->source_key ? 'post-'.Str::slug($work->source_key) : 'work');
                $slug = $base;
                $i = 2;
                while (static::where('slug', $slug)->whereKeyNot($work->getKey())->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $work->slug = $slug;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(WorkMedia::class)->orderBy('position')->orderBy('id');
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    /** Title in the current locale; imported posts without one fall back to their category. */
    public function name(): string
    {
        return $this->tr('title') ?: ($this->category?->tr('name') ?: 'Calone Perfume');
    }

    /** First image of the work, used as its cover. */
    public function cover(): ?WorkMedia
    {
        return $this->media->firstWhere('type', 'image') ?? $this->media->first();
    }
}
