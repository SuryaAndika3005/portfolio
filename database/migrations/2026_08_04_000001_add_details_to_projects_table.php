<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('role')->nullable()->after('title');
            $table->string('client')->nullable()->after('role');
            $table->string('year')->nullable()->after('client');
            $table->string('tools')->nullable()->after('year');

            // Extra screenshots shown in the "Visual Showcase" gallery on the
            // detail page. Resolved once (seed time or admin upload) instead
            // of being probed with file_exists() on every page render.
            $table->json('gallery_images')->nullable()->after('image_path');

            $table->boolean('is_highlighted')->default(false)->after('category_id');
            $table->unsignedInteger('featured_order')->nullable()->after('is_highlighted');

            // Optional case-study fields. Nullable — the detail page falls
            // back to the plain `description` when these are empty.
            $table->text('problem')->nullable()->after('description');
            $table->text('process')->nullable()->after('problem');
            $table->text('result')->nullable()->after('process');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'client', 'year', 'tools', 'gallery_images',
                'is_highlighted', 'featured_order', 'problem', 'process', 'result',
            ]);
        });
    }
};
