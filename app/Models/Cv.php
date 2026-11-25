<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * The CV shown at /cv. A single row; `data` holds the header fields plus one Markdown
 * text per section (see resources/views/cv/edit.blade.php for the conventions).
 */
class Cv extends Model
{
    protected $fillable = ['data'];

    protected $casts = [
        'data' => 'array',
    ];

    public const FIELDS = [
        'name', 'headline', 'location', 'email', 'phone', 'website', 'github', 'extra',
        'profile', 'skills', 'experience', 'projects', 'education', 'interests',
    ];

    public static function current(): self
    {
        return static::query()->oldest('id')->first() ?? new static(['data' => []]);
    }

    public function field(string $key, $default = ''): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /** Markdown to HTML. Raw HTML in the source is stripped, and javascript: links are dropped. */
    public static function markdown(?string $text): string
    {
        return Str::markdown((string) $text, ['html_input' => 'strip', 'allow_unsafe_links' => false]);
    }

    public static function inline(?string $text): string
    {
        return Str::inlineMarkdown((string) $text, ['html_input' => 'strip', 'allow_unsafe_links' => false]);
    }

    /**
     * "Area: item, item (with, commas), item" per line → [['area' => …, 'items' => […]], …].
     * Commas inside brackets stay with their item.
     */
    public function skillGroups(): array
    {
        $groups = [];
        foreach (preg_split('/\R/', (string) $this->field('skills')) as $line) {
            if (! str_contains($line, ':') || trim($line) === '') {
                continue;
            }
            [$area, $items] = array_map('trim', explode(':', $line, 2));
            $groups[] = [
                'area' => $area,
                'items' => array_values(array_filter(array_map('trim', preg_split('/,(?![^()]*\))/', $items)))),
            ];
        }

        return $groups;
    }

    /**
     * Splits a section on "### " headings. Each entry: heading split on " — " into title and org,
     * an optional one-line meta (the dates line straight under the heading) and the Markdown body.
     *
     * @return array{intro: string, entries: array<int, array{title: string, org: ?string, meta: ?string, body: string}>}
     */
    public function entries(string $section, bool $withMeta = true): array
    {
        $parts = preg_split('/^###[ \t]+/m', trim((string) $this->field($section)));
        $intro = trim((string) array_shift($parts));
        $entries = [];

        foreach ($parts as $part) {
            $lines = preg_split('/\R/', trim($part));
            $heading = trim((string) array_shift($lines));
            $rest = ltrim(implode("\n", $lines));
            $meta = null;

            if ($withMeta && preg_match('/\A([^\n]{1,90})(?:\n[ \t]*\n|\s*\z)/', $rest, $m)
                && ! preg_match('/^\s*([-*+]|\d+\.)\s/', $m[1])) {
                $meta = trim($m[1]);
                $rest = ltrim(substr($rest, strlen($m[0])));
            }

            $split = preg_split('/\s+[—–]\s+/u', $heading, 2);

            $entries[] = [
                'title' => $split[0],
                'org' => $split[1] ?? null,
                'meta' => $meta,
                'body' => $rest,
            ];
        }

        return ['intro' => $intro, 'entries' => $entries];
    }

    /** The whole CV as one Markdown document, for the download. */
    public function toMarkdown(bool $includePhone): string
    {
        $contact = array_filter([
            $this->field('location'),
            $includePhone ? $this->field('phone') : null,
            $this->field('email'),
            $this->field('website'),
            $this->field('github'),
            $this->field('extra'),
        ]);

        $skills = collect($this->skillGroups())
            ->map(fn ($g) => '- **'.$g['area'].':** '.implode(', ', $g['items']))
            ->implode("\n");

        return collect([
            '# '.$this->field('name'),
            $this->field('headline'),
            implode(' · ', $contact),
            "## Profile\n\n".trim($this->field('profile')),
            "## Skills\n\n".$skills,
            "## Experience\n\n".trim($this->field('experience')),
            "## Selected projects\n\n".trim($this->field('projects')),
            "## Education\n\n".trim($this->field('education')),
            "## Interests\n\n".trim($this->field('interests')),
        ])->filter(fn ($block) => trim($block) !== '')->implode("\n\n")."\n";
    }
}
