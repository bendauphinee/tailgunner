<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Models\TemplateRecord;
use Illuminate\Database\Seeder;

class TemplateRecordsSeeder extends Seeder
{
    public function run(): void
    {
        Template::all()->each(function ($template) {
            TemplateRecord::factory()
                ->count(5)
                ->create(['template_id' => $template->id]);
        });
    }
}
