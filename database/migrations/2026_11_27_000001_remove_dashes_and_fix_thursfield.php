<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * - Takes the em and en dashes out of the CV, in place so edits made at /cv/edit
     *   are kept: "### Role — Company" headings become "### Role | Company" (the
     *   separator App\Models\Cv now reads), date ranges read "2019 to 2022", and any
     *   other dash becomes a comma or hyphen.
     * - Thursfield Primary School linked to UC Santa Barbara's website and was
     *   placed in Lancashire; it is in Staffordshire and has no link.
     */
    public function up(): void
    {
        DB::table('cvs')->get(['id', 'data'])->each(function ($cv) {
            $data = json_decode($cv->data, true);

            foreach ($data as $key => $value) {
                if (is_string($value)) {
                    $data[$key] = self::undash($value);
                }
            }

            DB::table('cvs')->where('id', $cv->id)->update([
                'data' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]);
        });

        DB::table('education')->where('slug', 'thursfield-primary-school')->update([
            'location' => 'Thursfield, Staffordshire',
            'link' => '',
            'updated_at' => now(),
        ]);

        Cache::forget('nav.education');
    }

    /** The old punctuation and the wrong school link aren't worth restoring. */
    public function down(): void
    {
    }

    private static function undash(string $text): string
    {
        $dash = '[\x{2014}\x{2013}]';

        $text = preg_replace('/^(###[^\n]*?)\s+'.$dash.'\s+/mu', '$1 | ', $text);  // heading separator
        $text = preg_replace('/(\d{4})\s*'.$dash.'\s*/u', '$1 to ', $text);         // "2019 – 2022", "2022 – present"
        $text = preg_replace('/(\S)'.$dash.'(\S)/u', '$1 to $2', $text);             // "A*–C"
        $text = preg_replace('/\s+'.$dash.'\s+/u', ', ', $text);                    // a spaced dash mid-sentence

        return preg_replace('/'.$dash.'/u', '-', $text);
    }
};
