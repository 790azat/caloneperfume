<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasTranslations;

    protected $fillable = ['slug', 'name', 'position'];

    protected array $translatable = ['name'];

    public function works(): HasMany
    {
        return $this->hasMany(Work::class);
    }
}
