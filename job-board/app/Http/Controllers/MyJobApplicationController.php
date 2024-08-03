<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class MyJobApplicationController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $applications = $user
            ->jobApplications()
            ->with([
                'job' => fn($query) => $query->withCount('jobApplications')
                    ->withAvg('jobApplications', 'expected_salary')
                    ->withTrashed(),
                'job.employer'
            ])            
            ->latest()
            ->get();

        return view('my_job_application.index', compact('applications'));
    }

    public function destroy(JobApplication $myJobApplication)
    {
        $myJobApplication->delete();
        return redirect()->back()->with(
            'success',
            'Job application removed',
        );
    }
}
