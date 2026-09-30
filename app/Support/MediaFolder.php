<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Work;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Turns a folder of photos and videos into portfolio works.
 *
 * Files are grouped into one work per post: Instagram exports name carousel files
 * "<post id>_<media id>.jpg", so everything before the first "_" is the post key.
 * Any other file becomes a work of its own. Keys that were imported once are remembered
 * in `imported_keys`, so a work deleted in the admin panel is not imported again.
 */
class MediaFolder
{
    public const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    public const VIDEO_EXTENSIONS = ['mp4', 'mov', 'm4v', 'webm'];

    /** Group key for a file name: "3051034622250072370_3051034614431872802.jpg" -> "3051034622250072370". */
    public static function keyFor(string $filename): string
    {
        $base = pathinfo($filename, PATHINFO_FILENAME);
        $base = preg_replace('/\.fdash.*$/', '', $base);

        return Str::limit(Str::before($base, '_') ?: $base, 120, '');
    }

    public static function typeFor(string $filename): ?string
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return match (true) {
            in_array($ext, self::IMAGE_EXTENSIONS, true) => 'image',
            in_array($ext, self::VIDEO_EXTENSIONS, true) => 'video',
            default => null,
        };
    }

    /** Instagram ids carry their creation time: (id >> 23) ms after 2011-08-24. */
    public static function dateFor(string $key, ?int $fallbackTimestamp = null): ?string
    {
        if (preg_match('/^\d{17,19}$/', $key)) {
            $ms = ((int) $key >> 23) + 1314220021721;
            $date = CarbonImmutable::createFromTimestampMs($ms);
            if ($date->year >= 2011 && $date->isPast()) {
                return $date->toDateString();
            }
        }

        return $fallbackTimestamp ? date('Y-m-d', $fallbackTimestamp) : null;
    }

    public static function readable(?string $path): bool
    {
        return filled($path) && is_dir($path) && is_readable($path);
    }

    /**
     * @return array<string, array{files: list<string>, date: ?string, has_video: bool}>
     */
    public static function scan(string $path): array
    {
        $groups = [];
        foreach (scandir($path) ?: [] as $file) {
            if (! is_file($path.DIRECTORY_SEPARATOR.$file) || ! self::typeFor($file)) {
                continue;
            }
            $key = self::keyFor($file);
            $groups[$key]['files'][] = $file;
            $groups[$key]['mtime'] = max($groups[$key]['mtime'] ?? 0, (int) filemtime($path.DIRECTORY_SEPARATOR.$file));
        }

        foreach ($groups as $key => &$group) {
            sort($group['files']);
            $group['date'] = self::dateFor((string) $key, $group['mtime']);
            $group['has_video'] = collect($group['files'])->contains(fn ($f) => self::typeFor($f) === 'video');
            unset($group['mtime']);
        }

        krsort($groups);

        return $groups;
    }

    /** @param list<string> $keys @return list<string> keys that were imported before */
    public static function knownKeys(array $keys): array
    {
        return collect($keys)->chunk(500)
            ->flatMap(fn ($chunk) => DB::table('imported_keys')->whereIn('key', $chunk->values()->all())->pluck('key'))
            ->map(fn ($k) => (string) $k)
            ->values()
            ->all();
    }

    public static function remember(string $key): void
    {
        DB::table('imported_keys')->insertOrIgnore(['key' => $key, 'created_at' => now()]);
    }

    /**
     * Creates a work from already stored media.
     *
     * @param  list<array{type: string, url?: ?string, path?: ?string, thumb?: ?string, poster?: ?string}>  $media
     */
    public static function createWork(string $key, array $media, ?string $date = null, ?string $categorySlug = null): ?Work
    {
        if ($media === [] || in_array($key, self::knownKeys([$key]), true)) {
            return null;
        }

        $categorySlug ??= collect($media)->every(fn ($m) => $m['type'] === 'video') ? 'video' : 'fragrances';

        $work = Work::create([
            'source_key' => $key,
            'title' => [],
            'description' => [],
            'category_id' => Category::where('slug', $categorySlug)->value('id'),
            'event_date' => $date,
            'is_published' => true,
        ]);

        foreach (array_values($media) as $position => $item) {
            $work->media()->create([
                'type' => $item['type'],
                'url' => $item['url'] ?? null,
                'path' => $item['path'] ?? null,
                'thumb' => $item['thumb'] ?? null,
                'poster' => $item['poster'] ?? null,
                'position' => $position,
            ]);
        }

        self::remember($key);

        return $work;
    }

    /** Copies one group from a readable folder to the media disk and creates its work (server-side import). */
    public static function importGroup(string $path, string $key, array $group): ?Work
    {
        $disk = config('filesystems.media_disk');
        $media = [];

        foreach ($group['files'] as $file) {
            $target = 'works/import/'.Str::slug($key).'/'.Str::lower($file);
            $stream = fopen($path.DIRECTORY_SEPARATOR.$file, 'r');
            Storage::disk($disk)->put($target, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
            $media[] = ['type' => self::typeFor($file), 'path' => $target];
        }

        return self::createWork($key, $media, $group['date']);
    }
}
