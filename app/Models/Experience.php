<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Experience extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * There is no real date column on this table -- `duration` is a
     * free-text string ("Jun 2025 - Present", "May - Jul 2024",
     * "2024 - 2025") and every seeded row shares an identical created_at,
     * so neither can be trusted for chronological ordering as-is. These
     * two methods do a safe, centralized parse of the *existing* duration
     * string into comparable Carbon values -- used by
     * Admin\ExperienceController::index() and the public homepage's
     * Professional Work list. A genuinely reliable fix would still be a
     * real start_date/end_date column; that's a schema change and is
     * intentionally deferred, not applied silently here.
     */
    public function chronologyEnd(): Carbon
    {
        $end = trim((string) Str::of($this->duration)->explode(' - ')->last());

        if (str_contains($end, 'Present')) {
            return Carbon::create(9999, 12, 31, 23, 59, 59);
        }

        if (preg_match('/^\d{4}$/', $end)) {
            return Carbon::createFromDate((int) $end, 12, 31)->endOfDay();
        }

        try {
            return Carbon::createFromFormat('M Y', $end)->endOfMonth();
        } catch (\Exception) {
            return Carbon::createFromTimestamp(0);
        }
    }

    public function chronologyStart(): Carbon
    {
        $parts = array_map('trim', explode(' - ', (string) $this->duration));
        $start = $parts[0] ?? '';

        if (preg_match('/^\d{4}$/', $start)) {
            return Carbon::createFromDate((int) $start, 1, 1)->startOfDay();
        }

        if (preg_match('/^[A-Za-z]{3,9}$/', $start)) {
            // Month-only start ("May - Jul 2024"): the year lives on the
            // end token instead, so borrow it from there.
            $endToken = trim((string) end($parts));
            preg_match('/\d{4}/', $endToken, $yearMatch);
            $year = $yearMatch[0] ?? now()->year;

            try {
                return Carbon::createFromFormat('M Y', $start.' '.$year)->startOfMonth();
            } catch (\Exception) {
                return Carbon::createFromTimestamp(0);
            }
        }

        try {
            return Carbon::createFromFormat('M Y', $start)->startOfMonth();
        } catch (\Exception) {
            return Carbon::createFromTimestamp(0);
        }
    }

    /**
     * Most-recent/current-first ordering, shared by Admin's index and the
     * public homepage's Professional Work list so both use the exact same
     * rule. Ties on end date fall back to start date, then id -- both
     * deterministic, neither arbitrary.
     */
    public static function sortChronologically(\Illuminate\Support\Collection $items): \Illuminate\Support\Collection
    {
        return $items->sort(function (self $a, self $b) {
            return $b->chronologyEnd()->timestamp <=> $a->chronologyEnd()->timestamp
                ?: $b->chronologyStart()->timestamp <=> $a->chronologyStart()->timestamp
                ?: $b->id <=> $a->id;
        })->values();
    }
}
