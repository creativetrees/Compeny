<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Client;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdmin();
        $this->seedSettings();
        $this->seedServices();
        $this->seedClients();
        $this->seedTeam();
        $categories = $this->seedCategories();
        $projects = $this->seedProjects($categories);
        $this->seedProducts($categories);
        $this->seedTestimonials($projects);
        $this->seedLeads();

        $this->call([
            PricingTierSeeder::class,
            PricingIncludeSeeder::class,
            ProcessPhaseSeeder::class,
            PrincipleSeeder::class,
            FaqSeeder::class,
            StartStepSeeder::class,
            NavLinkSeeder::class,
            SiteContentSeeder::class,
        ]);
    }

    private function seedAdmin(): void
    {
        // In production an admin is only seeded when ADMIN_SEED_PASSWORD is set,
        // so `db:seed` can never create a well-known default credential there.
        // Locally it falls back to "password" for convenience.
        $password = (string) env('ADMIN_SEED_PASSWORD', app()->isProduction() ? '' : 'password');

        if ($password === '') {
            $this->command?->warn('seedAdmin skipped: set ADMIN_SEED_PASSWORD to seed the admin user in production.');

            return;
        }

        User::updateOrCreate(
            ['email' => (string) env('ADMIN_SEED_EMAIL', 'admin@creativetrees.group')],
            ['name' => 'CTG Admin', 'password' => Hash::make($password)],
        );
    }

    private function seedSettings(): void
    {
        SiteSetting::updateOrCreate(['id' => 1], [
            'brand_name' => 'Creative Trees Group',
            'hero_eyebrow' => 'DIGITAL PRODUCT STUDIO & IT ECOSYSTEM',
            'hero_title' => "WE GROW DIGITAL\nPRODUCTS THAT SCALE",
            'hero_subtitle' => 'We help startups and teams turn ideas into powerful digital products — from strategy and design to scalable engineering.',
            'hero_cta_label' => 'Start a project',
            'hero_cta_url' => '/start',
            'about_heading' => 'A studio built like a product team.',
            'about_body' => "Creative Trees Group is a digital product studio. We design and engineer software the way the best in-house teams do — close to the problem, fast to ship, and obsessed with the details users actually feel.\n\nWe work as one embedded team across strategy, design, and engineering, so nothing gets lost in handoff.",
            'contact_email' => 'hello@creativetrees.group',
            'contact_phone' => '+62 811 2000 700',
            'contact_address' => 'Jakarta · Remote-first',
            'social_links' => [
                'x' => 'https://x.com/creativetrees',
                'linkedin' => 'https://linkedin.com/company/creativetrees',
                'github' => 'https://github.com/creativetrees',
                'dribbble' => 'https://dribbble.com/creativetrees',
            ],
            'stats' => [
                ['label' => 'Products shipped', 'value' => '120+'],
                ['label' => 'Avg. NPS', 'value' => '72'],
                ['label' => 'Years compounding', 'value' => '8'],
                ['label' => 'Team across', 'value' => '6 tz'],
            ],
            'seo_title' => 'Creative Trees Group — Digital Product Studio',
            'seo_description' => 'We design and build SaaS products that scale. Strategy, design, and engineering as one embedded team.',
            'footer_tagline' => 'Designed and built to compound.',
        ]);
    }

    private function seedServices(): void
    {
        $services = [
            ['Product Strategy', 'heroicon-o-map', 'Positioning, roadmaps, and the unglamorous decisions that decide whether a product lives.', ['Discovery sprints', 'Positioning', 'Roadmapping', 'Success metrics']],
            ['UX & UI Design', 'heroicon-o-swatch', 'Interfaces that feel inevitable — clear hierarchy, honest copy, zero decoration for its own sake.', ['Product design', 'Prototyping', 'Motion', 'Usability testing']],
            ['Web Engineering', 'heroicon-o-code-bracket', 'Fast, typed, well-tested codebases your team will still enjoy a year from now.', ['Laravel & PHP', 'TypeScript', 'APIs', 'Performance']],
            ['Mobile Apps', 'heroicon-o-device-phone-mobile', 'Native-feeling iOS and Android experiences from one focused codebase.', ['React Native', 'Offline-first', 'Push & realtime', 'App Store ops']],
            ['Design Systems', 'heroicon-o-squares-2x2', 'A single source of truth so design and code never drift apart again.', ['Tokens', 'Components', 'Documentation', 'Governance']],
            ['Platform & DevOps', 'heroicon-o-server-stack', 'The infrastructure, pipelines, and observability that keep shipping boring.', ['CI/CD', 'Docker', 'Observability', 'Scaling']],
        ];

        foreach ($services as $i => [$title, $icon, $summary, $caps]) {
            Service::updateOrCreate(['slug' => Str::slug($title)], [
                'title' => $title,
                'icon' => $icon,
                'summary' => $summary,
                'description' => $summary,
                'capabilities' => $caps,
                'is_featured' => true,
                'sort' => $i,
            ]);
        }
    }

    private function seedClients(): void
    {
        foreach (['Veltra', 'Zyntric', 'Orvian', 'Prismo', 'Trivox', 'Nexel', 'Caldera'] as $i => $name) {
            Client::updateOrCreate(['name' => $name], [
                'website_url' => 'https://'.Str::slug($name).'.com',
                'is_featured' => true,
                'sort' => $i,
            ]);
        }
    }

    private function seedTeam(): void
    {
        $team = [
            ['Arman Pratama', 'Founder & CEO', 'Sets the studio standard and keeps every engagement honest.'],
            ['Dewi Larasati', 'Design Lead', 'Turns ambiguity into interfaces people understand on first contact.'],
            ['Raka Wibowo', 'Engineering Lead', 'Owns architecture and the boring reliability that makes products trustworthy.'],
            ['Naomi Sutanto', 'Product Manager', 'Connects user problems to shippable scope without losing the plot.'],
            ['Bayu Anggara', 'Senior Engineer', 'Full-stack generalist who ships fast and writes tests anyway.'],
            ['Sinta Halim', 'Brand Designer', 'Gives every product a voice and a look it can defend.'],
        ];

        foreach ($team as $i => [$name, $role, $bio]) {
            TeamMember::updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name,
                'role' => $role,
                'bio' => $bio,
                'photo_path' => 'https://i.pravatar.cc/480?u='.Str::slug($name),
                'socials' => ['linkedin' => 'https://linkedin.com', 'x' => 'https://x.com'],
                'is_published' => true,
                'sort' => $i,
            ]);
        }
    }

    private function seedCategories(): array
    {
        $names = ['SaaS', 'Fintech', 'Healthtech', 'Marketplace', 'AI / ML', 'Developer Tools'];
        $out = [];
        foreach ($names as $i => $name) {
            $out[$name] = Category::updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name,
                'type' => 'project',
                'sort' => $i,
            ]);
        }

        return $out;
    }

    private function seedProjects(array $categories): array
    {
        $projects = [
            ['Veltra', 'Veltra — billing that scales', 'SaaS', 2026, '+38% conversion', ['Strategy', 'Design', 'Engineering']],
            ['Zyntric', 'Zyntric — an AI ops console', 'AI / ML', 2025, '−60% triage time', ['Design', 'Engineering']],
            ['Orvian', 'Orvian — healthtech onboarding', 'Healthtech', 2025, '4.9 App Store', ['Product', 'Mobile']],
            ['Prismo', 'Prismo — a developer marketplace', 'Marketplace', 2024, '12k devs', ['Strategy', 'Engineering']],
            ['Trivox', 'Trivox — fintech dashboard', 'Fintech', 2024, '+52% retention', ['Design', 'Engineering']],
            ['Nexel', 'Nexel — internal design system', 'Developer Tools', 2023, '1 source of truth', ['Design Systems']],
        ];

        $out = [];
        foreach ($projects as $i => [$client, $title, $cat, $year, $result, $services]) {
            $seed = 'ctw-'.Str::slug($client);
            $out[] = Project::updateOrCreate(['slug' => Str::slug($title)], [
                'category_id' => $categories[$cat]->id ?? null,
                'title' => $title,
                'client_name' => $client,
                'year' => $year,
                'role' => 'Design & Engineering',
                'summary' => "How we partnered with {$client} to design and build a product their team could grow on.",
                'body' => "We embedded with {$client} as one team across strategy, design, and engineering. The result is a product that ships weekly and earns its keep.",
                'cover_path' => "https://picsum.photos/seed/{$seed}/1280/860",
                'gallery' => [
                    "https://picsum.photos/seed/{$seed}-a/1280/860",
                    "https://picsum.photos/seed/{$seed}-b/1280/860",
                ],
                'services' => $services,
                'results' => [
                    ['label' => 'Outcome', 'value' => $result],
                    ['label' => 'Timeline', 'value' => (8 + $i).' wks'],
                ],
                'website_url' => 'https://'.Str::slug($client).'.com',
                'is_featured' => $i < 4,
                'status' => 'published',
                'sort' => $i,
            ]);
        }

        return $out;
    }

    private function seedProducts(array $categories): void
    {
        $products = [
            ['Sapling', 'SaaS', 'A production-ready SaaS starter — auth, billing, teams, and admin out of the box.', 'From $4,000', ['Auth & teams', 'Stripe billing', 'Admin panel', 'API']],
            ['Canopy', 'Template', 'A Filament-powered design system kit to launch a branded admin in days.', 'From $1,500', ['Tokens', 'Components', 'Dark mode', 'Docs']],
            ['Grove', 'Service', 'Product analytics setup and dashboards your whole team will actually read.', 'From $2,500', ['Tracking plan', 'Dashboards', 'Funnels', 'Alerts']],
        ];

        foreach ($products as $i => [$title, $type, $summary, $price, $features]) {
            Product::updateOrCreate(['slug' => Str::slug($title)], [
                'category_id' => $categories['SaaS']->id ?? null,
                'title' => $title,
                'type' => $type,
                'summary' => $summary,
                'description' => $summary,
                'price_label' => $price,
                'features' => $features,
                'cover_path' => 'https://picsum.photos/seed/ctp-'.Str::slug($title).'/1200/800',
                'cta_label' => 'Request access',
                'cta_url' => '/start',
                'is_featured' => true,
                'status' => 'published',
                'sort' => $i,
            ]);
        }
    }

    private function seedTestimonials(array $projects): void
    {
        $quotes = [
            ['They operated like our own team — just faster and with better taste.', 'VP Product', 'Veltra'],
            ['The clarity they brought to a messy problem was worth the engagement on its own.', 'CTO', 'Zyntric'],
            ['We shipped in eight weeks what we had failed to ship in a year.', 'Founder', 'Orvian'],
            ['Design and engineering finally spoke the same language. That never drifted.', 'Head of Design', 'Nexel'],
        ];

        foreach ($quotes as $i => [$quote, $role, $company]) {
            Testimonial::updateOrCreate(
                ['author' => $role.' · '.$company],
                [
                    'project_id' => $projects[$i]->id ?? null,
                    'role' => $role,
                    'company' => $company,
                    'quote' => $quote,
                    'avatar_path' => 'https://i.pravatar.cc/200?u='.Str::slug($company.$role),
                    'rating' => 5,
                    'is_featured' => true,
                    'sort' => $i,
                ],
            );
        }
    }

    private function seedLeads(): void
    {
        if (Lead::count() === 0) {
            Lead::factory()->count(14)->create();
        }
    }
}
