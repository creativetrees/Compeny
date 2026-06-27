<?php

namespace Database\Seeders;

use App\Models\SiteContent;
use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        // [key, group, label, value] — values mirror the inline defaults wired
        // into the Blade views, so editing here changes the live copy.
        $items = [
            // ── Home ──
            ['home.trusted_eyebrow', 'Home', 'Trusted-by eyebrow', "Trusted by innovative teams"],
            ['home.cap_eyebrow', 'Home', 'Capabilities eyebrow', "Capabilities"],
            ['home.cap_title', 'Home', 'Capabilities title', "Everything you need to launch and scale."],
            ['home.cap_intro', 'Home', 'Capabilities intro', "One embedded team across strategy, design, and engineering — so nothing is lost in handoff."],
            ['home.work_eyebrow', 'Home', 'Selected work eyebrow', "Selected work"],
            ['home.work_title', 'Home', 'Selected work title', "Proof, not promises."],
            ['home.process_eyebrow', 'Home', 'Process eyebrow', "How we work"],
            ['home.process_title', 'Home', 'Process title', "A process built to de-risk the work."],
            ['home.process_intro', 'Home', 'Process intro', "Four phases, one continuous flow — each one de-risks the next."],
            ['home.signal_eyebrow', 'Home', 'Signal eyebrow', "Signal"],
            ['home.signal_title', 'Home', 'Signal title', "What partners say."],

            // ── Services ──
            ['services.hero_eyebrow', 'Services', 'Hero eyebrow', "Services"],
            ['services.hero_line1', 'Services', 'Hero headline line 1', "Capabilities"],
            ['services.hero_line2', 'Services', 'Hero headline line 2', "that compound."],
            ['services.hero_intro', 'Services', 'Hero intro', "We keep strategy, design, and engineering under one roof. Each capability below stands on its own — and gets sharper the moment it's paired with the next."],
            ['services.disciplines_eyebrow', 'Services', 'Disciplines eyebrow', "The disciplines"],
            ['services.disciplines_label', 'Services', 'Disciplines label', "Pick one — or the full stack"],

            // ── Products ──
            ['products.hero_eyebrow', 'Products', 'Hero eyebrow', "Products"],
            ['products.hero_line1', 'Products', 'Hero headline line 1', "Starters that ship"],
            ['products.hero_line2', 'Products', 'Hero headline line 2', "in days, not months."],
            ['products.hero_intro', 'Products', 'Hero intro', "Productized building blocks — SaaS foundations, design-system templates, and embedded services — each engineered to the same standard as our custom work. Pick a starting point, tell us where you're headed, and we tailor it to your roadmap."],
            ['products.empty_eyebrow', 'Products', 'Empty-state eyebrow', "Catalog in progress"],
            ['products.empty_message', 'Products', 'Empty-state message', "We're packaging our next set of starters. Tell us what you're building and we'll scope a custom path in the meantime."],

            // ── Pricing ──
            ['pricing.hero_eyebrow', 'Pricing', 'Hero eyebrow', "Pricing"],
            ['pricing.hero_title', 'Pricing', 'Hero heading', "Engagements,\npriced honestly."],
            ['pricing.hero_intro', 'Pricing', 'Hero intro', "We are a studio, not a checkout. Every engagement is scoped to the work in front of it — the numbers below are honest starting points, where most projects begin rather than where they are capped."],
            ['pricing.tiers_eyebrow', 'Pricing', 'Tiers eyebrow', "Engagement tiers"],
            ['pricing.tiers_note', 'Pricing', 'Tiers note', "Lead-based · scoped per project · no checkout"],
            ['pricing.included_eyebrow', 'Pricing', 'Included eyebrow', "No fine print"],
            ['pricing.included_title', 'Pricing', 'Included title', "What's always included."],
            ['pricing.included_intro', 'Pricing', 'Included intro', "However we work together, a few things never change — the reasons engagements stay honest."],
            ['pricing.faq_eyebrow', 'Pricing', 'FAQ eyebrow', "FAQ"],
            ['pricing.faq_title', 'Pricing', 'FAQ title', "Questions, answered."],

            // ── Process ──
            ['process.hero_eyebrow', 'Process', 'Hero eyebrow', "How we work"],
            ['process.hero_title', 'Process', 'Hero heading', "A process built to de-risk the work."],
            ['process.hero_intro', 'Process', 'Hero intro', "Four phases, one embedded team, zero handoffs. We spend the riskiest assumptions first and ship working software every week — so the path from idea to scale is something you can see, not something you have to trust."],
            ['process.sequence_eyebrow', 'Process', 'Sequence eyebrow', "The sequence"],
            ['process.phases_label', 'Process', 'Phase count suffix', "phases"],
            ['process.principles_eyebrow', 'Process', 'Principles eyebrow', "Operating principles"],
            ['process.principles_title', 'Process', 'Principles title', "The rules that keep the work honest."],
            ['process.principles_intro', 'Process', 'Principles intro', "Four constraints we hold on every engagement — the reason the process stays honest when the deadlines get loud."],

            // ── Team ──
            ['team.hero_eyebrow', 'Team', 'Hero eyebrow', "Team"],
            ['team.hero_title', 'Team', 'Hero heading', "The people behind\nthe work."],
            ['team.hero_intro', 'Team', 'Hero intro', "No account layers, no handoffs. Creative Trees is a small, senior team of strategists, designers, and engineers who embed directly with yours — and stay accountable from the first sketch to production traffic."],
            ['team.studio_eyebrow', 'Team', 'Member grid eyebrow', "The studio"],

            // ── About ──
            ['about.hero_eyebrow', 'About', 'Hero eyebrow', "About"],
            ['about.values_eyebrow', 'About', 'Values eyebrow', "What we value"],
            ['about.values_title', 'About', 'Values title', "How we think."],
            ['about.team_eyebrow', 'About', 'Team eyebrow', "The team"],
            ['about.team_title', 'About', 'Team title', "Senior, embedded, accountable."],
            ['about.team_link', 'About', 'Team link text', "Meet everyone"],
            ['about.clients_eyebrow', 'About', 'Clients eyebrow', "In good company"],

            // ── Start ──
            ['start.hero_eyebrow', 'Start', 'Hero eyebrow', "Start a project"],
            ['start.hero_title', 'Start', 'Hero heading', "Tell us where\nyou're headed."],
            ['start.hero_intro', 'Start', 'Hero intro', "Share a few details about what you're building. We'll tell you the shortest honest path to get there."],
            ['start.success_title', 'Start', 'Success heading', "Brief received."],
            ['start.success_message', 'Start', 'Success message', "Thank you — your brief is in. A real person will read it and reply within one business day."],

            // ── Contact ──
            ['contact.hero_eyebrow', 'Contact', 'Hero eyebrow', "Contact"],
            ['contact.hero_title', 'Contact', 'Hero heading', "Let's talk."],
            ['contact.hero_intro', 'Contact', 'Hero intro', "A fully-scoped build or a half-formed idea — either is a good place to start. Tell us where you're headed and we'll come back with the shortest honest path to get there."],
            ['contact.meta_response_label', 'Contact', 'Meta response label', "Response"],
            ['contact.meta_response_value', 'Contact', 'Meta response value', "Within 1 business day"],
            ['contact.meta_based_label', 'Contact', 'Meta based label', "Based"],
            ['contact.meta_based_value', 'Contact', 'Meta based value', "Jakarta · Remote-first"],
            ['contact.inquiry_label', 'Contact', 'Inquiry label', "New project"],
            ['contact.inquiry_title', 'Contact', 'Inquiry heading', "Project inquiries move fastest through the brief."],
            ['contact.inquiry_body', 'Contact', 'Inquiry body', "Answer a handful of questions about scope, timeline, and budget. We read every brief ourselves and reply within one business day — with a real next step, never an auto-response."],
            ['contact.inquiry_cta', 'Contact', 'Inquiry primary button', "Start a project"],
            ['contact.inquiry_secondary_cta', 'Contact', 'Inquiry secondary button', "See the work"],
            ['contact.direct_label', 'Contact', 'Direct lines label', "Direct lines"],
            ['contact.email_label', 'Contact', 'Email label', "Email"],
            ['contact.phone_label', 'Contact', 'Phone label', "Phone"],
            ['contact.studio_label', 'Contact', 'Studio label', "Studio"],
            ['contact.elsewhere_label', 'Contact', 'Elsewhere label', "Elsewhere"],
            ['contact.note', 'Contact', 'Direct lines note', "Press, partnerships, or careers? The same inbox reaches us — just say which."],
            ['contact.write_eyebrow', 'Contact', 'Mailto eyebrow', "Or just write"],
            ['contact.write_note', 'Contact', 'Mailto note', "Still, the fastest path is the brief — it gets you a scoped answer, not a thread."],

            // ── Footer ──
            ['footer.cta_eyebrow', 'Footer', 'CTA eyebrow', "Let's build"],
            ['footer.cta_title', 'Footer', 'CTA heading', "Have something\nworth building?"],
            ['footer.cta_body', 'Footer', 'CTA supporting text', "Tell us where you're headed. We'll tell you the shortest honest path to get there."],
            ['footer.cta_button', 'Footer', 'CTA button', "Start a project"],
            ['footer.contact_label', 'Footer', 'Contact column heading', "Contact"],
        ];

        foreach ($items as $i => [$key, $group, $label, $value]) {
            SiteContent::updateOrCreate(
                ['key' => $key],
                ['group' => $group, 'label' => $label, 'value' => $value, 'sort' => $i],
            );
        }
    }
}
