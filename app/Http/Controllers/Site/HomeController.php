<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('site.home', [
            'projects' => Project::query()->published()->ordered()->take(6)->get(),
            'services' => Service::query()->featured()->ordered()->get(),
            'clients' => Client::query()->featured()->ordered()->get(),
            'testimonials' => Testimonial::query()->ordered()->get(),
            'products' => Product::query()->published()->featured()->ordered()->take(3)->get(),
            'process' => \App\Models\ProcessPhase::query()->ordered()->get(),
        ]);
    }
}
