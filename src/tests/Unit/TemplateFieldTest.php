<?php

namespace Tests\Unit;

use App\Models\Template;
use App\Models\TemplateField;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateFieldTest extends TestCase
{
    use RefreshDatabase;

    public function test_template_can_get_fields()
    {
        $template = Template::factory()->create();
        $field = TemplateField::factory()->create(['template_id' => $template->id]);

        $this->assertTrue($template->fields->contains($field));
    }
}