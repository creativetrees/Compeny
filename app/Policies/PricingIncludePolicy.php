<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PricingInclude;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PricingIncludePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PricingInclude');
    }

    public function view(AuthUser $authUser, PricingInclude $pricingInclude): bool
    {
        return $authUser->can('View:PricingInclude');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PricingInclude');
    }

    public function update(AuthUser $authUser, PricingInclude $pricingInclude): bool
    {
        return $authUser->can('Update:PricingInclude');
    }

    public function delete(AuthUser $authUser, PricingInclude $pricingInclude): bool
    {
        return $authUser->can('Delete:PricingInclude');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PricingInclude');
    }

    public function restore(AuthUser $authUser, PricingInclude $pricingInclude): bool
    {
        return $authUser->can('Restore:PricingInclude');
    }

    public function forceDelete(AuthUser $authUser, PricingInclude $pricingInclude): bool
    {
        return $authUser->can('ForceDelete:PricingInclude');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PricingInclude');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PricingInclude');
    }

    public function replicate(AuthUser $authUser, PricingInclude $pricingInclude): bool
    {
        return $authUser->can('Replicate:PricingInclude');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PricingInclude');
    }
}
