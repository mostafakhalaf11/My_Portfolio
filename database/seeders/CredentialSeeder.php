<?php

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\EducationEntry;
use Illuminate\Database\Seeder;

class CredentialSeeder extends Seeder
{
    public function run(): void
    {
        $certifications = [
            ['name' => 'Open-Source Web Development (Laravel)', 'issuer' => 'Information Technology Institute (ITI)'],
            ['name' => 'Laravel Development Internship', 'issuer' => 'TryOrder'],
            ['name' => 'Complete Flutter and Dart Development', 'issuer' => 'Udemy'],
        ];

        foreach ($certifications as $index => $certification) {
            Certification::updateOrCreate(
                ['name' => $certification['name'], 'issuer' => $certification['issuer']],
                ['sort_order' => $index],
            );
        }

        EducationEntry::updateOrCreate(
            ['degree' => 'Bachelor of Computer Science', 'institution' => 'Assiut University'],
            [
                'location' => 'Assiut, Egypt',
                'started_on' => '2016-09-01',
                'ended_on' => '2022-06-30',
                'sort_order' => 0,
            ],
        );
    }
}
