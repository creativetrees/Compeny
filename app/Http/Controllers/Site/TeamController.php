<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;

class TeamController extends Controller
{
    public function index()
    {
        return view('site.team', [
            'members' => TeamMember::query()->published()->ordered()->get(),
        ]);
    }
}
