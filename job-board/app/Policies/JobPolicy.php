<?php

namespace App\Policies;

use App\Models\Job;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JobPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function viewAnyEmployer(User $user): bool
    {
        return true;
    }

    public function view(?User $user, Job $job): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->employer !== null;
    }

    public function update(User $user, Job $job): bool|Response
    {
        if ($user->employer->user_id !== $user->id) {
            return false;
        }

        if ($job->jobApplications()->count() > 0) {
            return Response::deny('Cannot change job with applications');
        }

        return true;
    }

    public function delete(User $user, Job $job): bool
    {
        return $user->employer->user_id === $user->id;
    }

    public function restore(User $user, Job $job): bool
    {
        return $user->employer->user_id === $user->id;
    }

    public function forceDelete(User $user, Job $job): bool
    {
        return $user->employer->user_id === $user->id;
    }

    public function apply(User $user, Job $job): bool
    {
        return !$job->hasUserApplied($user);
    }
}
