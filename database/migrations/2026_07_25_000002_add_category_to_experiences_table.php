<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            // Groups entries into the site's existing "Professional Work",
            // "Organization", "Events" columns — previously hardcoded per-card
            // in the Blade file with no underlying data to back it.
            $table->string('category')->default('Professional Work')->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
