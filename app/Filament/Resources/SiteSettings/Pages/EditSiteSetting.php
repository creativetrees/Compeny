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
