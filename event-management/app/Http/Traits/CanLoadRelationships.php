<?php

namespace App\Http\Traits;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;

trait CanLoadRelationships
{
    protected function shouldIncludeRelation(string $relation)
    {
        $include = request()->get('include');

        if (!$include) {
            return false;
        }

        $relations = array_map('trim', explode(',', $include));

        return in_array($relation, $relations);
    }

    public function loadRelationships(Model|Builder|EloquentBuilder $for, ?array $relations = null): Model|Builder|EloquentBuilder
    {
        $relations = $relations ?? $this->relations ?? [];

        foreach ($relations as $relation) {
            $for->when($this->shouldIncludeRelation($relation), fn ($q) => $for instanceof Model ? $for->load($relation) : $q->with($relation));
        }

        return $for;
    }
}
