<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobApplicationController extends Controller
{
    public function create(Job $job): View
    {
        $this->authorize('apply', $job);
        return view('job_application.create', compact('job'));
    }

    public function store(Request $request, Job $job): RedirectResponse
    {
        $validatedData = $request->validate([
            'expected_salary' => 'required|integer|min:0|max:1000000',
            'cv' => 'required|file|mimes:pdf|max:2048',
        ]);

        $file = request()->file('cv');
        $path = $file->store('cvs', 'private');

        $job->jobApplications()->create([
            'user_id' => Auth::user()->id,
            'expected_salary' => $validatedData['expected_salary'],
            'cv_path' => $path,
        ]);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Job application submitted.');
    }

    public function destroy(string $id)
    {
        //
    }
}
