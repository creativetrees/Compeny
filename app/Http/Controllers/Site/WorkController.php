<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Project;

class WorkController extends Controller
{
    public function index()
    {
        return view('site.work.index', [
            'projects' => Project::query()->published()->with('category')->ordered()->get(),
            'categories' => Category::query()->ofType('project')->ordered()->get(),
        ]);
    }

    public function show(string $slug)
    {
        $project = Project::query()->published()->where('slug', $slug)->firstOrFail();

        return view('site.work.show', [
            'project' => $project,
            'more' => Project::query()->published()->where('id', '!=', $project->id)->ordered()->take(2)->get(),
        ]);
    }
}
