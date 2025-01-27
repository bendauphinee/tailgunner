<?php

namespace Tests\Unit\Models;

use App\Models\Template;
use App\Models\TemplateRecord;
use App\Models\TemplateRecordValue;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TemplateRecordTest extends TestCase
{
    use DatabaseTransactions;

    private Template $template;

    protected function setUp(): void
    {
        parent::setUp();
        $this->template = Template::factory()->create();
    }

    public function test_template_record_belongs_to_template()
    {
        $record = TemplateRecord::factory()
            ->state(['template_id' => $this->template->id])
            ->create();

        $this->assertInstanceOf(Template::class, $record->template);
    }

    public function test_template_record_has_many_values()
    {
        $record = TemplateRecord::factory()
            ->state(['template_id' => $this->template->id])
            ->create();

        // Create values without unnecessary relationships
        TemplateRecordValue::factory()
            ->count(3)
            ->state([
                'template_record_id' => $record->id,
                'template_id' => $this->template->id
            ])
            ->create();

        $this->assertCount(3, $record->values);
        $this->assertInstanceOf(TemplateRecordValue::class, $record->values->first());
    }
}