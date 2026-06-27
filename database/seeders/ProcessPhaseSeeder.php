<?php

namespace Database\Seeders;

use App\Models\ProcessPhase;
use Illuminate\Database\Seeder;

class ProcessPhaseSeeder extends Seeder
{
    public function run(): void
    {
        $phases = [
            [
                'name' => 'Discover',
                'lead' => 'We pressure-test the problem and the market before anyone touches a pixel.',
                'body' => 'Before a single screen is drawn, we interrogate the problem, the people, and the constraints. We map the riskiest assumptions, talk to the humans who will actually use the thing, and define what success has to look like in numbers. You leave this phase with a sharp scope and a reason to believe in it.',
                'deliverables' => ['Stakeholder interviews', 'Market teardown', 'Problem statement', 'Success metrics', 'Scope & roadmap'],
                'sort' => 0,
            ],
            [
                'name' => 'Design',
                'lead' => 'Interfaces and systems that make the right thing the obvious thing.',
                'body' => 'We translate the strategy into flows, wireframes, and a working interface language. Every screen is pressure-tested against real tasks rather than taste, and prototyped early so we learn before we build. The output is a design system engineers can run with — not a static mockup left open to interpretation.',
                'deliverables' => ['User flows', 'Wireframes', 'Interactive prototype', 'Design system', 'UX copy'],
                'sort' => 1,
            ],
            [
                'name' => 'Build',
                'lead' => 'Typed, tested, shippable software — delivered weekly, not quarterly.',
                'body' => 'Engineering ships in weekly increments against a typed, tested codebase you can read and own. We integrate continuously, demo working software every Friday, and keep the backlog ruthlessly prioritised so the highest-leverage work lands first. No big-bang reveal, no surprises waiting at the end.',
                'deliverables' => ['Typed codebase', 'CI/CD pipeline', 'Weekly demos', 'Automated tests', 'Staging environment'],
                'sort' => 2,
            ],
            [
                'name' => 'Scale',
                'lead' => 'Infrastructure, analytics, and iteration that keep the product compounding.',
                'body' => 'Launch is the start line, not the finish. We instrument the product, watch how it behaves in the wild, and iterate on the signals that move the metrics that matter. Infrastructure hardens, analytics sharpen, and the roadmap keeps compounding long after go-live.',
                'deliverables' => ['Observability', 'Analytics & funnels', 'Performance hardening', 'Iteration cadence', 'Handover & docs'],
                'sort' => 3,
            ],
        ];

        foreach ($phases as $phase) {
            ProcessPhase::updateOrCreate(['name' => $phase['name']], $phase);
        }
    }
}
