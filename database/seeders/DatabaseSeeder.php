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
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
        ]);
    }

    private function seedAdmin(): void
    {
        // In production an admin is only seeded when ADMIN_SEED_PASSWORD is set,
        // so `db:seed` can never create a well-known default credential there.
        // Locally it falls back to "password" for convenience.
        $password = (string) env('ADMIN_SEED_PASSWORD', app()->isProduction() ? '' : 'password');

        if ($password === '') {
            $this->command?->warn('seedAdmin skipped: set ADMIN_SEED_PASSWORD to seed the admin user.');

            return;
        }

        $email = (string) env('ADMIN_SEED_EMAIL', 'admin@creativetrees.group');

        // Username is derived from the email local-part (e.g. halfirzzha@gmail.com
        // → "halfirzzha"), lower-cased and stripped to the allowed characters.
        $username = (string) Str::of($email)
            ->before('@')
            ->lower()
            ->replaceMatches('/[^a-z0-9_.-]/', '');

        if (strlen($username) < 3) {
            $username = 'admin';
        }

        $admin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'CTG Admin',
                'username' => $username,
                'password' => Hash::make($password),
            ],
        );

        // Authorization is via Filament Shield: grant the `developer` role, which
        // bypasses every policy (the super-admin Gate::before). Permissions exist
        // after `php artisan shield:generate`; sync whatever is present.
        $developer = Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);
        $developer->syncPermissions(Permission::all());
        $admin->syncRoles([$developer]);
    }

    private function seedSettings(): void
    {
        SiteSetting::updateOrCreate(['id' => 1], [
            'brand_name' => 'Creative Trees Group',
            'header_description' => 'Digital product studio & IT ecosystem.',
            'nav_menu' => [
                ['label' => 'Work', 'url' => '/work'],
                ['label' => 'Services', 'url' => '/services'],
                ['label' => 'Pricing', 'url' => '/pricing'],
                ['label' => 'Process', 'url' => '/process'],
                ['label' => 'Team', 'url' => '/team'],
                ['label' => 'About', 'url' => '/about'],
            ],
            'hero_eyebrow' => 'DIGITAL PRODUCT STUDIO & IT ECOSYSTEM',
            'hero_title' => "WE GROW DIGITAL\nPRODUCTS THAT SCALE",
            'hero_subtitle' => 'We help startups and teams turn ideas into powerful digital products — from strategy and design to scalable engineering.',
            'hero_cta_label' => 'Start a project',
            'hero_cta_url' => '/start',
            'hero_cta_secondary_label' => 'View work',
            'hero_cta_secondary_url' => '/work',
            'about_heading' => 'A studio built like a product team.',
            'about_body' => "Creative Trees Group is a digital product studio. We design and engineer software the way the best in-house teams do — close to the problem, fast to ship, and obsessed with the details users actually feel.\n\nWe work as one embedded team across strategy, design, and engineering, so nothing gets lost in handoff.",
            'contact_email' => 'hello@creativetrees.group',
            'contact_phone' => '+62 811 2000 700',
            'contact_address' => 'Jakarta · Remote-first',
            'social_links' => [
                ['platform' => 'X', 'url' => 'https://x.com/creativetrees'],
                ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com/company/creativetrees'],
                ['platform' => 'GitHub', 'url' => 'https://github.com/creativetrees'],
                ['platform' => 'Dribbble', 'url' => 'https://dribbble.com/creativetrees'],
            ],
            'stats' => [
                ['label' => 'Products shipped', 'value' => '120+'],
                ['label' => 'Avg. NPS', 'value' => '72'],
                ['label' => 'Years compounding', 'value' => '8'],
                ['label' => 'Team across', 'value' => '6 tz'],
            ],
            'seo_title' => 'Creative Trees Group — Digital Product Studio',
            'seo_description' => 'We design and build SaaS products that scale. Strategy, design, and engineering as one embedded team.',
            'footer_tagline' => '<p>Designed and built to compound.</p>',
            // Per-page editable copy, resolved by content('page.key', default).
            'page_content' => [
                'header' => [
                    'cta_label' => 'Start a project',
                    'cta_url' => '/start',
                ],
                'home' => [
                    'trusted_eyebrow' => 'Trusted by innovative teams',
                    'cap_eyebrow' => 'Capabilities',
                    'cap_title' => 'Everything you need to launch and scale.',
                    'cap_intro' => 'One embedded team across strategy, design, and engineering — so nothing is lost in handoff.',
                    'work_eyebrow' => 'Selected work',
                    'work_title' => 'Proof, not promises.',
                    'process_eyebrow' => 'How we work',
                    'process_title' => 'A process built to de-risk the work.',
                    'process_intro' => 'Four phases, one continuous flow — each one de-risks the next.',
                    'signal_eyebrow' => 'Signal',
                    'signal_title' => 'What partners say.',
                ],
                'services' => [
                    'hero_eyebrow' => 'Services',
                    'hero_line1' => 'Capabilities',
                    'hero_line2' => 'that compound.',
                    'hero_intro' => "We keep strategy, design, and engineering under one roof. Each capability below stands on its own — and gets sharper the moment it's paired with the next.",
                    'disciplines_eyebrow' => 'The disciplines',
                    'disciplines_label' => 'Pick one — or the full stack',
                ],
                'work' => [
                    'hero_eyebrow' => 'Selected work',
                    'hero_title' => "Proof, not\npromises.",
                    'hero_intro' => "A selection of products we've designed and engineered — for founders, teams, and the people who use what they ship.",
                ],
                'products' => [
                    'hero_eyebrow' => 'Products',
                    'hero_line1' => 'Starters that ship',
                    'hero_line2' => 'in days, not months.',
                    'hero_intro' => "Productized building blocks — SaaS foundations, design-system templates, and embedded services — each engineered to the same standard as our custom work. Pick a starting point, tell us where you're headed, and we tailor it to your roadmap.",
                    'empty_eyebrow' => 'Catalog in progress',
                    'empty_message' => "We're packaging our next set of starters. Tell us what you're building and we'll scope a custom path in the meantime.",
                ],
                'pricing' => [
                    'hero_eyebrow' => 'Pricing',
                    'hero_title' => "Engagements,\npriced honestly.",
                    'hero_intro' => 'We are a studio, not a checkout. Every engagement is scoped to the work in front of it — the numbers below are honest starting points, where most projects begin rather than where they are capped.',
                    'tiers_eyebrow' => 'Engagement tiers',
                    'tiers_note' => 'Lead-based · scoped per project · no checkout',
                    'included_eyebrow' => 'No fine print',
                    'included_title' => "What's always included.",
                    'included_intro' => 'However we work together, a few things never change — the reasons engagements stay honest.',
                    'faq_eyebrow' => 'FAQ',
                    'faq_title' => 'Questions, answered.',
                ],
                'process' => [
                    'hero_eyebrow' => 'How we work',
                    'hero_title' => 'A process built to de-risk the work.',
                    'hero_intro' => 'Four phases, one embedded team, zero handoffs. We spend the riskiest assumptions first and ship working software every week — so the path from idea to scale is something you can see, not something you have to trust.',
                    'sequence_eyebrow' => 'The sequence',
                    'phases_label' => 'phases',
                    'principles_eyebrow' => 'Operating principles',
                    'principles_title' => 'The rules that keep the work honest.',
                    'principles_intro' => 'Four constraints we hold on every engagement — the reason the process stays honest when the deadlines get loud.',
                ],
                'team' => [
                    'hero_eyebrow' => 'Team',
                    'hero_title' => "The people behind\nthe work.",
                    'hero_intro' => 'No account layers, no handoffs. Creative Trees is a small, senior team of strategists, designers, and engineers who embed directly with yours — and stay accountable from the first sketch to production traffic.',
                    'studio_eyebrow' => 'The studio',
                ],
                'about' => [
                    'hero_eyebrow' => 'About',
                    'values_eyebrow' => 'What we value',
                    'values_title' => 'How we think.',
                    'team_eyebrow' => 'The team',
                    'team_title' => 'Senior, embedded, accountable.',
                    'team_link' => 'Meet everyone',
                    'clients_eyebrow' => 'In good company',
                ],
                'contact' => [
                    'hero_eyebrow' => 'Contact',
                    'hero_title' => "Let's talk.",
                    'hero_intro' => "A fully-scoped build or a half-formed idea — either is a good place to start. Tell us where you're headed and we'll come back with the shortest honest path to get there.",
                ],
                'start' => [
                    'hero_eyebrow' => 'Start a project',
                    'hero_title' => "Tell us where\nyou're headed.",
                    'hero_intro' => "Share a few details about what you're building. We'll tell you the shortest honest path to get there.",
                ],
                'footer' => [
                    'contact_label' => 'Contact',
                ],
            ],
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
            ['They operated like our own team — just faster and with better taste.', 'Sari Wijaya', 'VP Product', 'Veltra'],
            ['The clarity they brought to a messy problem was worth the engagement on its own.', 'Daniel Tan', 'CTO', 'Zyntric'],
            ['We shipped in eight weeks what we had failed to ship in a year.', 'Maya Larasati', 'Founder', 'Orvian'],
            ['Design and engineering finally spoke the same language. That never drifted.', 'Rizki Hartono', 'Head of Design', 'Nexel'],
        ];

        foreach ($quotes as $i => [$quote, $author, $role, $company]) {
            Testimonial::updateOrCreate(
                ['author' => $author],
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
