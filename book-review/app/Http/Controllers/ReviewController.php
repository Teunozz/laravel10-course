<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('throttle:reviews')
            ->only(['store']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Book $book): View
    {
        return view('books.reviews.create', compact('book'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Book $book)
    {
        $data = $request->validate([
            'review' => 'required|string|min:15',
            'rating' => 'required|integer|min:1|max:5'
        ]);

        $book->reviews()->create($data);

        return redirect(route('books.show', compact('book')));
    }
}
