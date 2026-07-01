<?php

namespace App\Filament\Resources\SiteSettings\Pages;

use App\Filament\Resources\SiteSettings\SiteSettingResource;
use App\Models\SiteSetting;
use App\Support\Html;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditSiteSetting extends EditRecord
{
    protected static string $resource = SiteSettingResource::class;

    /**
     * Singleton: there is exactly one settings row, edited directly. The
     * resource's index route points here, so no record id appears in the URL.
     */
    public function mount(int|string|null $record = null): void
    {
        parent::mount(SiteSetting::query()->firstOrCreate(['id' => 1])->getKey());
    }

    public function getTitle(): string
    {
        return 'Site Settings';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    /**
     * Two save-time fixups:
     *  1. Preserve page_content namespaces that have no form field (e.g. start.*).
     *     Filament rebuilds an array-cast column from its registered fields only, so
     *     without this merge an admin save would silently drop every un-exposed
     *     page_content key, permanently reverting those pages to hardcoded defaults.
     *  2. Route mail-account passwords into the encrypted `email_secrets` column and
     *     strip plaintext from the non-secret `emails` column (see below).
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $existing = $this->record->page_content ?? [];
        $submitted = $data['page_content'] ?? [];

        $data['page_content'] = array_replace_recursive($existing, $submitted);

        // Mail-account passwords: never store plaintext in `emails`. Move each typed
        // password into `email_secrets` (encrypted cast), keyed by lowercase address;
        // keep the existing secret when the field is left blank; drop orphaned addresses.
        $secrets = $this->record->email_secrets ?? [];
        $rows = $data['emails'] ?? [];
        $kept = [];

        foreach ($rows as $i => $row) {
            $address = strtolower(trim($row['address'] ?? ''));
            $password = $row['password'] ?? null;

            unset($rows[$i]['password']);   // plaintext never persists in `emails`

            if ($address === '') {
                continue;
            }

            if (filled($password)) {
                $secrets[$address] = $password;
            }

            if (filled($secrets[$address] ?? null)) {
                $kept[$address] = $secrets[$address];
            }
        }

        $data['emails'] = array_values($rows);
        $data['email_secrets'] = $kept;

        // Sanitize admin-authored rich HTML so a compromised admin session cannot
        // persist XSS that would run for every public visitor (footer, hero, error
        // & maintenance pages all render this content raw with {!! !!}).
        $data['page_content'] = Html::cleanDeep($data['page_content']);

        foreach (['about_body', 'hero_subtitle', 'footer_tagline', 'footer_cta_body'] as $richField) {
            if (array_key_exists($richField, $data) && is_string($data[$richField])) {
                $data[$richField] = Html::clean($data[$richField]);
            }
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_site')
                ->label('View site')
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->color('gray')
                ->url('/', shouldOpenInNewTab: true),
        ];
    }
}
