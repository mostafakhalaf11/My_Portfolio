<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /** Every seeder is idempotent — re-running updates rather than duplicates. */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            ProfileSeeder::class,
            SkillSeeder::class,
            ExperienceSeeder::class,
            CaseStudySeeder::class,
            CredentialSeeder::class,
            PageSectionSeeder::class,
        ]);
    }
}
