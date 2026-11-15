<?php

namespace App\Policies;

use App\Models\National\Eregistry\Organisation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrganisationPolicy
{

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->hasRole(['registry', 'admin', 'system-admin'])) {
            return true;
        }
        return false;
    }


    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Organisation $organisation): bool
    {
        if ($user->hasRole(['registry']) && $user->organisation_id === $organisation->id) {
            return true;
        }

        if ($user->hasRole('system-admin')) {
            return true;
        }
        return false;
    }


    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->hasRole('system-admin')) {
            return true;
        }
        return false;
    }


    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Organisation $organisation): bool
    {
        if ($user->hasRole('system-admin')) {
            return true;
        }

        if ($user->hasRole(['registry']) && $user->organisation_id === $organisation->id) {
            return true;
        }

        return false;
    }


    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Organisation $organisation): bool
    {
        if ($user->hasRole('system-admin')) {
            return true;
        }

        if ($user->hasRole(['registry']) && $user->organisation_id === $organisation->id) {
            return true;
        }
        return false;
    }

    
    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Organisation $organisation): bool
    {
        if ($user->hasRole('system-admin')) {
            return true;
        }

        if ($user->hasRole(['registry']) && $user->organisation_id === $organisation->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Organisation $organisation): bool
    {
        if ($user->hasRole('system-admin')) {
            return true;
        }
        return false;
    }
}
