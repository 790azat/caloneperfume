<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    protected $fillable = [
        'user_id', 'name', 'phone', 'email', 'event_type',
        'event_date', 'guests', 'message', 'status',
    ];

    protected function casts(): array
    {
        return ['event_date' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
