<?php

namespace App\Console\Commands;

use App\Support\MediaFolder;
use Illuminate\Console\Command;

/**
 * Builds database/data/instagram.json, the starter content imported on deploy.
 *
 * Images must already be converted into public/media/p/<post>/<name>.webp (+ -t.webp preview),
 * video posters into public/media/p/<post>/<name>-poster.webp, and the videos uploaded to Blob
 * (their URLs listed in database/data/videos.json by scripts/push-media.mjs).
 */
class BuildMediaManifest extends Command
{
    protected $signature = 'calone:manifest {folder : Folder with the original Instagram files} {--picks= : JSON with categories/titles per post key}';

    protected $description = 'Build the starter content manifest from the media folder';

    public function handle(): int
    {
        $folder = $this->argument('folder');
        if (! MediaFolder::readable($folder)) {
            $this->error('Folder not readable');

            return self::FAILURE;
        }

        $videos = json_decode((string) @file_get_contents(database_path('data/videos.json')), true) ?: [];
        $picks = $this->option('picks') ? json_decode(file_get_contents($this->option('picks')), true) : [];

        $posts = [];
        $skipped = 0;
        foreach (MediaFolder::scan($folder) as $key => $group) {
            $key = (string) $key;
            $media = [];
            foreach ($group['files'] as $file) {
                $base = preg_replace('/\.fdash.*$/', '', pathinfo($file, PATHINFO_FILENAME));
                $dir = "/media/p/{$key}/";
                if (MediaFolder::typeFor($file) === 'image') {
                    if (! is_file(public_path($dir.$base.'.webp'))) {
                        continue 2;
                    }
                    $media[] = ['type' => 'image', 'url' => $dir.$base.'.webp', 'thumb' => $dir.$base.'-t.webp'];
                } else {
                    if (empty($videos[$base])) {
                        $skipped++;

                        continue 2; // not uploaded yet, the post is imported on a later deploy
                    }
                    $media[] = ['type' => 'video', 'url' => $videos[$base], 'poster' => $dir.$base.'-poster.webp'];
                }
            }

            $pick = $picks[$key] ?? [];
            $posts[] = array_filter([
                'key' => $key,
                'date' => $group['date'],
                'category' => $pick['category'] ?? ($group['has_video'] && collect($media)->every(fn ($m) => $m['type'] === 'video') ? 'video' : 'fragrances'),
                'title' => $pick['title'] ?? null,
                'featured' => $pick['featured'] ?? null,
                'media' => $media,
            ], fn ($v) => $v !== null);
        }

        file_put_contents(
            database_path('data/instagram.json'),
            json_encode(['posts' => $posts], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
        );

        $this->info(count($posts).' posts written, '.$skipped.' waiting for video upload');

        return self::SUCCESS;
    }
}
