<?php

namespace Tests\Unit\Models;

use App\Models\Template;
use App\Models\TemplateField;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TemplateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_create_template()
    {
        $templateData = [
            'title' => 'Test Template',
            'description' => 'Test Description',
            'user_id' => 1
        ];

        $template = Template::create($templateData);

        $this->assertDatabaseHas('templates', $templateData);
        $this->assertEquals($templateData['title'], $template->title);
        $this->assertEquals($templateData['description'], $template->description);
        $this->assertEquals($templateData['user_id'], $template->user_id);
    }

    public function test_template_and_fields_can_be_loaded()
    {
        $template = Template::factory()->create();
        $field = TemplateField::factory()->create(['template_id' => $template->id]);

        $loadedTemplate = Template::with('fields')->find($template->id);

        $this->assertTrue($loadedTemplate->fields->contains($field));
    }

    public function test_scope_with_meta_and_fields_basic()
    {
        $template = Template::factory()->create();
        $field = TemplateField::factory()->create(['template_id' => $template->id]);

        $loadedTemplate = Template::withMetaAndFields()->find($template->id);

        $this->assertNotNull($loadedTemplate);
        $this->assertTrue($loadedTemplate->fields->contains($field));

        $loadedTemplateArray = $loadedTemplate->toArray();
        $this->assertArrayHasKeys(['id', 'title', 'description'], $loadedTemplateArray);

        $loadedFieldArray = $loadedTemplate->fields->first()->toArray();
        $this->assertArrayHasKeys([
            'id',
            'template_id',
            'label',
            'name',
            'type',
            'order',
        ], $loadedFieldArray);
    }

    public function test_scope_with_meta_and_fields_extended()
    {
        $template = Template::factory()->create();
        $field = TemplateField::factory()->create(['template_id' => $template->id]);

        $loadedTemplate = Template::withMetaAndFields(true)->find($template->id);

        $this->assertNotNull($loadedTemplate);
        $loadedField = $loadedTemplate->fields->first();
        $this->assertArrayHasKey('extended_options', $loadedField->toArray());
    }

    public function test_scope_with_meta_and_fields_all()
    {
        $template = Template::factory()->create();
        $field = TemplateField::factory()->create(['template_id' => $template->id]);

        $loadedTemplate = Template::withMetaAndFields(true, true)->find($template->id);

        $this->assertNotNull($loadedTemplate);

        $loadedField = $loadedTemplate->fields->first();
        $loadedFieldArray = $loadedTemplate->fields->first()->toArray();

        $this->assertArrayHasKeys(['extended_options', 'created_at', 'updated_at'], $loadedFieldArray);
    }
}
