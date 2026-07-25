<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Experience;
use App\Models\Project;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Graphic Design' => 'graphic-design',
            'UI/UX Design' => 'ui-ux-design',
            'IT & Development' => 'it-development',
            'Fotografi' => 'fotografi',
            'Modeling' => 'modeling',
        ];

        foreach ($categories as $name => $slug) {
            Category::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }

        $catId = fn (string $name) => Category::where('name', $name)->value('id');

        $graphicId = $catId('Graphic Design');
        $uiUxId = $catId('UI/UX Design');
        $itId = $catId('IT & Development');

        // Each project now carries its own real metadata instead of having
        // Role/Client/Year/Tools guessed from its title at render time.
        $projects = [
            [
                'category_id' => $graphicId,
                'title' => 'LuxSuits',
                'description' => 'Visual promotional design for LuxSuits that communicates a premium and elegant brand image. Emphasizing a minimalist composition with strong contrast to highlight the value of formal wear products.',
                'image_path' => 'projects/Luxsuits/luxsuits.webp',
                'role' => 'Graphic Designer',
                'client' => 'Personal / Commercial',
                'year' => '2025',
                'tools' => 'Photoshop, Illustrator',
            ],
            [
                'category_id' => $graphicId,
                'title' => 'Manufer Super League',
                'description' => 'Visual asset development for the Manufer Super League competitive event. Encompassing social media design to physical on-field elements, maintaining a bold color identity.',
                'image_path' => 'projects/MSL/manufer.webp',
                'role' => 'Graphic Designer',
                'client' => 'Sports & Event',
                'year' => '2025',
                'tools' => 'Photoshop, Illustrator',
            ],
            [
                'category_id' => $graphicId,
                'title' => 'Top Scorer Arena',
                'description' => 'High-energy visual exploration for the Top Scorer Arena mini-soccer business. Maintaining a strong and dynamic sports identity consistently across social media platforms.',
                'image_path' => 'projects/TSA/topscorer.webp',
                'role' => 'Graphic Designer',
                'client' => 'Sports & Event',
                'year' => '2025',
                'tools' => 'Photoshop, Illustrator',
            ],
            [
                'category_id' => $graphicId,
                'title' => '523 Studio',
                'description' => 'Adaptation of various visual styles for the clients of 523 Studio creative agency. Including recruitment poster designs and comprehensive social media campaigns.',
                'image_path' => 'projects/523/523studio.webp',
                'role' => 'Graphic Designer',
                'client' => '523 Studio',
                'year' => '2024',
                'tools' => 'Photoshop, Illustrator',
            ],
            [
                'category_id' => $graphicId,
                'title' => 'FTI UNAND',
                'description' => 'Designing academic visual materials for the Faculty of Information Technology, Andalas University, by balancing dense information through clean and structured layouts.',
                'image_path' => 'projects/FTI/fti-unand.webp',
                'role' => 'Graphic Designer',
                'client' => 'Akademik',
                'year' => '2025',
                'tools' => 'Photoshop, Illustrator',
            ],
            [
                'category_id' => $uiUxId,
                'title' => 'SPMB Universitas Adzkia (UI/UX Design)',
                'description' => 'Designing the user experience for the new student admission system. Focusing on the ease of filling out complex registration forms, clear information hierarchy, and a clean interface.',
                'image_path' => 'projects/SPMB/spmb-adzkia.webp',
                'role' => 'UI/UX Designer',
                'client' => 'Akademik',
                'year' => '2025',
                'tools' => 'Figma',
            ],
            [
                'category_id' => $itId,
                'title' => 'SPMB Universitas Adzkia (Web Development)',
                'description' => 'Building the front-end of the student registration system using a responsive code architecture. Implementing pixel-perfect design integration with Tailwind CSS and optimizing load performance.',
                'image_path' => 'projects/SPMB/spmbweb.webp',
                'role' => 'Web & App Developer',
                'client' => 'Akademik',
                'year' => '2025',
                'tools' => 'Laravel, Tailwind CSS',
            ],
            [
                'category_id' => $uiUxId,
                'title' => 'Informatika 23 Landing Page (UI/UX Design)',
                'description' => 'Visual identity and layout design for the Informatics 2023 batch landing page. Exploring a modern-futuristic style with intuitive navigation to facilitate information access.',
                'image_path' => 'projects/IF/if23.webp',
                'role' => 'UI/UX Designer',
                'client' => 'Akademik',
                'year' => '2023',
                'tools' => 'Figma',
            ],
            [
                'category_id' => $itId,
                'title' => 'Informatika 23 Landing Page (Web Dev)',
                'description' => 'Landing page implementation using a modern framework. Focusing on smooth animations, image optimization, and reusable component structures for future scalability.',
                'image_path' => 'projects/IF/if23web.webp',
                'role' => 'Web & App Developer',
                'client' => 'Akademik',
                'year' => '2023',
                'tools' => 'Laravel, Tailwind CSS',
            ],
            [
                'category_id' => $uiUxId,
                'title' => 'Money Tracker App',
                'description' => 'Designing intuitive user flows and a modern visual interface to facilitate seamless personal financial tracking, data visualization, and daily record-keeping.',
                'image_path' => 'projects/moneytracker.webp',
                'role' => 'UI/UX Designer',
                'client' => 'Personal / Commercial',
                'year' => '2026',
                'tools' => 'Figma',
            ],
            [
                'category_id' => $graphicId,
                'title' => 'Metro Software',
                'description' => 'Designed visual assets and user interface concepts for an urban mobility platform, focusing on clarity, layout, and user-friendly visuals.',
                'image_path' => 'projects/Metro/metro.webp',
                'role' => 'Graphic Designer',
                'client' => 'Personal / Commercial',
                'year' => '2026',
                'tools' => 'Photoshop, Illustrator',
            ],
            [
                'category_id' => $graphicId,
                'title' => 'Alir Pictures (Intern)',
                'description' => 'Graphic Designer intern at a production house, responsible for designing visual content, branding elements, and promotional materials.',
                'image_path' => 'projects/Alir/alir.webp',
                'role' => 'Graphic Designer',
                'client' => 'Alir Pictures',
                'year' => '2024',
                'tools' => 'Photoshop, Illustrator',
            ],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(['title' => $project['title']], $project);
        }

        $experiences = [
            ['category' => 'Professional Work', 'role' => 'Graphic Designer', 'company' => '523 Studio - Creative Agency', 'duration' => 'Jun 2025 - Present'],
            ['category' => 'Professional Work', 'role' => 'Graphic Designer Intern', 'company' => 'Alir Pictures - Production House', 'duration' => 'May - Jul 2024'],
            ['category' => 'Organization', 'role' => 'Coordinator of Media & Info', 'company' => 'LIMPAKO', 'duration' => '2024 - 2025'],
            ['category' => 'Organization', 'role' => 'Coordinator of InfoMed', 'company' => 'Himpunan Mahasiswa Informatika', 'duration' => '2024 - 2025'],
            ['category' => 'Events', 'role' => 'Pubdok Coordinator', 'company' => 'KKN KAMS', 'duration' => '2025 - 2026'],
            ['category' => 'Events', 'role' => 'Chief Executive', 'company' => 'Bakti FTI', 'duration' => 'Apr - Aug 2025'],
            ['category' => 'Events', 'role' => 'Pubdok Coordinator', 'company' => 'APAN 8 - LIMPAKO', 'duration' => '2024 - 2025'],
        ];

        foreach ($experiences as $experience) {
            Experience::updateOrCreate(
                ['role' => $experience['role'], 'company' => $experience['company']],
                $experience
            );
        }
    }
}
