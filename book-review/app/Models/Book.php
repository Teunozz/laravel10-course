<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::updated(fn (Book $book) => cache()->forget('book:' . $book->book_id));
        static::deleted(fn (Book $book) => cache()->forget('book:' . $book->book_id));
    }

    private function dateRangeFilter(Builder $query, $from = null, $to = null): ?Builder
    {
        if ($from && !$to) {
            return $query->where('created_at', '>=', $from);
        } else if (!$from && $to) {
            return $query->where('created_at', '<=', $to);
        } else if ($from && $to) {
            return $query->whereBetween('created_at', [$from, $to]);
        }

        return $query;
    }

    private function dateRangeParamBuilder(int $monthsToSub): array
    {
        $to = now();
        $from = $to->clone()->subMonths($monthsToSub);

        return [$from, $to];
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function scopeTitle(Builder $query, string $title): Builder
    {
        return $query->where('title', 'LIKE', '%' . $title . '%');
    }

    public function scopeWithReviewsCount(Builder $query, $from = null, $to = null): Builder
    {
        return $query->withCount(
            [
                'reviews' => fn (Builder $q) => $this->dateRangeFilter($q, $from, $to),
            ]
        );
    }

    public function scopePopular(Builder $query, $from = null, $to = null): Builder
    {
        return $query->withReviewsCount($from, $to)
            ->orderBy('reviews_count', 'desc');
    }

    public function scopeWithAvgRating(Builder $query, $from = null, $to = null): Builder
    {
        return $query->withAvg([
            'reviews' => fn (Builder $q) => $this->dateRangeFilter($q, $from, $to),
        ], 'rating');
    }

    public function scopeHighestRated(Builder $query, $from = null, $to = null): Builder
    {
        return $query->withAvgRating($from, $to)
            ->orderBy('reviews_avg_rating', 'desc');
    }

    public function scopeMinReviews(Builder $query, int $minReviews): Builder
    {
        return $query->having('reviews_count', '>=', $minReviews);
    }

    public function scopePopularSinceMonthsAgo(Builder $query, int $monthsAgo): Builder
    {
        [$from, $to] = $this->dateRangeParamBuilder($monthsAgo);
        return $query->popular($from, $to)
            ->highestRated($from, $to);
    }

    public function scopePopularLastMonth(Builder $query): Builder
    {
        return $query->popularSinceMonthsAgo(1)
            ->minReviews(2);
    }

    public function scopePopularLast6Months(Builder $query): Builder
    {
        return $query->popularSinceMonthsAgo(6)
            ->minReviews(5);
    }

    public function scopeHighestRatedSinceMonthsAgo(Builder $query, int $monthsToSub): Builder
    {
        [$from, $to] = $this->dateRangeParamBuilder($monthsToSub);
        return $query->highestRated($from, $to)
            ->popular($from, $to);
    }

    public function scopeHighestRatedLastMonth(Builder $query): Builder
    {
        return $query->highestRatedSinceMonthsAgo(1)
            ->minReviews(2);
    }

    public function scopeHighestRatedLast6Months(Builder $query): Builder
    {
        return $query->highestRatedSinceMonthsAgo(6)
            ->minReviews(5);
    }
}
