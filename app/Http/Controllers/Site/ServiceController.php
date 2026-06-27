<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        return view('site.services', [
            'services' => Service::query()->ordered()->get(),
        ]);
    }
}
