<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use App\Support\MediaFolder;
use App\Support\VercelBlob;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * "Import from folder": the owner points the site at a working folder of photos and videos.
 * When the server can read the folder (local run) it imports new files itself;
 * on Vercel the owner picks the folder in the browser and files go straight to Blob.
 */
#[Layout('layouts::admin')]
#[Title('Импорт из папки')]
class Import extends Component
{
    public string $folder = '';

    public ?array $summary = null;

    public int $imported = 0;

    public function mount(): void
    {
        $this->folder = (string) Setting::get('media_folder');
        if (MediaFolder::readable($this->folder)) {
            $this->scan();
        }
    }

    public function saveFolder(): void
    {
        $this->validate(['folder' => 'nullable|string|max:500']);
        $this->folder = trim($this->folder, " \t\n\r\0\x0B\"'");
        Setting::put(['media_folder' => $this->folder]);
        $this->scan();
        $this->dispatch('toast', message: 'Папка сохранена');
    }

    public function scan(): void
    {
        $this->summary = null;
        if (! MediaFolder::readable($this->folder)) {
            return;
        }

        $groups = MediaFolder::scan($this->folder);
        $known = MediaFolder::knownKeys(array_map('strval', array_keys($groups)));
        $new = array_diff_key($groups, array_flip($known));

        $this->summary = [
            'posts' => count($groups),
            'images' => collect($groups)->sum(fn ($g) => collect($g['files'])->filter(fn ($f) => MediaFolder::typeFor($f) === 'image')->count()),
            'videos' => collect($groups)->sum(fn ($g) => collect($g['files'])->filter(fn ($f) => MediaFolder::typeFor($f) === 'video')->count()),
            'new' => count($new),
        ];
    }

    /** Imports the next batch of new posts from the server-readable folder; the view calls it until nothing is left. */
    public function importBatch(int $size = 10): int
    {
        abort_unless(MediaFolder::readable($this->folder), 422);

        $groups = MediaFolder::scan($this->folder);
        $known = array_flip(MediaFolder::knownKeys(array_map('strval', array_keys($groups))));
        $done = 0;

        foreach ($groups as $key => $group) {
            if (isset($known[(string) $key])) {
                continue;
            }
            if (MediaFolder::importGroup($this->folder, (string) $key, $group)) {
                $done++;
            }
            if ($done >= $size) {
                break;
            }
        }

        $this->imported += $done;
        $this->scan();

        return $this->summary['new'] ?? 0;
    }

    /** Browser import: which of these keys are already on the site. */
    public function knownKeys(array $keys): array
    {
        return MediaFolder::knownKeys(array_map('strval', array_slice($keys, 0, 5000)));
    }

    /** Browser import: files of one post were uploaded to Blob, create the work. */
    public function importUploaded(string $key, array $items, ?int $lastModified = null): bool
    {
        $media = collect($items)
            ->filter(fn ($item) => in_array($item['type'] ?? null, ['image', 'video'], true) && VercelBlob::owns($item['url'] ?? null))
            ->map(fn ($item) => ['type' => $item['type'], 'url' => $item['url']])
            ->values()
            ->all();

        $key = mb_substr($key, 0, 120);
        $work = MediaFolder::createWork($key, $media, MediaFolder::dateFor($key, $lastModified ? intdiv($lastModified, 1000) : null));
        if ($work) {
            $this->imported++;
        }

        return (bool) $work;
    }

    public function render()
    {
        return view('livewire.admin.import', [
            'readable' => MediaFolder::readable($this->folder),
            'blob' => VercelBlob::enabled(),
        ]);
    }
}
