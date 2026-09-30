<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Imports the posts listed in database/data/instagram.json (built by `php artisan calone:manifest`).
 * Posts imported once are remembered in `imported_keys`, so re-running only adds new ones
 * and never brings back a post the owner deleted. Uses bulk inserts: it runs inside a web request on Vercel.
 */
class InstagramSeeder extends Seeder
{
    public function run(): void
    {
        $posts = collect(json_decode((string) @file_get_contents(database_path('data/instagram.json')), true)['posts'] ?? []);
        if ($posts->isEmpty()) {
            return;
        }

        $known = DB::table('imported_keys')->pluck('key')->map(fn ($k) => (string) $k)->flip();
        $posts = $posts->reject(fn ($post) => isset($known[$post['key']]));
        $categories = Category::pluck('id', 'slug');
        $now = now();

        foreach ($posts->chunk(200) as $chunk) {
            DB::transaction(function () use ($chunk, $categories, $now) {
                DB::table('works')->insertOrIgnore($chunk->map(fn ($post) => [
                    'source_key' => $post['key'],
                    'slug' => 'post-'.$post['key'],
                    'title' => json_encode($post['title'] ?? [], JSON_UNESCAPED_UNICODE),
                    'description' => json_encode([]),
                    'category_id' => $categories[$post['category'] ?? 'fragrances'] ?? null,
                    'event_date' => $post['date'] ?? null,
                    'is_featured' => (bool) ($post['featured'] ?? false),
                    'is_published' => true,
                    'views' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->values()->all());

                $ids = DB::table('works')->whereIn('source_key', $chunk->pluck('key')->all())->pluck('id', 'source_key');

                $media = [];
                foreach ($chunk as $post) {
                    foreach (array_values($post['media']) as $position => $item) {
                        $media[] = [
                            'work_id' => $ids[$post['key']],
                            'type' => $item['type'],
                            'url' => $item['url'],
                            'thumb' => $item['thumb'] ?? null,
                            'poster' => $item['poster'] ?? null,
                            'position' => $position,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
                foreach (array_chunk($media, 300) as $rows) {
                    DB::table('work_media')->insert($rows);
                }

                DB::table('imported_keys')->insertOrIgnore(
                    $chunk->map(fn ($post) => ['key' => $post['key'], 'created_at' => $now])->values()->all(),
                );
            });
        }
    }
}
