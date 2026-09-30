<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasTranslations;

    protected $fillable = ['author', 'text', 'event', 'rating', 'is_active'];

    protected array $translatable = ['text'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
