<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        // Sourced from Surya's CV (storage/app/public/projects/CV.pdf).
        $experiences = [
            ['category' => 'Professional Work', 'role' => 'Graphic Designer', 'company' => '523 Studio - Creative Agency', 'duration' => 'Jun 2025 - Present'],
            ['category' => 'Professional Work', 'role' => 'Graphic Designer Intern', 'company' => 'Alir Pictures - Production House', 'duration' => 'May - Jul 2024'],
            ['category' => 'Organization', 'role' => 'Coordinator of Media & Info', 'company' => 'LIMPAKO', 'duration' => '2024 - 2025'],
            ['category' => 'Organization', 'role' => 'Coordinator of InfoMed', 'company' => 'Himpunan Mahasiswa Informatika', 'duration' => '2024 - 2025'],
            ['category' => 'Events', 'role' => 'Pubdok Coordinator', 'company' => 'KKN III Koto Aur Malintang Selatan', 'duration' => 'Nov 2025 - Feb 2026'],
            ['category' => 'Events', 'role' => 'Chief Executive', 'company' => 'Bakti FTI', 'duration' => 'Apr - Aug 2025'],
            ['category' => 'Events', 'role' => 'Pubdok Coordinator', 'company' => 'APAN 8 - LIMPAKO', 'duration' => 'Dec 2024 - Feb 2025'],
            ['category' => 'Events', 'role' => 'Pubdok Coordinator', 'company' => 'LKMM-TD FTI', 'duration' => 'May - Sep 2024'],
            ['category' => 'Events', 'role' => 'Staff of Media & Info', 'company' => 'APAN 7 - LIMPAKO', 'duration' => 'Dec 2023 - Feb 2024'],
        ];

        foreach ($experiences as $experience) {
            Experience::updateOrCreate(
                ['role' => $experience['role'], 'company' => $experience['company']],
                $experience
            );
        }
    }
}
