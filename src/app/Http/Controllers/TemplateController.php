<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;

class TemplateController extends Controller
{
    public function index()
    {
        /**
         * Get the template data, and modify it to include the fields that
         * we're going to fetch later, when we make the actual table to store
         * the data entered in a template.
         */
        $templates = Template::query()
            ->where('user_id', auth()->id())
            ->get()
            ->transform(function($template) {
                return [
                    'id' => $template->id,
                    'title' => $template->title,
                    'description' => $template->description,
                    'created_at' => $template->created_at,

                    // Fake these fields for now
                    'last_used' => $template->created_at->addDay(),
                    'records' => rand(20, 100),
                ];
            });

        // Send back the Inertia template and template data
        return Inertia::render('Templates/Index', [
            'templates' => $templates
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:120',
        ]);

        // Make us a new template
        $template = Template::create([
            'title' => $validated['title'],
            'user_id' => auth()->id(),
        ]);

        // Pass back the data for the new template, and a flash message
        return response()->json([
            'template' => [
                'id' => $template->id,
                'title' => $template->title,
                'description' => '',
                'created_at' => $template->created_at,
                'last_used' => $template->created_at,
                'records' => 0,
            ],
            'flash' => [
                'success' => [
                    'data' => [
                        'id' => $template->id,
                        'title' => $template->title,
                    ]
                ]
            ]
        ]);
    }

    public function show(Request $request)
    {
        // Get the template data, and include the fields
        $template = Template::select('id', 'title', 'description')
            ->with('fields:id,template_id,label,name,type,order,extended_options')
            ->find($request->template);

        // Add a fake count of records
        $template->records = rand(20, 100);

        // Send back the Inertia template and template data
        return Inertia::render('Templates/Show', [
            'template' => $template
        ]);
    }

    private function fieldsAreDifferent($existing, $new): bool
    {
        return $existing->label !== $new['label']
            || $existing->name !== $new['name']
            || $existing->type !== $new['type']
            || $existing->order !== $new['order']
            || json_encode($existing->extended_options) !== json_encode($new['extended_options']);
    }

    public function update(Request $request, Template $template)
    {
        // Make sure we own the template
        if ($template->user_id !== auth()->id()) {
            abort(403);
        }

        // Validate the form data
        $validated = $request->validate([
            'title' => 'required|string|max:120',
            'description' => 'nullable|string',
            'fields.*.id' => 'nullable|integer|exists:template_fields,id',
            'fields.*.label' => ['required', 'string', 'max:80', 'filled', 'regex:/\S/'],
            'fields.*.name' => ['required', 'string', 'max:80', 'filled', 'regex:/\S/'],
            'fields.*.type' => ['required', 'string', 'in:integer,string,text,dropdown'],
            'fields.*.extended_options' => 'nullable',
            'fields.*.extended_options.*' => 'nullable|string'
        ]);

        // TODO: Add validation for duplicate field names
        // TODO: Add space replacement for field names
        // TODO: Add validation return to the front end

        // Trim strings before saving
        $validated['title'] = trim($validated['title']);
        $validated['description'] = $validated['description'] ? trim($validated['description']) : null;

        // Do some cleanup on the template fields
        foreach ($validated['fields'] as $k => $field) {
            $validated['fields'][$k]['label'] = trim($field['label']);
            $validated['fields'][$k]['name'] = trim($field['name']);

            // Run through the options and trim them
            if (is_array($validated['fields'][$k]['extended_options'])) {
                $validated['fields'][$k]['extended_options'] = array_map(
                    'trim',
                    array_filter($validated['fields'][$k]['extended_options'], 'strlen')
                );
            }

            // If what we're left with is an empty array, set it to null
            if (empty($validated['fields'][$k]['extended_options'])) {
                $validated['fields'][$k]['extended_options'] = null;
            }
        }

        // Only update template if data has changed
        if ($template->title !== $validated['title'] || $template->description !== $validated['description']) {
            $template->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
            ]);
        }

        // Get existing fields with their current data
        $existingFields = $template->fields()->get();
        $updatedFieldIds = [];

        // Update or create fields
        foreach ($validated['fields'] as $index => $field) {
            $fieldData = [
                'label' => $field['label'],
                'name' => $field['name'],
                'type' => $field['type'],
                'order' => $index + 1,
                'extended_options' => is_array($field['extended_options'])
                    ? json_encode($field['extended_options'])
                    : $field['extended_options']
            ];

            $existingField = $existingFields->find($field['id'] ?? null);

            if ($existingField) {
                // Only update if the field data has changed
                if ($this->fieldsAreDifferent($existingField, $fieldData)) {
                    $existingField->update($fieldData);
                }

                $updatedFieldIds[] = $existingField->id;
            } else {
                // Create new field
                $newField = $template->fields()->create($fieldData);
                $updatedFieldIds[] = $newField->id;
            }
        }

        // Delete fields that weren't updated or created
        $template->fields()
            ->whereNotIn('id', $updatedFieldIds)
            ->delete();

        return redirect()->back()->with('flash', [
            'success' => 'Template updated successfully'
        ]);
    }
}
