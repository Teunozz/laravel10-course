<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Database\Eloquent\Builder;

class BookController extends Controller
{
    const FILTER_LATEST = 'latest';
    const FILTER_POP_LAST_MONTH = 'pop_last_month';
    const FILTER_POP_LAST_6_MONTHS = 'pop_last_6_months';
    const FILTER_HIGH_LAST_MONTH = 'high_last_month';
    const FILTER_HIGH_LAST_6_MONTHS = 'high_last_6_months';

    public function index(Request $request): View
    {
        $filters = [
            self::FILTER_LATEST => 'Latest',
            self::FILTER_POP_LAST_MONTH => 'Popular Last Month',
            self::FILTER_POP_LAST_6_MONTHS => 'Popular Last 6 Months',
            self::FILTER_HIGH_LAST_MONTH => 'Highest Rated Last Month',
            self::FILTER_HIGH_LAST_6_MONTHS => 'Highest Rated Last 6 Months',
        ];

        $title = $request->get('title');
        $activeFilter = $request->get('filter');
        if (empty($activeFilter)) {
            $activeFilter = self::FILTER_LATEST;
        }

        $query = Book::when($title, fn (Builder $query, $title) => $query->title($title));
        $query = match ($activeFilter) {
            self::FILTER_POP_LAST_MONTH => $query->popularLastMonth(),
            self::FILTER_POP_LAST_6_MONTHS => $query = $query->popularLast6Months(),
            self::FILTER_HIGH_LAST_MONTH => $query->highestRatedLastMonth(),
            self::FILTER_HIGH_LAST_6_MONTHS => $query->highestRatedLast6Months(),
            default => $query->latest()->withReviewsCount()->withAvgRating(),
        };

        $cacheKey = 'books:' . $activeFilter . ':' . $title;
        $books = cache()->remember($cacheKey, 3600, fn () => $query->get());

        return view('books.index', compact('books', 'filters', 'activeFilter'));
    }

    public function show(int $id): View
    {
        $cacheKey = 'book:' . $id;
        $book = cache()->remember(
            $cacheKey,
            3600,
            fn () => Book::with([
                'reviews' => fn (HasMany $query) => $query->latest(),
            ])
                ->withReviewsCount()
                ->withAvgRating()
                ->firstOrFail($id)
        );

        return view('books.show', compact('book'));
    }
}
