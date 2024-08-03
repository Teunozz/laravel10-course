<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'location', 'description', 'salary', 'experience', 'category'];

    public static array $category = ['IT', 'Finance', 'Sales', 'Marketing'];
    public static array $experience = ['entry', 'intermediate', 'senior'];

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function hasUserApplied(Authenticatable|User|int $user): bool
    {
        return $this->where('id', $this->id)
            ->whereHas(
                'jobApplications',
                fn($query) => $query->where('user_id', $user->id ?? $user),
            )
            ->exists();
    }

    public function scopeFilter(Builder|QueryBuilder $query, array $filters): Builder|QueryBuilder
    {
        $search = $filters['search'] ?? null;
        $minSalary = $filters['min_salary'] ?? null;
        $maxSalary = $filters['max_salary'] ?? null;
        $experience = $filters['experience'] ?? null;
        $category = $filters['category'] ?? null;

        return $query
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('title', 'like', '%' . $search . '%')
                            ->orWhere('description', 'like', '%' . $search . '%')
                            ->orWhereHas('employer', function ($query) use ($search) {
                                $query->where('company_name', 'like', '%' . $search . '%');
                            });
                    });
                })
                ->when($minSalary, function ($query) use ($minSalary) {
                    $query->where('salary', '>=', $minSalary);
                })
                ->when($maxSalary, function ($query) use ($maxSalary) {
                    $query->where('salary', '<=', $maxSalary);
                })
                ->when($experience, function ($query) use ($experience) {
                    $query->where('experience', $experience);
                })
                ->when($category, function ($query) use ($category) {
                    $query->where('category', $category);
                });
    }
}
