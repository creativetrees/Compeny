<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Faq;
use App\Models\PricingInclude;
use App\Models\PricingTier;
use App\Models\Principle;
use App\Models\ProcessPhase;
use App\Models\Product;
use App\Models\Service;
use App\Models\TeamMember;

class PageController extends Controller
{
    public function about()
    {
        return view('site.about', [
            'members' => TeamMember::query()->published()->ordered()->get(),
            'clients' => Client::query()->ordered()->get(),
            'values' => Principle::query()->ordered()->get(),
        ]);
    }

    public function process()
    {
        return view('site.process', [
            'services' => Service::query()->ordered()->get(),
            'phases' => ProcessPhase::query()->ordered()->get(),
            'principles' => Principle::query()->ordered()->get(),
        ]);
    }

    public function pricing()
    {
        return view('site.pricing', [
            'products' => Product::query()->published()->ordered()->get(),
            'services' => Service::query()->ordered()->get(),
            'tiers' => PricingTier::query()->ordered()->get(),
            'included' => PricingInclude::query()->ordered()->get(),
            'faqs' => Faq::query()->published()->ordered()->get(),
        ]);
    }

    public function contact()
    {
        return view('site.contact');
    }
}
