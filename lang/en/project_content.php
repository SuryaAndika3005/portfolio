<?php

/**
 * Project content translations (English), keyed by project ID.
 *
 * Every project's DB record holds English content as its default/source
 * language, so localized() falling back to the raw column already IS the
 * correct English behavior everywhere -- this file is intentionally empty.
 *
 * It previously held a single exception for Project 22 (Dashboard Kreatif
 * 523 Studio), whose `description` column was itself written in
 * Indonesian. That was normalized away (Language Content Completion
 * pass, Section 5): the DB column now holds the English text this file
 * used to override with (verified as an accurate translation before the
 * swap, so no meaning changed), and the Indonesian version moved into
 * lang/id/project_content.php's own entry for 22, the same shape every
 * other project already uses there.
 *
 * Kept as an empty return (not deleted) so the pattern is ready if a
 * future project's DB content is ever entered in Indonesian by mistake
 * and needs the same kind of temporary English override again.
 */
return [];
