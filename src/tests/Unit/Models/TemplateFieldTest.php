<?php

namespace Tests\Unit\Models;

use App\Models\Template;
use App\Models\TemplateField;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateFieldTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_template_field()
    {
        $template = Template::factory()->create();
        $field = TemplateField::factory()->create([
            'template_id' => $template->id,
            'name' => 'Test Field',
            'type' => 'text'
        ]);

        $this->assertDatabaseHas('template_fields', [
            'id' => $field->id,
            'template_id' => $template->id,
            'name' => 'Test Field',
            'type' => 'text'
        ]);
    }

    public function test_template_can_get_fields()
    {
        $template = Template::factory()->create();
        $field = TemplateField::factory()->create(['template_id' => $template->id]);

        $this->assertTrue($template->fields->contains($field));
    }
}
