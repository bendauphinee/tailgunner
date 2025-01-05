<?php

namespace Tests\Unit;

use App\Models\Template;
use App\Models\TemplateField;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_template_and_fields_can_be_loaded()
    {
        $template = Template::factory()->create();
        $field = TemplateField::factory()->create(['template_id' => $template->id]);

        $loadedTemplate = Template::with('fields')->find($template->id);

        $this->assertTrue($loadedTemplate->fields->contains($field));
    }
}
