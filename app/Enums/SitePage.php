<?php

namespace App\Enums;

/**
 * The site's public pages, as a single source of truth for every "pick a URL"
 * field in Site Settings (nav menu, CTAs). Add a case here when a new public
 * page ships and it appears in every page-picker automatically.
 *
 * The backed value IS the path stored in the DB and rendered into href, so the
 * frontend keeps working unchanged — the enum only constrains what an admin can
 * choose, removing typos and unsafe schemes.
 */
enum SitePage: string
{
    case Home = '/';
    case Work = '/work';
    case Services = '/services';
    case Products = '/products';
    case Pricing = '/pricing';
    case Process = '/process';
    case Team = '/team';
    case About = '/about';
    case Start = '/start';
    case Contact = '/contact';

    /** Human label for the admin dropdown. */
    public function label(): string
    {
        return match ($this) {
            self::Home => 'Home (Beranda)',
            self::Work => 'Work',
            self::Services => 'Services',
            self::Products => 'Products',
            self::Pricing => 'Pricing',
            self::Process => 'Process',
            self::Team => 'Team',
            self::About => 'About',
            self::Start => 'Start a project',
            self::Contact => 'Contact',
        };
    }

    /**
     * Options for a Filament Select: [path => "Label — /path"], so the admin sees
     * both the page name and the exact URL it stores.
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $page): array => [$page->value => $page->label().' — '.$page->value])
            ->all();
    }
}
