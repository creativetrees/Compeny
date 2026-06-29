<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\StartStep;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class StartStepPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StartStep');
    }

    public function view(AuthUser $authUser, StartStep $startStep): bool
    {
        return $authUser->can('View:StartStep');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StartStep');
    }

    public function update(AuthUser $authUser, StartStep $startStep): bool
    {
        return $authUser->can('Update:StartStep');
    }

    public function delete(AuthUser $authUser, StartStep $startStep): bool
    {
        return $authUser->can('Delete:StartStep');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StartStep');
    }

    public function restore(AuthUser $authUser, StartStep $startStep): bool
    {
        return $authUser->can('Restore:StartStep');
    }

    public function forceDelete(AuthUser $authUser, StartStep $startStep): bool
    {
        return $authUser->can('ForceDelete:StartStep');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StartStep');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StartStep');
    }

    public function replicate(AuthUser $authUser, StartStep $startStep): bool
    {
        return $authUser->can('Replicate:StartStep');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StartStep');
    }
}
