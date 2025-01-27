<?php

namespace Tests\Unit\Models;

use App\Models\Template;
use App\Models\TemplateField;
use App\Models\TemplateRecord;
use App\Models\TemplateRecordValue;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TemplateRecordValueTest extends TestCase
{
    use DatabaseTransactions;

    public function test_record_value_relationships()
    {
        $value = TemplateRecordValue::factory()->create();

        $this->assertInstanceOf(Template::class, $value->template);
        $this->assertInstanceOf(TemplateField::class, $value->field);
        $this->assertInstanceOf(TemplateRecord::class, $value->record);
    }

    public function test_get_typed_value()
    {
        $value = TemplateRecordValue::factory()->create([
            'integer_value' => 42,
            'string_value' => 'test',
            'text_value' => 'long text'
        ]);

        $this->assertEquals(42, $value->getTypedValue('integer'));
        $this->assertEquals('test', $value->getTypedValue('string'));
        $this->assertEquals('long text', $value->getTypedValue('text'));
    }

    public function test_set_typed_value()
    {
        $value = new TemplateRecordValue();

        $value->setTypedValue('integer', 42);
        $this->assertEquals(42, $value->integer_value);

        $value->setTypedValue('string', 'test');
        $this->assertEquals('test', $value->string_value);

        $value->setTypedValue('text', 'long text');
        $this->assertEquals('long text', $value->text_value);
    }
}