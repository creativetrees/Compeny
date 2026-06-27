<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\Project;
use App\Models\Service;
use App\Models\TeamMember;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudioStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $newLeads = Lead::where('status', 'new')->count();
        $wonLeads = Lead::where('status', 'won')->count();

        return [
            Stat::make('New leads', $newLeads)
                ->description('Awaiting first response')
                ->descriptionIcon('heroicon-m-inbox-arrow-down')
                ->color($newLeads > 0 ? 'info' : 'gray'),
            Stat::make('Won', $wonLeads)
                ->description('Closed engagements')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success'),
            Stat::make('Projects', Project::count())
                ->description(Project::published()->count().' published')
                ->descriptionIcon('heroicon-m-briefcase'),
            Stat::make('Services', Service::count())
                ->description(TeamMember::published()->count().' people on the team')
                ->descriptionIcon('heroicon-m-squares-2x2'),
        ];
    }
}
