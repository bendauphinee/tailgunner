<?php

namespace Tests\Unit\Models;

use App\Models\Template;
use App\Models\TemplateField;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_scope_with_meta_and_fields_basic_query()
    {
        $template = Template::factory()->create([
            'title' => 'Test Template',
            'description' => 'Test Description'
        ]);

        TemplateField::factory()->create([
            'template_id' => $template->id,
            'label' => 'Test Field',
            'name' => 'test_field',
            'type' => 'text',
            'order' => 1,
            'extended_options' => json_encode(['required' => true])
        ]);

        $result = Template::withMetaAndFields()->find($template->id);

        $this->assertEquals('Test Template', $result->title);
        $this->assertEquals('Test Description', $result->description);
        $this->assertCount(1, $result->fields);

        $field = $result->fields->first();
        $this->assertEquals('Test Field', $field->label);
        $this->assertEquals('test_field', $field->name);
        $this->assertEquals('text', $field->type);
        $this->assertEquals(1, $field->order);
    }

    public function test_scope_with_meta_and_fields_ordering()
    {
        $template = Template::factory()->create();

        TemplateField::factory()->create([
            'template_id' => $template->id,
            'label' => 'Second Field',
            'order' => 2
        ]);

        TemplateField::factory()->create([
            'template_id' => $template->id,
            'label' => 'First Field',
            'order' => 1
        ]);

        $result = Template::withMetaAndFields()->first();

        $fields = $result->fields->toArray();
        $this->assertEquals('First Field', $fields[0]['label']);
        $this->assertEquals('Second Field', $fields[1]['label']);
    }

    public function test_scope_with_meta_and_fields_extended_fields()
    {
        $template = Template::factory()->create();
        $field = TemplateField::factory()->create([
            'template_id' => $template->id,
            'extended_options' => json_encode(['required' => true])
        ]);

        $result = Template::withMetaAndFields(true)->first();

        $this->assertArrayHasKey('extended_options', $result->fields->first()->toArray());
        $this->assertEquals(
            ['required' => true],
            json_decode($result->fields->first()->extended_options, true)
        );
    }
}
