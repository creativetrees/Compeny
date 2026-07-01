<?php

namespace Tests\Feature;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

/**
 * Guards that the whole admin panel stays under Shield RBAC. Any new widget, page,
 * or resource that is not gated (or explicitly excluded) fails these tests — which
 * is how the dashboard widgets were originally shipped un-gated.
 */
class ShieldCoverageTest extends TestCase
{
    public function test_every_widget_is_shield_gated(): void
    {
        $exclude = config('filament-shield.widgets.exclude', []);

        foreach (glob(app_path('Filament/Widgets/*.php')) as $file) {
            $class = 'App\\Filament\\Widgets\\'.pathinfo($file, PATHINFO_FILENAME);

            if (in_array($class, $exclude, true)) {
                continue;
            }

            $this->assertContains(
                HasWidgetShield::class,
                class_uses_recursive($class),
                "{$class} must `use HasWidgetShield` so it is gated by a Shield permission.",
            );
        }
    }

    public function test_every_custom_page_is_shield_gated(): void
    {
        $exclude = config('filament-shield.pages.exclude', []);

        foreach (glob(app_path('Filament/Pages/*.php')) as $file) {
            $class = 'App\\Filament\\Pages\\'.pathinfo($file, PATHINFO_FILENAME);

            if (in_array($class, $exclude, true)) {
                continue;
            }

            $this->assertContains(
                HasPageShield::class,
                class_uses_recursive($class),
                "{$class} must `use HasPageShield` so it is gated by a Shield permission.",
            );
        }
    }

    public function test_every_resource_model_has_a_policy(): void
    {
        foreach (glob(app_path('Filament/Resources/*/*Resource.php')) as $file) {
            $class = 'App\\Filament\\Resources\\'.str_replace(
                ['/', '.php'],
                ['\\', ''],
                str_replace(app_path('Filament/Resources').'/', '', $file),
            );

            $model = $class::getModel();

            $this->assertNotNull(
                Gate::getPolicyFor($model),
                "{$model} (via {$class}) must have a policy for Shield RBAC.",
            );
        }
    }
}
