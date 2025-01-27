<?php

namespace Tests\Unit\Services;

use App\Models\Template;
use App\Models\TemplateField;
use App\Models\TemplateRecordValue;
use App\Services\TemplateRecordTransformer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TemplateRecordTransformerTest extends TestCase
{
    use DatabaseTransactions;

    private Template $template;
    private TemplateField $nameField;
    private TemplateField $ageField;

    protected function setUp(): void
    {
        parent::setUp();

        // Create template once
        $this->template = new Template();

        // Create fields
        $this->nameField = TemplateField::factory()
            ->state(['name' => 'name', 'type' => 'string'])
            ->create();

        $this->ageField = TemplateField::factory()
            ->state(['name' => 'age', 'type' => 'integer'])
            ->create();

        $this->template->fields = collect([$this->nameField, $this->ageField]);
    }

    public function test_it_transforms_record_values_into_structured_format()
    {
        // Create record values
        $values = collect([
            new TemplateRecordValue([
                'template_record_id' => 1,
                'template_field_id' => $this->nameField->id,
                'string_value' => 'John Doe'
            ]),
            new TemplateRecordValue([
                'template_record_id' => 1,
                'template_field_id' => $this->ageField->id,
                'integer_value' => '25'
            ])
        ]);

        $result = TemplateRecordTransformer::transformRecords($values, $this->template);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'John Doe',
                'age' => 25
            ]
        ], $result);
    }

    public function test_it_handles_multiple_records()
    {
        $values = collect([
            TemplateRecordValue::factory()->create([
                'template_record_id' => 1,
                'template_field_id' => $this->nameField->id,
                'string_value' => 'John'
            ]),
            TemplateRecordValue::factory()->create([
                'template_record_id' => 2,
                'template_field_id' => $this->nameField->id,
                'string_value' => 'Jane'
            ])
        ]);

        $result = TemplateRecordTransformer::transformRecords($values, $this->template);

        $this->assertCount(2, $result);
        $this->assertEquals('John', $result[0]['name']);
        $this->assertEquals('Jane', $result[1]['name']);
    }

    public function test_it_handles_missing_fields()
    {
        $values = collect([
            new TemplateRecordValue([
                'template_record_id' => 1,
                'template_field_id' => 999, // Non-existent field
                'string_value' => 'John'
            ])
        ]);

        $result = TemplateRecordTransformer::transformRecords($values, $this->template);

        $this->assertEquals([['id' => 1]], $result);
    }
}