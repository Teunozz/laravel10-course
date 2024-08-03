<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Job::class);

        $filter = $request->only(
            'search',
            'min_salary',
            'max_salary',
            'experience',
            'category',
        );

        return view('job.index', ['jobs' => Job::with(['employer'])->filter($filter)->latest()->get()]);
    }

    public function show(Job $job)
    {
        $this->authorize('view', $job);

        return view('job.show', ['job' => $job->load('employer.jobs')]);
    }
}
