<?php

namespace Tests\Unit\Models;

use App\Models\Template;
use App\Models\TemplateRecord;
use App\Models\TemplateRecordValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateRecordTest extends TestCase
{
    use RefreshDatabase;

    public function test_template_record_belongs_to_template()
    {
        $record = TemplateRecord::factory()->create();
        $this->assertInstanceOf(Template::class, $record->template);
    }

    public function test_template_record_has_many_values()
    {
        $record = TemplateRecord::factory()->create();

        TemplateRecordValue::factory()->count(3)->create([
            'template_record_id' => $record->id
        ]);

        $this->assertCount(3, $record->values);
        $this->assertInstanceOf(TemplateRecordValue::class, $record->values->first());
    }
}