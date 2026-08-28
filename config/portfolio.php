<?php

return [
    'contact_email' => env('PORTFOLIO_CONTACT_EMAIL', 'surdik2811@gmail.com'),
    'whatsapp_number' => env('PORTFOLIO_WHATSAPP_NUMBER', '6282288706114'),
    'whatsapp_display' => env('PORTFOLIO_WHATSAPP_DISPLAY', '+62 822 8870 6114'),

    // Single source of truth for the Admin Project form's tool checklist
    // (resources/views/admin/projects/_form.blade.php) and V1.2's AI Tool
    // Suggestions (ProjectAssistantController::suggestTools) — both read
    // this same list so a suggestion that matches an existing checkbox can
    // be confirmed by checking it, rather than two independently
    // maintained copies drifting apart.
    'tool_options' => [
        'Photoshop', 'Illustrator', 'Canva', 'Figma', 'After Effects',
        'Premiere Pro', 'CapCut', 'Laravel', 'Tailwind CSS', 'Flutter', 'Python',
    ],
];
