<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Optional override for the Selected Works accordion cover.
            // Nullable: when empty, the accordion falls back to image_path
            // (see Project::coverImagePath()). Lets a project use a
            // different crop/composition for its accordion cover than for
            // its detail-page hero image, without touching image_path or
            // the detail gallery.
            $table->string('cover_image_path')->nullable()->after('image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('cover_image_path');
        });
    }
};
