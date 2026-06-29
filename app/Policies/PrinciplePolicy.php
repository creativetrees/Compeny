<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Principle;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PrinciplePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Principle');
    }

    public function view(AuthUser $authUser, Principle $principle): bool
    {
        return $authUser->can('View:Principle');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Principle');
    }

    public function update(AuthUser $authUser, Principle $principle): bool
    {
        return $authUser->can('Update:Principle');
    }

    public function delete(AuthUser $authUser, Principle $principle): bool
    {
        return $authUser->can('Delete:Principle');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Principle');
    }

    public function restore(AuthUser $authUser, Principle $principle): bool
    {
        return $authUser->can('Restore:Principle');
    }

    public function forceDelete(AuthUser $authUser, Principle $principle): bool
    {
        return $authUser->can('ForceDelete:Principle');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Principle');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Principle');
    }

    public function replicate(AuthUser $authUser, Principle $principle): bool
    {
        return $authUser->can('Replicate:Principle');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Principle');
    }
}
