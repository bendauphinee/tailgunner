<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TemplateField>
 */
class TemplateFieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'template_id' => \App\Models\Template::factory(),
            'label' => $this->faker->word,
            'name' => strtolower($this->faker->word),
            'type' => $this->faker->randomElement(['string', 'integer', 'text']),
            'order' => $this->faker->numberBetween(1, 10),
            'extended_options' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
