<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The site moves from alexdavi.es to alex-davies.com. Rewrites the site links
     * stored in the database (project links and the CV) in place, so edits made
     * at /cv/edit are kept. Email addresses (anything after an "@") are not
     * touched: mail stays on alexdavi.es.
     */
    private const OLD = 'alexdavi.es';

    private const NEW = 'alex-davies.com';

    public function up(): void
    {
        $this->swap(self::OLD, self::NEW);
    }

    public function down(): void
    {
        $this->swap(self::NEW, self::OLD);
    }

    private function swap(string $from, string $to): void
    {
        DB::table('projects')->where('link', 'like', '%'.$from.'%')->get(['id', 'link'])
            ->each(fn ($project) => DB::table('projects')->where('id', $project->id)
                ->update(['link' => self::replace($project->link, $from, $to)]));

        DB::table('cvs')->get(['id', 'data'])->each(function ($cv) use ($from, $to) {
            $data = json_decode($cv->data, true);

            foreach ($data as $key => $value) {
                if ($key !== 'email' && is_string($value)) {
                    $data[$key] = self::replace($value, $from, $to);
                }
            }

            DB::table('cvs')->where('id', $cv->id)->update([
                'data' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]);
        });

        Cache::forget('nav.projects');
    }

    private static function replace(string $text, string $from, string $to): string
    {
        return preg_replace('/(?<![@\w.-])'.preg_quote($from, '/').'/i', $to, $text);
    }
};
