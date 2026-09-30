<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Minimal Vercel Blob integration: the browser uploads files directly to Blob
 * (see resources/js/admin.js), the server only signs client tokens and deletes files.
 * Mirrors generateClientTokenFromReadWriteToken() from the @vercel/blob package.
 */
class VercelBlob
{
    public const API_URL = 'https://vercel.com/api/blob';

    public const API_VERSION = '12';

    public static function enabled(): bool
    {
        return filled(config('services.blob.token'));
    }

    public static function clientToken(string $pathname, array $allowedContentTypes, int $maximumSizeInBytes): string
    {
        $token = (string) config('services.blob.token');
        $storeId = explode('_', $token)[3] ?? '';

        $payload = base64_encode(json_encode([
            'pathname' => $pathname,
            'allowedContentTypes' => $allowedContentTypes,
            'maximumSizeInBytes' => $maximumSizeInBytes,
            'addRandomSuffix' => true,
            'validUntil' => (int) round(microtime(true) * 1000) + 3600 * 1000,
        ], JSON_UNESCAPED_SLASHES));

        $signature = hash_hmac('sha256', $payload, $token);

        return 'vercel_blob_client_'.$storeId.'_'.base64_encode($signature.'.'.$payload);
    }

    /** True when the URL points at a file in a Vercel Blob store. */
    public static function owns(?string $url): bool
    {
        $host = parse_url((string) $url, PHP_URL_HOST);

        return is_string($host) && str_ends_with($host, '.blob.vercel-storage.com');
    }

    public static function delete(string $url): void
    {
        if (! self::enabled() || ! self::owns($url)) {
            return;
        }

        try {
            Http::withToken(config('services.blob.token'))
                ->withHeaders(['x-api-version' => self::API_VERSION])
                ->post(self::API_URL.'/delete', ['urls' => [$url]]);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
