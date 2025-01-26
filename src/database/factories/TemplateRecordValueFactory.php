<?php

namespace Database\Factories;

use App\Models\Template;
use App\Models\TemplateField;
use App\Models\TemplateRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TemplateRecordValue>
 */
class TemplateRecordValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'template_id' => Template::factory(),
            'template_field_id' => TemplateField::factory(),
            'template_record_id' => TemplateRecord::factory(),
            'integer_value' => $this->faker->numberBetween(1, 1000),
            'string_value' => $this->faker->word(),
            'text_value' => $this->faker->paragraph(),
        ];
    }
}
