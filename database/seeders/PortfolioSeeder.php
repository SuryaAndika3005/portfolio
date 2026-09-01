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
        // Archive Taxonomy Restructure: name => slug pinned explicitly for
        // every category (not derived via Str::slug($name)) after
        // discovering that deriving it dynamically here would be a real
        // landmine for 'Web & Systems' specifically -- Str::slug('Web &
        // Systems') produces 'web-systems', but the live category (matched
        // by name below) intentionally KEPT its original 'it-development'
        // slug when it was renamed from "IT & Development" (see
        // CategoryController's docblock for why the slug must never
        // change). Re-running this seeder with a name-derived slug would
        // silently rewrite that slug and break every slug-keyed Blade
        // view/test. 'AI & Data' is a genuinely new category with no
        // legacy slug to preserve, so its pinned slug ('ai-data') simply
        // matches what Str::slug would have produced anyway.
        $categories = [
            'Graphic Design' => 'graphic-design',
            'UI/UX Design' => 'uiux-design',
            'Web & Systems' => 'it-development',
            'AI & Data' => 'ai-data',
            'Fotografi' => 'fotografi',
            'Modeling' => 'modeling',
        ];

        foreach ($categories as $name => $slug) {
            // Matched by name (a stable key), same as before -- but the
            // slug is now always the pinned value above, never derived, so
            // a re-run can rename IT & Development -> Web & Systems (an
            // old DB from before this pass) without ever touching its
            // slug.
            Category::updateOrCreate(
                ['name' => $name],
                ['slug' => $slug]
            );
        }

        // Mengambil ID Kategori untuk relasi data
        $graphicId = Category::where('name', 'Graphic Design')->first()->id;
        $uiUxId = Category::where('name', 'UI/UX Design')->first()->id;
        // Still the same 'it-development'-slugged category as before the
        // split -- only its display name changed. None of this seeder's
        // own baseline projects belong in the new 'AI & Data' category
        // (that category exists here only so a fresh install has the
        // correct 4-category taxonomy from the start); the real AI/data
        // projects (Vision AI Attendance, WebGIS, Speech Emotion
        // Recognition) were all added later via the Admin CMS, outside
        // this seeder, and reassigned to AI & Data directly in the
        // database -- see the Archive Taxonomy Restructure report.
        $itId = Category::where('name', 'Web & Systems')->first()->id;
        $fgId = Category::where('name', 'Fotografi')->first()->id;
        $mdlId = Category::where('name', 'Modeling')->first()->id;

        $projects = [
            [
                'category_id' => $graphicId,
                'title' => 'LuxSuits',
                'role' => 'Graphic Designer',
                'client' => 'Personal / Commercial',
                'year' => '2025',
                'tools' => 'Photoshop, Illustrator',
                'is_highlighted' => true,
                'featured_order' => 1,
                'description' => 'Visual promotional design for LuxSuits, a formal-wear rental brand, that communicates a premium and elegant image. Emphasizing a minimalist composition with strong contrast to highlight the value of formal wear products.',
                'problem' => 'LuxSuits needed promotional visuals that could hold their own against established suit rental brands online, but had no consistent visual language tying its social posts together.',
                'process' => 'Built a minimalist visual system around confident typography, muted tones, and strong product photography, then applied it across a series of posters exploring different angles on the "formal wear" theme.',
                'result' => 'A cohesive set of visuals that gave LuxSuits a more premium, recognizable presence on social media, and a reusable template for future campaigns.',
                'image_path' => 'projects/Luxsuits/luxsuits.webp',
            ],
            [
                'category_id' => $graphicId,
                'title' => 'Manufer Super League',
                'role' => 'Graphic Designer',
                'client' => 'Sports & Event',
                'year' => '2025',
                'tools' => 'Photoshop, Illustrator',
                'description' => 'Visual asset development for the Manufer Super League competitive event. Encompassing social media design to physical on-field elements, maintaining a bold color identity.',
                'problem' => 'The tournament needed a visual identity strong enough to carry both online promotion and physical on-field signage, on a tight event production timeline.',
                'process' => 'Established a bold, high-contrast color system and a flexible template set covering match announcements through to on-site banners, so new assets could be produced quickly as the tournament progressed.',
                'result' => 'A consistent, high-energy identity that carried the event from its earliest social teasers through to matchday signage.',
                'image_path' => 'projects/MSL/manufer.webp',
            ],
            [
                'category_id' => $graphicId,
                'title' => 'Top Scorer Arena',
                'role' => 'Graphic Designer',
                'client' => 'Sports & Event',
                'year' => '2025',
                'tools' => 'Photoshop, Illustrator',
                'description' => 'High-energy visual exploration for the Top Scorer Arena mini-soccer business. Maintaining a strong and dynamic sports identity consistently across social media platforms.',
                'problem' => 'As a newer mini-soccer venue, Top Scorer Arena needed a visual identity that felt as competitive and dynamic as the sport itself, to build an audience on social media from scratch.',
                'process' => 'Designed a bold, sports-driven visual language and applied it consistently across a recurring series of social posts to build recognizability over time.',
                'result' => 'A distinct, energetic presence that set the venue apart from more generic local sports-venue branding.',
                'image_path' => 'projects/TSA/topscorer.webp',
            ],
            [
                'category_id' => $graphicId,
                'title' => '523 Studio',
                'role' => 'Graphic Designer',
                'client' => '523 Studio',
                'year' => '2024',
                'tools' => 'Photoshop, Illustrator',
                'description' => 'Adaptation of various visual styles for the clients of 523 Studio creative agency. Including recruitment poster designs and comprehensive social media campaigns.',
                'problem' => 'Working in-house at a creative agency meant adapting quickly to different client briefs, brand voices, and formats, from recruitment drives to social campaigns, without a slow ramp-up per project.',
                'process' => 'Translated each incoming brief into layouts that stayed true to the client\'s brand while meeting the agency\'s turnaround expectations, across recruitment posters and social campaign sets.',
                'result' => 'A working slice of agency output showing range across recruitment, campaign, and social formats for multiple clients.',
                'image_path' => 'projects/523/523studio.webp',
            ],
            [
                'category_id' => $graphicId,
                'title' => 'FTI UNAND',
                'role' => 'Graphic Designer',
                'client' => 'Akademik',
                'year' => '2025',
                'tools' => 'Photoshop, Illustrator',
                'description' => 'Designing academic visual materials for the Faculty of Information Technology, Andalas University, by balancing dense information through clean and structured layouts.',
                'problem' => 'Faculty materials needed to communicate dense academic information (programs, requirements, schedules) without feeling cluttered or unapproachable to prospective students.',
                'process' => 'Applied a clean, structured layout system that organized the information hierarchy clearly while keeping materials visually consistent with the university\'s identity.',
                'result' => 'Materials that made dense academic content easier to scan at a glance, used across the faculty\'s promotional channels.',
                'image_path' => 'projects/FTI/fti-unand.webp',
            ],

            [
                'category_id' => $uiUxId,
                'title' => 'UI SPMB Universitas Adzkia',
                'role' => 'UI/UX Designer',
                'client' => 'Akademik',
                'year' => '2025',
                'tools' => 'Figma',
                'description' => 'Designing the user experience for the new student admission system. Focusing on the ease of filling out complex registration forms, clear information hierarchy, and a clean interface.',
                'problem' => 'The university\'s admission process relied on a confusing, form-heavy registration flow that discouraged prospective students from completing sign-up.',
                'process' => 'Mapped the full registration journey, broke the form into clear manageable steps, and designed a clean interface with strong information hierarchy to reduce the cognitive load at each step.',
                'result' => 'A development-ready UI design built specifically to make a traditionally frustrating registration process feel straightforward.',
                'image_path' => 'projects/SPMB/spmb-adzkia.webp',
            ],
            [
                'category_id' => $itId,
                'title' => 'Web SPMB Universitas Adzkia',
                'role' => 'Web & App Developer',
                'client' => 'Akademik',
                'year' => '2025',
                'tools' => 'Laravel, Tailwind CSS',
                'description' => 'Building the front-end of the student registration system using a responsive code architecture. Implementing pixel-perfect design integration with Tailwind CSS and optimizing load performance.',
                'problem' => 'The approved UI/UX design needed to become a real, responsive front-end without losing fidelity to the original design or sacrificing load performance.',
                'process' => 'Implemented the front-end with Tailwind CSS for pixel-accurate integration, optimizing images and layout for fast load times across devices.',
                'result' => 'A responsive, production-ready front-end that carried the original design intent through to a working registration system.',
                'image_path' => 'projects/SPMB/spmbweb.webp',
            ],
            [
                'category_id' => $uiUxId,
                'title' => 'UI Informatika 23 Landing Page',
                'role' => 'UI/UX Designer',
                'client' => 'Akademik',
                'year' => '2023',
                'tools' => 'Figma',
                'description' => 'Visual identity and layout design for the Informatics 2023 batch landing page. Exploring a modern-futuristic style with intuitive navigation to facilitate information access.',
                'problem' => 'The Informatics 2023 batch needed a shared digital presence that felt modern and cohesive, rather than another generic university template page.',
                'process' => 'Explored a modern, futuristic visual direction and designed an intuitive navigation structure so visitors could find batch information quickly.',
                'result' => 'A development-ready UI design that gave the batch a landing page that felt distinctly its own.',
                'image_path' => 'projects/IF/if23.webp',
            ],
            [
                'category_id' => $itId,
                'title' => 'Web Informatika 23 Landing Page',
                'role' => 'Web & App Developer',
                'client' => 'Akademik',
                'year' => '2023',
                'tools' => 'Laravel, Tailwind CSS',
                'description' => 'Landing page implementation using a modern framework. Focusing on smooth animations, image optimization, and reusable component structures for future scalability.',
                'problem' => 'Turning the approved design into a real site meant balancing visual polish, like animation and imagery, against performance and long-term maintainability.',
                'process' => 'Built the landing page with a component-based structure, focusing on smooth animations and optimized images so the page stayed fast without cutting the visual polish.',
                'result' => 'A working, animated landing page structured to be extended as the batch\'s needs changed over time.',
                'image_path' => 'projects/IF/if23web.webp',
            ],

            [
                'category_id' => $uiUxId,
                'title' => 'Money Tracker App',
                'role' => 'UI/UX Designer',
                'client' => 'Personal / Commercial',
                'year' => '2026',
                'tools' => 'Figma',
                'description' => 'Designing intuitive user flows and a modern visual interface to facilitate seamless personal financial tracking, data visualization, and daily record-keeping.',
                'problem' => 'Personal finance apps often overwhelm users with data; the goal here was a tracker that made daily recording and reviewing spending feel effortless rather than like a chore.',
                'process' => 'Designed the core flows around the two things people actually do most, logging a transaction and checking where their money went, backed by clear data visualization for the bigger picture.',
                'result' => 'A UI design exploring how personal finance tracking could feel simple enough to actually stick with day to day.',
                'image_path' => 'projects/moneytracker.webp',
            ],
            [
                'category_id' => $graphicId,
                'title' => 'Metro Software',
                'role' => 'Graphic Designer',
                'client' => 'Personal / Commercial',
                'year' => '2026',
                'tools' => 'Photoshop, Illustrator',
                'description' => 'Designed visual assets and user interface concepts for an urban mobility platform, focusing on clarity, layout, and user-friendly visuals.',
                'problem' => 'An urban mobility concept needed visual assets and interface concepts that felt trustworthy and easy to read at a glance, across both marketing and product touchpoints.',
                'process' => 'Designed a clear, approachable visual language paired with interface concepts, prioritizing legibility and clarity over decoration.',
                'result' => 'A cohesive concept set spanning marketing visuals and interface direction for the platform.',
                'image_path' => 'projects/Metro/metro.webp',
            ],
            [
                'category_id' => $graphicId,
                'title' => 'Alir Pictures (Intern)',
                'role' => 'Graphic Designer',
                'client' => 'Alir Pictures',
                'year' => '2024',
                'tools' => 'Photoshop, Illustrator',
                'description' => 'Graphic Designer intern at a production house, responsible for designing visual content, branding elements, and promotional materials.',
                'problem' => 'As an intern at a production house, needed to produce visual content and branding materials for real client projects under real studio deadlines.',
                'process' => 'Worked across promotional materials and branding elements for the studio\'s client work, adapting to each project\'s specific creative direction.',
                'result' => 'A body of internship work that built real production-house experience turning creative briefs into finished visual assets.',
                'image_path' => 'projects/Alir/alir.webp',
            ],
            [
                // Added via the admin panel first, so its image_path/gallery_images
                // already use the admin upload's randomized filenames rather than
                // this seeder's usual folder layout — see the 'gallery_images' note
                // on the loop below for why that matters here.
                'category_id' => $graphicId,
                'title' => 'PT Guna Griya Abadi',
                'role' => 'Graphic Designer',
                'client' => 'Personal / Commercial',
                'year' => '2026',
                'tools' => 'Photoshop, Illustrator',
                'description' => 'Visual identity and promotional materials for PT Guna Griya Abadi, giving the company a more professional and consistent visual presence across its materials.',
                'problem' => 'PT Guna Griya Abadi needed a professional visual presence but had no consistent identity tying its promotional materials together.',
                'process' => 'Developed a clean visual system and applied it across the company\'s core promotional materials, keeping the look consistent and easy to reuse.',
                'result' => 'A cohesive set of visuals that gave the company a more professional, recognizable presence.',
                'image_path' => 'projects/ptbAxnsE4IgL1aNxAVmbWBoSp67WjGyRqnSqjmRn.png',
                'gallery_images' => [
                    'storage/projects/qUJRk6vZzuPEzUaJyhsmkUrNRUeLqVCUq7XCKbdH.png',
                    'storage/projects/anGbl8Sg6cr8cRAkJH2R1sszAkpfxL2bHsEE6MwN.png',
                    'storage/projects/41mrnrQYm8mxSPJx30MOPMvCvf0THjYMteRbTPNz.png',
                    'storage/projects/r7cSOabbN3r9OPjyHFW1Nvi3sfgO8DYKz1jjVj5u.png',
                ],
            ],
            [
                'category_id' => $graphicId,
                'title' => 'Yasmin International Boarding School',
                'role' => 'Graphic Designer',
                'client' => 'Akademik',
                'year' => '2026',
                'tools' => 'Photoshop, Illustrator',
                'description' => 'Visual identity and promotional materials for Yasmin International Boarding School, communicating a trustworthy and modern image to prospective students and parents.',
                'problem' => 'As an international boarding school, Yasmin needed promotional materials that felt trustworthy and modern enough to stand out to prospective students and parents.',
                'process' => 'Designed a clean, professional visual system for the school\'s promotional materials, balancing an international feel with approachability.',
                'result' => 'A cohesive set of visuals ready to support the school\'s outreach and admissions materials.',
                'image_path' => 'projects/G4CvBqC9ZfAKG1vY7sKSmTStFUPxdH5HKjojLkk8.png',
                'gallery_images' => [
                    'storage/projects/Dufx6npxrCrVAyJfLDC1HF4uQ0X2Z18G6B2h980y.png',
                    'storage/projects/NjhVZG44vmxJnF17PnkgDKVyKFXaOx7uMRm9AOUn.png',
                    'storage/projects/HIzR6UI4qOdqcasaiBIBw6slLqzESdBdfGuJbY1x.png',
                    'storage/projects/ISMVHN9YSGmzJ1QYzQT0R5yfyD3hEJWzRrGdWrH9.png',
                ],
            ],
        ];

        foreach ($projects as $project) {
            // Pull gallery_images out before updateOrCreate: it's a JSON-cast
            // column, but we handle it separately below (explicit value here,
            // or scanGallery() for everything else) rather than trusting mass
            // assignment with it, so the two paths stay obviously distinct.
            $galleryImages = $project['gallery_images'] ?? null;
            unset($project['gallery_images']);

            $model = Project::updateOrCreate(
                ['title' => $project['title']],
                $project
            );

            // scanGallery() only finds files matching its own client-{id}-N.webp
            // convention. Projects added via the admin panel keep whatever
            // randomized filenames Laravel's upload handler gave them, so
            // scanning would find nothing and wipe out their real gallery —
            // use the explicit list above for those instead.
            $model->update(['gallery_images' => $galleryImages ?? $this->scanGallery($model->id)]);
        }
    }

    /**
     * Extra showcase images for a project, resolved once at seed time from
     * files already sitting in storage/app/public/projects (client-{id}-N.webp)
     * instead of being probed with file_exists() on every page render.
     */
    private function scanGallery(int $projectId): array
    {
        $files = glob(storage_path("app/public/projects/client-{$projectId}-*.webp")) ?: [];
        natsort($files);

        return collect($files)
            ->map(fn(string $path) => 'storage/projects/' . basename($path))
            ->values()
            ->all();
    }
}
