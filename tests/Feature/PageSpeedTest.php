<?php

use App\Models\Project;
use App\Models\ProjectImage;
use App\Support\ResponsiveImage;
use Illuminate\Support\Facades\Storage;

function fakePng(int $width, int $height): string
{
    $image = imagecreatetruecolor($width, $height);
    imagefill($image, 0, 0, imagecolorallocate($image, 80, 70, 200));
    ob_start();
    imagepng($image);
    imagedestroy($image);

    return ob_get_clean();
}

it('makes WebP copies no wider than the original and builds a srcset from them', function () {
    Storage::fake('public');
    Storage::disk('public')->put('projects/demo/0.png', fakePng(1200, 600));

    expect(ResponsiveImage::attributes('projects/demo/0.png'))
        ->toBe(['src' => asset('storage/projects/demo/0.png'), 'srcset' => null]);

    expect(ResponsiveImage::generate('projects/demo/0.png'))->toBe(4)
        ->and(ResponsiveImage::generate('projects/demo/0.png'))->toBe(0);

    foreach ([480, 640, 960, 1200] as $width) {
        $file = Storage::disk('public')->path("projects/demo/0-{$width}w.webp");
        expect(getimagesize($file)[0])->toBe($width)
            ->and(getimagesize($file)['mime'])->toBe('image/webp');
    }

    $img = ResponsiveImage::attributes('projects/demo/0.png');
    expect($img['src'])->toBe(asset('storage/projects/demo/0-1200w.webp'))
        ->and($img['srcset'])->toBe(
            asset('storage/projects/demo/0-480w.webp').' 480w, '
            .asset('storage/projects/demo/0-640w.webp').' 640w, '
            .asset('storage/projects/demo/0-960w.webp').' 960w, '
            .asset('storage/projects/demo/0-1200w.webp').' 1200w'
        );

    ResponsiveImage::delete('projects/demo/0.png');
    expect(Storage::disk('public')->files('projects/demo'))->toBe(['projects/demo/0.png']);
});

it('URL-encodes file names with spaces so the srcset still parses', function () {
    Storage::fake('public');
    Storage::disk('public')->put('projects/demo/main copy.png', fakePng(300, 200));
    ResponsiveImage::generate('projects/demo/main copy.png');

    expect(ResponsiveImage::attributes('projects/demo/main copy.png')['srcset'])
        ->toBe(asset('storage/projects/demo/main%20copy-300w.webp').' 300w');
});

it('makes the copies on upload and removes them with the image', function () {
    Storage::fake('public');
    $project = Project::create(['name' => 'Demo', 'slug' => 'demo-'.uniqid(), 'description' => 'x']);
    Storage::disk('public')->put("projects/{$project->slug}/0.png", fakePng(500, 250));

    $image = ProjectImage::create(['project_id' => $project->id, 'filename' => '0.png', 'sort_order' => 0]);
    expect(Storage::disk('public')->exists("projects/{$project->slug}/0-480w.webp"))->toBeTrue()
        ->and(Storage::disk('public')->exists("projects/{$project->slug}/0-500w.webp"))->toBeTrue();

    $image->delete();
    expect(Storage::disk('public')->allFiles("projects/{$project->slug}"))->toBe([]);

    // Project::create also scaffolds a write-up view; clean it up.
    @unlink(resource_path("views/projects/projects/{$project->slug}/index.blade.php"));
    @rmdir(resource_path("views/projects/projects/{$project->slug}"));
});

it('self-hosts the font and loads the first carousel image straight away', function () {
    $html = $this->get('/')->assertOk()->getContent();

    expect($html)->not->toContain('fonts.bunny.net')
        ->and($html)->not->toContain('<link rel="preload" href=""');

    // One project's image is eager and high priority (in both the wide and narrow layouts); the rest stay lazy.
    expect(substr_count($html, 'fetchpriority="high"'))->toBe(2)
        ->and(substr_count($html, 'loading="lazy"'))->toBeGreaterThan(2);

    // The first two slides show before the carousel script runs.
    expect(substr_count($html, 'swiper-slide swiper-slide-active '))->toBe(1)
        ->and(substr_count($html, 'swiper-slide  swiper-slide-next '))->toBe(1);
});

it('preloads the self-hosted font from the Vite build', function () {
    if (! file_exists(public_path('build/manifest.json'))) {
        $this->markTestSkipped('No Vite build (run npm run build).');
    }

    $this->withVite();

    expect($this->get('/')->assertOk()->getContent())
        ->toMatch('/<link rel="preload" href="[^"]+\/build\/assets\/figtree-latin-wght-normal-[^"]+\.woff2" as="font" type="font\/woff2" crossorigin>/');
});
