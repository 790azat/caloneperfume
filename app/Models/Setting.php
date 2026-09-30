<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Throwable;

class Setting extends Model
{
    public const KEYS = ['phone', 'email', 'address', 'hours', 'media_folder', 'instagram', 'facebook', 'whatsapp', 'telegram', 'hero_video', 'stat_events', 'stat_guests', 'stat_years'];

    public const DEFAULTS = [
        'phone' => '+374 11 20 51 02',
        'email' => 'caloneperfume11@gmail.com',
        'address' => 'Yerevan, Armenia',
        'hours' => '10:30–21:30',
        // Working folder with photos and videos for "Import from folder" in the admin panel.
        'media_folder' => '',
        'instagram' => 'https://www.instagram.com/caloneperfume/',
        'facebook' => 'https://www.facebook.com/caloneperfume',
        'whatsapp' => '',
        'telegram' => '',
        'hero_video' => '',
        // Numbers for the counters on the home page; a counter is hidden while empty.
        'stat_events' => '',
        'stat_guests' => '17300',
        'stat_years' => '',
    ];

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = ['key', 'value'];

    public static function values(): array
    {
        try {
            $stored = Cache::rememberForever('settings', fn () => static::query()->pluck('value', 'key')->all());
        } catch (Throwable) {
            $stored = [];
        }

        return array_merge(self::DEFAULTS, array_filter($stored, fn ($v) => $v !== null));
    }

    public static function get(string $key): ?string
    {
        return static::values()[$key] ?? null;
    }

    public static function put(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        Cache::forget('settings');
    }
}
