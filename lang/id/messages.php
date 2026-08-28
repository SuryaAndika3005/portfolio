<?php

// Bahasa Indonesia does not inflect nouns for plurality, so every count
// range renders the same phrase -- unlike English's messages.php, this is
// not a missing distinction, it is the correct one.
return [
    'works_count' => ':count Karya',
    'projects_count' => ':count Proyek',
];
