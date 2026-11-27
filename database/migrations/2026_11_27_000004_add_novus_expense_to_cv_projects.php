<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Adds Novus Expense to the CV's selected projects, after ASM Sim & Learn (before Lil Chongee
     * & Bud-Bro), or at the end if that entry has been edited away. Only once; other edits are kept.
     */
    private const ENTRY = <<<'MD'
### [Novus Expense](https://alex-davies.com/projects/novus-expense)

The expenses and travel booking system I built for Novus Property Solutions in 2019. Staff claim back expenses and mileage, approvers authorise claims and finance reviews them, and staff book accommodation, trains, ferries and flights for themselves or a colleague. Built in Angular and TypeScript with a Java EE and SQL back end, and adopted across the whole company.
MD;

    public function up(): void
    {
        DB::table('cvs')->get(['id', 'data'])->each(function ($row) {
            $data = json_decode($row->data, true) ?: [];
            $projects = (string) ($data['projects'] ?? '');

            if (str_contains($projects, 'projects/novus-expense')) {
                return;
            }

            $before = '### [Lil Chongee & Bud-Bro]';
            $data['projects'] = str_contains($projects, $before)
                ? preg_replace('/'.preg_quote($before, '/').'/', addcslashes(self::ENTRY."\n\n".$before, '\\$'), $projects, 1)
                : rtrim($projects)."\n\n".self::ENTRY;

            DB::table('cvs')->where('id', $row->id)->update([
                'data' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
        });
    }

    public function down(): void
    {
        DB::table('cvs')->get(['id', 'data'])->each(function ($row) {
            $data = json_decode($row->data, true) ?: [];
            $data['projects'] = str_replace(self::ENTRY."\n\n", '', (string) ($data['projects'] ?? ''));
            DB::table('cvs')->where('id', $row->id)->update(['data' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
        });
    }
};
