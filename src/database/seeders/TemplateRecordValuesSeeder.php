<?php

namespace Database\Seeders;

use App\Models\TemplateRecord;
use App\Models\TemplateRecordValue;
use Illuminate\Database\Seeder;

class TemplateRecordValuesSeeder extends Seeder
{
    public function run(): void
    {
        TemplateRecord::with(['template.fields'])->each(function ($record) {
            $record->template->fields->each(function ($field) use ($record) {
                TemplateRecordValue::factory()->create([
                    'template_id' => $record->template_id,
                    'template_field_id' => $field->id,
                    'template_record_id' => $record->id,
                ]);
            });
        });
    }
}
