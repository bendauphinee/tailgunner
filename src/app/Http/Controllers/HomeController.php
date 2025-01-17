<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => implode('.', array_slice(explode('.', \Illuminate\Foundation\Application::VERSION), 0, 1)),
            'phpVersion' => implode('.', array_slice(explode('.', PHP_VERSION), 0, 2)),

            'blogPosts' => [
                [
                    'title' => 'Part 1 – What To Build',
                    'url' => 'https://bendauphinee.com/writing/2024/12/20/designing-an-app-from-scratch-part-1-what-to-build',
                    'date' => 'Dec 20, 2024',
                ],
                [
                    'title' => 'Part 2 – Wireframing Requirements',
                    'url' => 'https://bendauphinee.com/writing/2024/12/24/designing-an-app-from-scratch-part-2-wireframing-requirements',
                    'date' => 'Dec 24, 2024',
                ],
                [
                    'title' => 'Part 3 – Workflows',
                    'url' => 'https://bendauphinee.com/writing/2024/12/27/designing-an-app-from-scratch-part-3-workflows',
                    'date' => 'Dec 27, 2024',
                ],
                [
                    'title' => 'Part 4 – System Design',
                    'url' => 'https://bendauphinee.com/writing/2024/12/30/designing-an-app-from-scratch-part-4-system-design',
                    'date' => 'Dec 30, 2024',
                ],
                [
                    'title' => 'Part 5 – Development Planning',
                    'url' => 'https://bendauphinee.com/writing/2025/01/02/an-app-from-scratch-part-5-development-planning',
                    'date' => 'Jan 2, 2025',
                ],
                [
                    'title' => 'Part 6 – Creating DB Tables (US1-C1)',
                    'url' => 'https://bendauphinee.com/writing/2025/01/07/an-app-from-scratch-part-6-creating-db-tables-us1-c1',
                    'date' => 'Jan 7, 2025',
                ],
                [
                    'title' => 'Part 7 – Creating The Template List Page (US1-C2)',
                    'url' => 'https://bendauphinee.com/writing/2025/01/09/an-app-from-scratch-part-7-creating-the-template-list-page-us1-c2',
                    'date' => 'Jan 9, 2025',
                ],
                [
                    'title' => 'Part 8 – Creating New Templates Tool (US1-C3)',
                    'url' => 'https://bendauphinee.com/writing/2025/01/14/an-app-from-scratch-part-8-creating-new-templates-tool-us1-c3',
                    'date' => 'Jan 14, 2025',
                ],
            ],
        ]);
    }
}
