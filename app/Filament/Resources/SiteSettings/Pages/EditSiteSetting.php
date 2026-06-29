<?php

namespace App\Filament\Resources\SiteSettings\Pages;

use App\Filament\Resources\SiteSettings\SiteSettingResource;
use App\Models\SiteSetting;
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
     * Preserve page_content namespaces that have no form field (e.g. start.*).
     * Filament rebuilds an array-cast column from its registered fields only, so
     * without this merge an admin save would silently drop every un-exposed
     * page_content key, permanently reverting those pages to hardcoded defaults.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $existing = $this->record->page_content ?? [];
        $submitted = $data['page_content'] ?? [];

        $data['page_content'] = array_replace_recursive($existing, $submitted);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_site')
                ->label('Lihat situs')
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->color('gray')
                ->url('/', shouldOpenInNewTab: true),
        ];
    }
}
