<?php

// Skills remain code-managed (not database-backed) — see the Batch 8B
// capability matrix: this content changes rarely enough that a CMS layer
// isn't warranted. Each group renders as one full-width interactive
// capability band on the homepage (index.blade.php's Skills section).
// Tool `icon` values are mask-image sources (see .skill-tool-icon in
// app.css) — every icon renders as a flat, single-color shape regardless
// of the source SVG's own colors, so mixing devicon brand-color SVGs here
// is safe and never produces the illegible-icon regression documented in
// CURRENT_PORTFOLIO_AUDIT.md.

return [
    'groups' => [
        [
            'label' => 'Design',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2 2 7l10 5 10-5-10-5Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m2 17 10 5 10-5M2 12l10 5 10-5"/></svg>',
            'primary' => [
                'Visual identity and brand systems',
                'Layout, composition, and print-ready design',
            ],
            'secondary' => [
                'Typography-led design systems · Campaign and social collateral',
            ],
            'tools' => [
                ['name' => 'Photoshop', 'icon' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/photoshop/photoshop-plain.svg'],
                ['name' => 'Illustrator', 'icon' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/illustrator/illustrator-plain.svg'],
                ['name' => 'Figma', 'icon' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/figma/figma-original.svg'],
            ],
        ],
        [
            'label' => 'Development',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m8 9-4 3 4 3m8-6 4 3-4 3M13 5l-2 14"/></svg>',
            'primary' => [
                'Web application development',
                'Responsive interface implementation',
            ],
            'secondary' => [
                'Laravel and PHP backends · Component-driven Tailwind front-ends',
            ],
            'tools' => [
                ['name' => 'Laravel', 'icon' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/laravel/laravel-original.svg'],
                ['name' => 'PHP', 'icon' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/php/php-original.svg'],
                ['name' => 'JavaScript', 'icon' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/javascript/javascript-original.svg'],
                ['name' => 'MySQL', 'icon' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/mysql/mysql-original.svg'],
                ['name' => 'Tailwind CSS', 'icon' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/tailwindcss/tailwindcss-original.svg'],
            ],
        ],
        [
            'label' => 'Applied AI & Data',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="5" cy="6" r="2"/><circle cx="19" cy="6" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="5" cy="18" r="2"/><circle cx="19" cy="18" r="2"/><path stroke-linecap="round" d="m6.5 7 4 4m7-4-4 4m-7 3 4 4m7-4-4 4"/></svg>',
            'primary' => [
                'Machine learning fundamentals',
                'Data analysis and visualization',
            ],
            'secondary' => [
                'Computer vision experiments · Model training and evaluation',
            ],
            'tools' => [
                ['name' => 'Python', 'icon' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/python/python-original.svg'],
                ['name' => 'TensorFlow', 'icon' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/tensorflow/tensorflow-original.svg'],
                ['name' => 'OpenCV', 'icon' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/opencv/opencv-original.svg'],
                ['name' => 'scikit-learn', 'icon' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/scikitlearn/scikitlearn-original.svg'],
            ],
        ],
    ],
];
