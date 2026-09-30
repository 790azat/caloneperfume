<?php

namespace App\Models;

use App\Support\VercelBlob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class WorkMedia extends Model
{
    protected $fillable = ['work_id', 'type', 'path', 'url', 'thumb', 'poster', 'caption', 'position'];

    protected static function booted(): void
    {
        static::deleted(function (WorkMedia $media) {
            if ($media->path) {
                Storage::disk(config('filesystems.media_disk'))->delete($media->path);
            }
            foreach ([$media->url, $media->thumb, $media->poster] as $url) {
                if ($url) {
                    VercelBlob::delete($url);
                }
            }
        });
    }

    public function work(): BelongsTo
    {
        return $this->belongsTo(Work::class);
    }

    public function src(): string
    {
        return $this->path
            ? Storage::disk(config('filesystems.media_disk'))->url($this->path)
            : (string) $this->url;
    }

    /** YouTube / Vimeo embed URL for `embed` media, or null. */
    public function embedUrl(): ?string
    {
        $url = (string) $this->url;

        if (preg_match('~(?:youtube\.com/(?:watch\?v=|shorts/|embed/)|youtu\.be/)([\w-]{11})~', $url, $m)) {
            return "https://www.youtube.com/embed/{$m[1]}?rel=0";
        }
        if (preg_match('~vimeo\.com/(\d+)~', $url, $m)) {
            return "https://player.vimeo.com/video/{$m[1]}?dnt=1";
        }

        return null;
    }

    public function thumbnail(): ?string
    {
        if ($this->type === 'image') {
            return $this->thumb ?: $this->src();
        }
        if ($this->type === 'video') {
            return $this->poster;
        }
        if ($this->type === 'embed' && preg_match('~embed/([\w-]{11})~', (string) $this->embedUrl(), $m)) {
            return "https://i.ytimg.com/vi/{$m[1]}/hqdefault.jpg";
        }

        return null;
    }
}
