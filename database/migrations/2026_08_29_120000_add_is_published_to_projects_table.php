<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * No visibility/published/archived mechanism existed on projects before
     * this (confirmed via Schema::getColumnListing during the curation
     * audit) — is_highlighted/featured_order only control homepage featuring,
     * not whether a project appears in listings at all. This is the minimal
     * generic field needed to unlist a project (e.g. a redundant UI-mockup
     * precursor) without deleting its record.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('is_published')->default(true)->after('featured_order');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('is_published');
        });
    }
};
