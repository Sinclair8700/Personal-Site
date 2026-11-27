<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Smaller WebP copies of an image on the public disk, for srcset.
 *
 * projects/scenic-routes/0.png gets projects/scenic-routes/0-480w.webp, 0-640w.webp, 0-960w.webp
 * and 0-1440w.webp next to it (never wider than the original). Views call attributes() and get
 * the WebP srcset when the copies exist, or the original when they don't.
 */
class ResponsiveImage
{
    public const WIDTHS = [480, 640, 960, 1440];

    private const QUALITY = 80;

    /** @var array<string, array<int, string>> */
    private static array $found = [];

    /**
     * src and srcset for an <img>. Pair with a sizes attribute in the view.
     *
     * @return array{src: string, srcset: string|null}
     */
    public static function attributes(string $path): array
    {
        $variants = self::variants($path);

        if ($variants === []) {
            return ['src' => self::url($path), 'srcset' => null];
        }

        $srcset = collect($variants)->map(fn ($variant, $width) => self::url($variant).' '.$width.'w')->implode(', ');

        return ['src' => self::url(end($variants)), 'srcset' => $srcset];
    }

    /**
     * The WebP copies that exist for $path, keyed by width, smallest first.
     *
     * @return array<int, string>
     */
    public static function variants(string $path): array
    {
        if (isset(self::$found[$path])) {
            return self::$found[$path];
        }

        $disk = Storage::disk('public');
        $stem = self::stem($path);
        $variants = [];
        foreach (glob(self::escapeGlob($disk->path($stem)).'-*w.webp') ?: [] as $file) {
            if (preg_match('/-(\d+)w\.webp$/', $file, $m)) {
                $variants[(int) $m[1]] = $stem.'-'.$m[1].'w.webp';
            }
        }
        ksort($variants);

        return self::$found[$path] = $variants;
    }

    /**
     * Make the WebP copies for $path that don't exist yet. Returns how many were written.
     * Never throws: a failed resize just means the page keeps using the original.
     */
    public static function generate(string $path): int
    {
        $disk = Storage::disk('public');
        if (! function_exists('imagewebp') || ! $disk->exists($path)) {
            return 0;
        }

        try {
            $source = @imagecreatefromstring($disk->get($path));
            if ($source === false) {
                return 0;
            }

            $width = imagesx($source);
            $height = imagesy($source);
            $targets = array_values(array_unique(array_map(fn ($w) => min($w, $width), self::WIDTHS)));

            $written = 0;
            foreach ($targets as $target) {
                $variant = self::stem($path).'-'.$target.'w.webp';
                if ($disk->exists($variant)) {
                    continue;
                }

                $targetHeight = max(1, (int) round($height * $target / $width));
                $resized = imagecreatetruecolor($target, $targetHeight);
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                imagecopyresampled($resized, $source, 0, 0, 0, 0, $target, $targetHeight, $width, $height);

                ob_start();
                imagewebp($resized, null, self::QUALITY);
                $disk->put($variant, ob_get_clean());
                imagedestroy($resized);
                $written++;
            }
            imagedestroy($source);
        } catch (Throwable $e) {
            report($e);

            return $written ?? 0;
        } finally {
            unset(self::$found[$path]);
        }

        return $written;
    }

    public static function delete(string $path): void
    {
        $disk = Storage::disk('public');
        foreach (self::variants($path) as $variant) {
            $disk->delete($variant);
        }
        unset(self::$found[$path]);
    }

    private static function stem(string $path): string
    {
        $info = pathinfo($path);

        return ($info['dirname'] === '.' ? '' : $info['dirname'].'/').$info['filename'];
    }

    // Each path segment URL-encoded, so a file like "main copy.png" still parses in a srcset.
    private static function url(string $path): string
    {
        return asset('storage/'.implode('/', array_map('rawurlencode', explode('/', $path))));
    }

    private static function escapeGlob(string $path): string
    {
        return preg_replace('/([*?\[\]])/', '[$1]', $path);
    }
}
