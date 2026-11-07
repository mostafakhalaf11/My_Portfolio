<?php

use Database\Seeders\CaseStudySeeder;
use Database\Seeders\ExperienceSeeder;
use Database\Seeders\PageSectionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Brings databases seeded before the November 2026 copy review up to the new seeder content:
 * framework-free hero, no exact employer figures, Stock Yard as B2B and B2C, the profile photo.
 *
 * Production is seeded once and then edited in /admin, so a field is overwritten only while it
 * still holds the value originally seeded — matched by the sha1 below. Anything edited by hand
 * keeps its edit. On a fresh database the tables are empty, so this does nothing and the
 * seeders write the new content directly.
 */
return new class extends Migration
{
    /** @var array<string, array<string, array<string, string>>> sha1 of each originally seeded value */
    private const SEEDED = [
        'case_studies' => [
            'amg-erp-multi-tenant-hrm-payroll' => [
                'summary' => 'cbc5130dc50a8a0bc643dd0cd50e68491be834b8',
                'context' => '10736a9cdd9f089aef634feeca9fb90bb51b30a6',
                'implementation' => '9a413199cc4818e7bbe595ae5d14dd3e7b7f19e9',
                'outcomes' => '51eb99c81f233b8b4b456bba64112a47fbb733fd',
            ],
            'stock-yard-commerce-rfq' => [
                'title' => '0f11c4fbabc813d8a965a7443853db1ac8ef290b',
                'client_label' => '1d067702224020c6d616caf2c189a2390a208913',
                'role' => '3ceb84c1c97f32a5a953f61780643a9605487e70',
                'summary' => '914d65493d02102c8ae6558cd49b091b06b2d66a',
                'context' => '2bb5298879428a583a607ec3f91fd8696f51e8e2',
                'implementation' => '27e2910c599ce4a1e0d029fdf8c1d7049381ce0e',
                'outcomes' => 'ef32f21ea6020dece5bfe8e2b08bb61fa03af189',
            ],
        ],
        'experiences' => [
            'Full-Stack Developer|2025-07-01' => [
                'company' => '4ee8d6aecc5a5f5db4b6fd83ea3ea86d7276bcc8',
                'highlights' => '45f3af29e17b76c7ac2106142fd9f57ca142a4df',
            ],
        ],
        'page_sections' => [
            'hero' => [
                'heading' => '717509f76031e28a9d536088084c7ab4f0ba4136',
                'subheading' => 'aa0046c37f6a05fe865bef0f1d4410eda0768bf2',
                'settings' => '9df702a5c4c55acd9584f741616ecfd40c11c10f',
            ],
            'case_studies' => [
                'subheading' => '5503a104abd4024fbca2e94286200a4b2bb484be',
            ],
            'stack' => [
                'settings' => '8a2aa68c7c586c27d558072c325da750ba2f8b58',
            ],
        ],
    ];

    public function up(): void
    {
        foreach (CaseStudySeeder::STUDIES as $study) {
            $this->refresh('case_studies', ['slug' => $study['slug']], self::SEEDED['case_studies'][$study['slug']] ?? [], $study);
        }

        foreach (ExperienceSeeder::ENTRIES as $entry) {
            $id = $entry['title'].'|'.$entry['started_on'];
            $row = DB::table('experiences')->where('title', $entry['title'])->whereDate('started_on', $entry['started_on'])->first();

            if ($row !== null) {
                $this->refresh('experiences', ['id' => $row->id], self::SEEDED['experiences'][$id] ?? [], $entry);
            }
        }

        foreach (PageSectionSeeder::SECTIONS as $section) {
            $this->refresh('page_sections', ['page' => 'home', 'key' => $section['key']], self::SEEDED['page_sections'][$section['key']] ?? [], $section);
        }

        DB::table('profiles')->whereNull('avatar_path')->update(['avatar_path' => 'images/mostafa-khalaf.webp']);
    }

    /**
     * Content migrations are one-way: the previous copy is not worth restoring automatically.
     */
    public function down(): void {}

    /**
     * @param  array<string, mixed>  $where
     * @param  array<string, string>  $seededHashes
     * @param  array<string, mixed>  $target
     */
    private function refresh(string $table, array $where, array $seededHashes, array $target): void
    {
        $row = DB::table($table)->where($where)->first();

        if ($row === null) {
            return;
        }

        $changes = [];

        foreach ($seededHashes as $field => $hash) {
            $current = $row->{$field};
            $isJson = is_array($target[$field]);

            if (sha1($this->canonical($isJson ? json_decode((string) $current, true) : $current)) !== $hash) {
                continue; // edited by hand since it was seeded: leave it alone
            }

            $changes[$field] = $isJson ? json_encode($target[$field]) : $target[$field];
        }

        if ($changes !== []) {
            DB::table($table)->where($where)->update([...$changes, 'updated_at' => now()]);
        }
    }

    /** Stable string form: JSON objects get sorted keys, because MySQL reorders them on save. */
    private function canonical(mixed $value): string
    {
        $sort = function (mixed $item) use (&$sort): mixed {
            if (! is_array($item)) {
                return $item;
            }

            $item = array_map($sort, $item);

            if (! array_is_list($item)) {
                ksort($item);
            }

            return $item;
        };

        return match (true) {
            is_array($value) => (string) json_encode($sort($value), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            is_bool($value) => $value ? '1' : '0',
            $value === null => '',
            default => (string) $value,
        };
    }
};
