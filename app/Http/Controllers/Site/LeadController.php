<?php

namespace App\Http\Controllers\Site;

use App\Events\LeadReceived;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Service;
use App\Models\StartStep;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function create()
    {
        return view('site.start', [
            'services' => Service::query()->ordered()->get(),
            'budgets' => ['< $10k', '$10k–$25k', '$25k–$50k', '$50k+'],
            'steps' => StartStep::query()->ordered()->get(),
        ]);
    }

    public function store(Request $request)
    {
        // Honeypot: bots fill the hidden field — silently accept and drop.
        if (filled($request->input('company_url'))) {
            return redirect()->route('start')->with('lead_sent', true);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'company' => ['nullable', 'string', 'max:160'],
            'phone' => ['nullable', 'string', 'max:60'],
            'budget' => ['nullable', 'string', 'max:60'],
            'service_interest' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:4000'],
        ]);

        $lead = Lead::create($data + ['status' => 'new', 'source' => 'website']);

        event(new LeadReceived($lead));

        return redirect()->route('start')->with('lead_sent', true);
    }
}
