<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class EmployerController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Employer::class);
    }

    public function create(): View
    {
        return view('employer.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'company_name' => 'required|string|min:3|max:255|unique:employers,company_name'
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->employer()->create($validatedData);

        return redirect()->route('jobs.index')
            ->with('success', 'Your employer account was created!');
    }
}
