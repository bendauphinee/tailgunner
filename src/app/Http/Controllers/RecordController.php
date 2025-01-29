<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\TemplateRecordValue;
use App\Services\TemplateRecordTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RecordController extends Controller
{
    /**
     * Display template records list
     *
     * @param Request $request
     * @return \Inertia\Response
     * @throws ModelNotFoundException
     */
    public function index(Request $request): \Inertia\Response
    {
        // Get the template information
        $template = Template::withMetaAndFields()
            ->where('user_id', auth()->id())
            ->findOrFail($request->template);

        // Grab the records data for the template
        $values = TemplateRecordValue::query()
            ->select([
                'template_record_id',
                'template_field_id',
                'string_value',
                'text_value',
                'integer_value'
            ])
            ->join('template_records', 'template_record_values.template_record_id', '=', 'template_records.id')
            ->where('template_records.template_id', $template->id)
            ->get();

        // Transform the values into proper records
        $records = TemplateRecordTransformer::transformRecords($values, $template);

        // Render the Inertia template
        return Inertia::render('Records/List', [
            'template' => $template,
            'records' => $records
        ]);
    }

    public function create()
    {
        return;
        // This function will return the form to add a record to a template
    }

    public function store()
    {
        return;
        // This function will add a record to a template
    }

    public function update()
    {
        return;
        // This function will update a record in a template
    }

    public function destroy()
    {
        return;
        // This function will delete a record from a template
    }

    public function show()
    {
        return;
        // This function will show a record from a template
    }

    public function edit()
    {
        return;
        // This function will return the form to edit a record in a template
    }
}
