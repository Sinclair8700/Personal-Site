<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Education>
 */
class EducationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'image' => $this->faker->imageUrl,
            'description' => $this->faker->sentence,
            'location' => $this->faker->city,
            'start_date' => $this->faker->date,
            'end_date' => $this->faker->date,
            'link' => $this->faker->url,
        ];
    }
}
