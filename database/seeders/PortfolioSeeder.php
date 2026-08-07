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
            'Graphic Design',
            'UI/UX Design',
            'IT & Development',
            'Fotografi',
            'Modeling'
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate([
                'name' => $category,
                'slug' => strtolower(str_replace([' & ', ' '], ['-', '-'], $category))
            ]);
        }

        // Mengambil ID Kategori untuk relasi data
        $graphicId = Category::where('name', 'Graphic Design')->first()->id;
        $uiUxId = Category::where('name', 'UI/UX Design')->first()->id;
        $itId = Category::where('name', 'IT & Development')->first()->id;
        $fgId = Category::where('name','Fotografi')->first()->id;
        $mdlId = Category::where('name','Modeling')->first()->id;

        $projects = [
            [
                'category_id' => $graphicId, 'title' => 'LuxSuits',
                'role' => 'Graphic Designer', 'client' => 'Personal / Commercial', 'year' => '2025', 'tools' => 'Photoshop, Illustrator',
                'is_highlighted' => true, 'featured_order' => 1,
                'description' => 'Visual promotional design for LuxSuits that communicates a premium and elegant brand image. Emphasizing a minimalist composition with strong contrast to highlight the value of formal wear products.',
                'image_path' => 'projects/Luxsuits/luxsuits.webp',
            ],
            [
                'category_id' => $graphicId,
                'title' => 'Manufer Super League',
                'role' => 'Graphic Designer', 'client' => 'Sports & Event', 'year' => '2025', 'tools' => 'Photoshop, Illustrator',
                'description' => 'Visual asset development for the Manufer Super League competitive event. Encompassing social media design to physical on-field elements, maintaining a bold color identity.',
                'image_path' => 'projects/MSL/manufer.webp',
            ],
            [
                'category_id' => $graphicId,
                'title' => 'Top Scorer Arena',
                'role' => 'Graphic Designer', 'client' => 'Sports & Event', 'year' => '2025', 'tools' => 'Photoshop, Illustrator',
                'description' => 'High-energy visual exploration for the Top Scorer Arena mini-soccer business. Maintaining a strong and dynamic sports identity consistently across social media platforms.',
                'image_path' => 'projects/TSA/topscorer.webp',
            ],
            [
                'category_id' => $graphicId,
                'title' => '523 Studio',
                'role' => 'Graphic Designer', 'client' => '523 Studio', 'year' => '2024', 'tools' => 'Photoshop, Illustrator',
                'description' => 'Adaptation of various visual styles for the clients of 523 Studio creative agency. Including recruitment poster designs and comprehensive social media campaigns.',
                'image_path' => 'projects/523/523studio.webp',
            ],
            [
                'category_id' => $graphicId,
                'title' => 'FTI UNAND',
                'role' => 'Graphic Designer', 'client' => 'Akademik', 'year' => '2025', 'tools' => 'Photoshop, Illustrator',
                'description' => 'Designing academic visual materials for the Faculty of Information Technology, Andalas University, by balancing dense information through clean and structured layouts.',
                'image_path' => 'projects/FTI/fti-unand.webp',
            ],

            [
                'category_id' => $uiUxId,
                'title' => 'SPMB Universitas Adzkia (UI/UX Design)',
                'role' => 'UI/UX Designer', 'client' => 'Akademik', 'year' => '2025', 'tools' => 'Figma',
                'description' => 'Designing the user experience for the new student admission system. Focusing on the ease of filling out complex registration forms, clear information hierarchy, and a clean interface.',
                'image_path' => 'projects/SPMB/spmb-adzkia.webp',
            ],
            [
                'category_id' => $itId,
                'title' => 'SPMB Universitas Adzkia (Web Development)',
                'role' => 'Web & App Developer', 'client' => 'Akademik', 'year' => '2025', 'tools' => 'Laravel, Tailwind CSS',
                'description' => 'Building the front-end of the student registration system using a responsive code architecture. Implementing pixel-perfect design integration with Tailwind CSS and optimizing load performance.',
                'image_path' => 'projects/SPMB/spmbweb.webp',
            ],
            [
                'category_id' => $uiUxId,
                'title' => 'Informatika 23 Landing Page (UI/UX Design)',
                'role' => 'UI/UX Designer', 'client' => 'Akademik', 'year' => '2023', 'tools' => 'Figma',
                'description' => 'Visual identity and layout design for the Informatics 2023 batch landing page. Exploring a modern-futuristic style with intuitive navigation to facilitate information access.',
                'image_path' => 'projects/IF/if23.webp',
            ],
            [
                'category_id' => $itId,
                'title' => 'Informatika 23 Landing Page (Web Dev)',
                'role' => 'Web & App Developer', 'client' => 'Akademik', 'year' => '2023', 'tools' => 'Laravel, Tailwind CSS',
                'description' => 'Landing page implementation using a modern framework. Focusing on smooth animations, image optimization, and reusable component structures for future scalability.',
                'image_path' => 'projects/IF/if23web.webp',
            ],

            [
                'category_id' => $uiUxId,
                'title' => 'Money Tracker App',
                'role' => 'UI/UX Designer', 'client' => 'Personal / Commercial', 'year' => '2026', 'tools' => 'Figma',
                'description' => 'Designing intuitive user flows and a modern visual interface to facilitate seamless personal financial tracking, data visualization, and daily record-keeping.',
                'image_path' => 'projects/moneytracker.webp',
            ],
            [
                'category_id' => $graphicId,
                'title' => 'Metro Software',
                'role' => 'Graphic Designer', 'client' => 'Personal / Commercial', 'year' => '2026', 'tools' => 'Photoshop, Illustrator',
                'description' => 'Designed visual assets and user interface concepts for an urban mobility platform, focusing on clarity, layout, and user-friendly visuals.',
                'image_path' => 'projects/Metro/metro.webp',
            ],
            [
                'category_id' => $graphicId,
                'title' => 'Alir Pictures (Intern)',
                'role' => 'Graphic Designer', 'client' => 'Alir Pictures', 'year' => '2024', 'tools' => 'Photoshop, Illustrator',
                'description' => 'Graphic Designer intern at a production house, responsible for designing visual content, branding elements, and promotional materials.',
                'image_path' => 'projects/Alir/alir.webp',
            ],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(
                ['title' => $project['title']], 
                $project
            );
        }
    }
}
