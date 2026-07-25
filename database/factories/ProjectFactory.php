<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'image_path' => 'projects/placeholder.webp',
            'gallery_images' => [],
            'role' => 'Designer',
            'client' => 'Personal / Commercial',
            'year' => (string) $this->faker->year(),
            'tools' => 'Figma',
        ];
    }
}
