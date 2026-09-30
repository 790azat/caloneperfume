<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasTranslations;

    protected $fillable = ['icon', 'title', 'description', 'position', 'is_active'];

    protected array $translatable = ['title', 'description'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
