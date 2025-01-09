<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
}
