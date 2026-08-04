<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Graphic Design',
            'UI/UX Design',
            'IT & Development',
            'Fotografi',
            'Modeling',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate([
                'name' => $category,
                'slug' => strtolower(str_replace([' & ', ' '], ['-', '-'], $category)),
            ]);
        }

        $catId = fn (string $name) => Category::where('name', $name)->value('id');

        $graphicId = $catId('Graphic Design');
        $uiUxId = $catId('UI/UX Design');
        $itId = $catId('IT & Development');

        $roleFor = fn ($catId) => match ($catId) {
            $graphicId => 'Graphic Designer',
            $uiUxId => 'UI/UX Designer',
            $itId => 'Web & App Developer',
            default => 'Creative Designer',
        };
        $toolsFor = fn ($catId) => match ($catId) {
            $graphicId => 'Photoshop, Illustrator',
            $uiUxId => 'Figma',
            $itId => 'Laravel, Tailwind CSS',
            default => null,
        };

        $projects = [
            ['category_id' => $graphicId, 'title' => 'LuxSuits', 'client' => 'Personal / Commercial', 'year' => '2025', 'highlighted' => true, 'order' => 1,
                'description' => 'Visual promotional design for LuxSuits that communicates a premium and elegant brand image. Emphasizing a minimalist composition with strong contrast to highlight the value of formal wear products.',
                'image_path' => 'projects/Luxsuits/luxsuits.webp'],
            ['category_id' => $graphicId, 'title' => 'Manufer Super League', 'client' => 'Sports & Event', 'year' => '2025',
                'description' => 'Visual asset development for the Manufer Super League competitive event. Encompassing social media design to physical on-field elements, maintaining a bold color identity.',
                'image_path' => 'projects/MSL/manufer.webp'],
            ['category_id' => $graphicId, 'title' => 'Top Scorer Arena', 'client' => 'Sports & Event', 'year' => '2025',
                'description' => 'High-energy visual exploration for the Top Scorer Arena mini-soccer business. Maintaining a strong and dynamic sports identity consistently across social media platforms.',
                'image_path' => 'projects/TSA/topscorer.webp'],
            ['category_id' => $graphicId, 'title' => '523 Studio', 'client' => '523 Studio', 'year' => '2024', 'highlighted' => true, 'order' => 4,
                'description' => 'Adaptation of various visual styles for the clients of 523 Studio creative agency. Including recruitment poster designs and comprehensive social media campaigns.',
                'image_path' => 'projects/523/523studio.webp'],
            ['category_id' => $graphicId, 'title' => 'FTI UNAND', 'client' => 'Akademik', 'year' => '2025',
                'description' => 'Designing academic visual materials for the Faculty of Information Technology, Andalas University, by balancing dense information through clean and structured layouts.',
                'image_path' => 'projects/FTI/fti-unand.webp'],

            ['category_id' => $uiUxId, 'title' => 'SPMB Universitas Adzkia (UI/UX Design)', 'client' => 'Akademik', 'year' => '2025', 'highlighted' => true, 'order' => 2,
                'description' => 'Designing the user experience for the new student admission system. Focusing on the ease of filling out complex registration forms, clear information hierarchy, and a clean interface.',
                'image_path' => 'projects/SPMB/spmb-adzkia.webp'],
            ['category_id' => $itId, 'title' => 'SPMB Universitas Adzkia (Web Development)', 'client' => 'Akademik', 'year' => '2025',
                'description' => 'Building the front-end of the student registration system using a responsive code architecture. Implementing pixel-perfect design integration with Tailwind CSS and optimizing load performance.',
                'image_path' => 'projects/SPMB/spmbweb.webp'],
            ['category_id' => $uiUxId, 'title' => 'Informatika 23 Landing Page (UI/UX Design)', 'client' => 'Akademik', 'year' => '2023',
                'description' => 'Visual identity and layout design for the Informatics 2023 batch landing page. Exploring a modern-futuristic style with intuitive navigation to facilitate information access.',
                'image_path' => 'projects/IF/if23.webp'],
            ['category_id' => $itId, 'title' => 'Informatika 23 Landing Page (Web Dev)', 'client' => 'Akademik', 'year' => '2023',
                'description' => 'Landing page implementation using a modern framework. Focusing on smooth animations, image optimization, and reusable component structures for future scalability.',
                'image_path' => 'projects/IF/if23web.webp'],

            ['category_id' => $uiUxId, 'title' => 'Money Tracker App', 'client' => 'Personal / Commercial', 'year' => '2026', 'highlighted' => true, 'order' => 3,
                'description' => 'Designing intuitive user flows and a modern visual interface to facilitate seamless personal financial tracking, data visualization, and daily record-keeping.',
                'image_path' => 'projects/moneytracker.webp'],
            ['category_id' => $graphicId, 'title' => 'Metro Software', 'client' => 'Personal / Commercial', 'year' => '2026',
                'description' => 'Designed visual assets and user interface concepts for an urban mobility platform, focusing on clarity, layout, and user-friendly visuals.',
                'image_path' => 'projects/Metro/metro.webp'],
            ['category_id' => $graphicId, 'title' => 'Alir Pictures (Intern)', 'client' => 'Alir Pictures', 'year' => '2024',
                'description' => 'Graphic Designer intern at a production house, responsible for designing visual content, branding elements, and promotional materials.',
                'image_path' => 'projects/Alir/alir.webp'],
        ];

        foreach ($projects as $index => $project) {
            $id = $index + 1; // matches the client-{id}-*.webp gallery filenames

            $project['role'] = $roleFor($project['category_id']);
            $project['tools'] = $toolsFor($project['category_id']);
            $project['is_highlighted'] = $project['highlighted'] ?? false;
            $project['featured_order'] = $project['order'] ?? null;
            $project['gallery_images'] = $this->scanGallery($id);
            unset($project['highlighted'], $project['order']);

            Project::updateOrCreate(['title' => $project['title']], $project);
        }
    }

    /**
     * Scan storage/app/public/projects for client-{id}-*.{webp,png,jpg}
     * files and return their public URLs, resolved once here instead of
     * with file_exists() checks on every page render.
     */
    private function scanGallery(int $id): array
    {
        $dir = storage_path('app/public/projects');

        if (! File::isDirectory($dir)) {
            return [];
        }

        $matches = collect(File::files($dir))
            ->filter(fn ($file) => preg_match('/^client-'.$id.'-\d+\.(webp|png|jpe?g)$/i', $file->getFilename()))
            ->sort(fn ($a, $b) => strnatcmp($a->getFilename(), $b->getFilename()))
            ->map(fn ($file) => 'storage/projects/'.$file->getFilename())
            ->values();

        return $matches->all();
    }
}
