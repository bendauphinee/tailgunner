<?php

namespace Tests\Unit\Models;

use App\Models\Template;
use App\Models\TemplateField;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TemplateFieldTest extends TestCase
{
    use DatabaseTransactions;

    private Template $template;
    private bool $templateCreated = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (!$this->templateCreated) {
            $this->template = Template::factory()->create();
            $this->templateCreated = true;
        }
    }

    public function test_can_create_template_field()
    {
        $field = TemplateField::factory()->create([
            'template_id' => $this->template->id,
            'name' => 'Test Field',
            'type' => 'text'
        ]);

        $this->assertDatabaseHas('template_fields', [
            'id' => $field->id,
            'template_id' => $this->template->id,
            'name' => 'Test Field',
            'type' => 'text'
        ]);
    }

    public function test_template_can_get_fields()
    {
        $field = TemplateField::factory()->create(['template_id' => $this->template->id]);

        $this->assertTrue($this->template->fields->contains($field));
    }
}
