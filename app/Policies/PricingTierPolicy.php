<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PricingTier;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PricingTierPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PricingTier');
    }

    public function view(AuthUser $authUser, PricingTier $pricingTier): bool
    {
        return $authUser->can('View:PricingTier');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PricingTier');
    }

    public function update(AuthUser $authUser, PricingTier $pricingTier): bool
    {
        return $authUser->can('Update:PricingTier');
    }

    public function delete(AuthUser $authUser, PricingTier $pricingTier): bool
    {
        return $authUser->can('Delete:PricingTier');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PricingTier');
    }

    public function restore(AuthUser $authUser, PricingTier $pricingTier): bool
    {
        return $authUser->can('Restore:PricingTier');
    }

    public function forceDelete(AuthUser $authUser, PricingTier $pricingTier): bool
    {
        return $authUser->can('ForceDelete:PricingTier');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PricingTier');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PricingTier');
    }

    public function replicate(AuthUser $authUser, PricingTier $pricingTier): bool
    {
        return $authUser->can('Replicate:PricingTier');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PricingTier');
    }
}
