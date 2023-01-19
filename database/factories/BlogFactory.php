<?php

namespace Database\Factories;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Blog>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image' => fake()->imageUrl(360, 360, 'animals', true, 'dogs', true),
            'title' => fake()->words('3', 'true'),
            'content' => fake()->realText,
            'status' => 'published',
            'publish_date' => fake()->dateTime,
        ];
    }
}
