<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ProcessPhase;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ProcessPhasePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProcessPhase');
    }

    public function view(AuthUser $authUser, ProcessPhase $processPhase): bool
    {
        return $authUser->can('View:ProcessPhase');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProcessPhase');
    }

    public function update(AuthUser $authUser, ProcessPhase $processPhase): bool
    {
        return $authUser->can('Update:ProcessPhase');
    }

    public function delete(AuthUser $authUser, ProcessPhase $processPhase): bool
    {
        return $authUser->can('Delete:ProcessPhase');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ProcessPhase');
    }

    public function restore(AuthUser $authUser, ProcessPhase $processPhase): bool
    {
        return $authUser->can('Restore:ProcessPhase');
    }

    public function forceDelete(AuthUser $authUser, ProcessPhase $processPhase): bool
    {
        return $authUser->can('ForceDelete:ProcessPhase');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProcessPhase');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProcessPhase');
    }

    public function replicate(AuthUser $authUser, ProcessPhase $processPhase): bool
    {
        return $authUser->can('Replicate:ProcessPhase');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProcessPhase');
    }
}
