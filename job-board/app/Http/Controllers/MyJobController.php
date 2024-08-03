<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobRequest;
use App\Models\Job;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class MyJobController extends Controller
{    
    public function index(): View
    {
        $this->authorize('viewAnyEmployer', Job::class);

        /** @var User $user */
        $user = Auth::user();
        $jobs = $user->employer
            ->jobs()
            ->with(['employer', 'jobApplications', 'jobApplications.user'])
            ->withTrashed()
            ->latest()
            ->get();

        return view('my_job.index', compact('jobs'));
    }

    public function create(): View
    {
        $this->authorize('create', Job::class);
        return view('my_job.create');
    }

    public function store(JobRequest $request)
    {
        $this->authorize('create', Job::class);

        /** @var User $user */
        $user = Auth::user();
        $user->employer
            ->jobs()
            ->create($request->validated());

        return redirect()->route('my-jobs.index')
            ->with('success', 'Job created successfully!');
    }

    public function edit(Job $myJob)
    {
        $this->authorize('update', $myJob);
        return view('my_job.edit', ['job' => $myJob]);
    }

    public function update(JobRequest $request, Job $myJob)
    {
        $this->authorize('update', $myJob);

        $myJob->update($request->validated());

        return redirect()->route('my-jobs.index')
            ->with('success', 'Job updated successfully!');
    }

    public function destroy(Job $myJob)
    {
        $myJob->delete();

        return redirect()->route('my-jobs.index')
            ->with('success', 'Job deleted successfully!');
    }
}
