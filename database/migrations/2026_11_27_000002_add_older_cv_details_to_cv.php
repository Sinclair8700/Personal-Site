<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Adds details from Alex's older CVs to the CV at /cv: LinkedIn, more of the Designamite work,
     * the Novus and Internet Central details, the fibre map specifics, extra skills, coursework
     * projects and school activities. Each change is made in place and only once, so anything
     * edited at /cv/edit since is kept; a change whose original text has been edited away is skipped.
     */
    private const LINKEDIN = 'https://www.linkedin.com/in/alex-davies-aa10a7215/';

    /** [field, text to find, replacement, text that shows it's already applied] */
    private const EDITS = [
        ['profile',
            'I started out writing small desktop tools in C++ and C#,',
            'I started coding at 12, writing small desktop tools in C++ and C#,',
            'I started coding at 12'],
        ['experience',
            'for clients including Channel 4, Wickes and Dunelm.',
            'for clients including Channel 4, Wickes and Dunelm. The Channel 4 tests fed straight into major design decisions.',
            'fed straight into major design decisions'],
        ['experience',
            '- Meet clients regularly to agree what they need',
            "- **WordPress and email:** built and maintained WordPress sites and plugins, and HTML email templates.\n- Code, test and review sites, and own live launches, hotfixes and product support to deadline.\n- Meet clients regularly to agree what they need",
            'HTML email templates'],
        ['experience',
            '- Used the Google Maps API with JavaScript on the front end and PHP and PostgreSQL behind it.',
            "- Used the Google Maps API with JavaScript on the front end and PHP and PostgreSQL behind it.\n- It plots the fibre runs, distribution cabinets and end points, converting GPS coordinates into map positions.",
            'distribution cabinets and end points'],
        ['experience',
            'following strict configuration and security standards.',
            'joining them to Azure Active Directory and following strict configuration and security standards.',
            'Azure Active Directory'],
        ['experience',
            'with an Angular and TypeScript front end and a Java EE and SQL back end.',
            'with an Angular and TypeScript front end and a Java EE and SQL back end. It was adopted across the whole company.',
            'adopted across the whole company'],
        ['experience',
            'Found leads for fibre broadband and visited BT exchanges with a senior engineer.',
            'A placement at their office on the Keele University campus. I found leads for fibre broadband, helped with data erasure, liaised with clients, and visited BT exchanges with a senior engineer.',
            'Keele University campus'],
        ['education',
            'Over 95% in Advanced Programming Practices, Programming II (Data Structures and Algorithms) and Mobile Application Development.',
            'Over 95% in Advanced Programming Practices (a multithreaded Robot Wars simulator in Java), Programming II (Data Structures and Algorithms) and Mobile Application Development (an Android app for saving locations with notes).',
            'Robot Wars'],
        ['education',
            'Communications and Networks, and Computational and Artificial Intelligence.',
            'Communications and Networks, Digital Forensics, Cybercrime, and Computational and Artificial Intelligence.',
            'Digital Forensics'],
        ['education',
            'including A\* in Mathematics, Physics and Chemistry.',
            'including A\* in Mathematics, Physics and Chemistry, and A in English Language, English Literature, History and Electronic Products.',
            'English Literature'],
    ];

    /** Skills added to the end of an existing "Area: …" line when missing. */
    private const SKILLS = [
        'Languages' => ['C', 'SCSS'],
        'Web' => ['Preact'],
        'Data' => ['MSSQL'],
        'Cloud and DevOps' => ['Azure Active Directory', 'GitLab', 'Bitbucket'],
        'Hardware' => ['PICAXE microcontrollers'],
    ];

    private const INTERESTS = 'At school I was an IT Ambassador, passing feedback between students and the IT department, and I played for the hockey team and was in the Scouts and the Combined Cadet Force.';

    public function up(): void
    {
        DB::table('cvs')->get(['id', 'data'])->each(function ($row) {
            $data = json_decode($row->data, true) ?: [];

            if (empty($data['linkedin'])) {
                $data['linkedin'] = self::LINKEDIN;
            }

            foreach (self::EDITS as [$field, $find, $replace, $marker]) {
                $text = (string) ($data[$field] ?? '');
                if (! str_contains($text, $marker) && str_contains($text, $find)) {
                    $data[$field] = preg_replace('/'.preg_quote($find, '/').'/', addcslashes($replace, '\\$'), $text, 1);
                }
            }

            $data['skills'] = self::addSkills((string) ($data['skills'] ?? ''));

            $interests = trim((string) ($data['interests'] ?? ''));
            if ($interests !== '' && ! str_contains($interests, 'IT Ambassador')) {
                $data['interests'] = $interests.' '.self::INTERESTS;
            }

            DB::table('cvs')->where('id', $row->id)->update([
                'data' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
        });
    }

    /** Content additions aren't worth reverting automatically; edit at /cv/edit instead. */
    public function down(): void
    {
    }

    private static function addSkills(string $skills): string
    {
        $lines = preg_split('/\R/', $skills);

        foreach ($lines as $i => $line) {
            if (! str_contains($line, ':')) {
                continue;
            }
            [$area, $items] = array_map('trim', explode(':', $line, 2));
            $have = array_map(fn ($s) => mb_strtolower(trim($s)), preg_split('/,(?![^()]*\))/', $items));
            $missing = array_filter(self::SKILLS[$area] ?? [], fn ($s) => ! in_array(mb_strtolower($s), $have, true));

            if ($missing) {
                $lines[$i] = $area.': '.$items.', '.implode(', ', $missing);
            }
        }

        return implode("\n", $lines);
    }
};
