<?php

declare(strict_types=1);

namespace App\Traits\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Builder;

trait FiltersRelationshipTrait
{
    /**
     * get the relationship model
     * @param  string  $relation
     * @return Relation
     */
    protected function getRelationWithoutConstraints( string $relation)
    {
        return Relation::noConstraints(
            function () use ($relation) {
                return $this->builder->getModel()->$relation();
            }
        );
    }

    /**
     * get the query relationhip property
     * @param  string  $relation
     * @return Builder
     */
    protected function createRelationQuery($relation) : Builder
    {
        $property = 'whereHas'.ucfirst($relation);
        if (!$this->$property) {
            $relation = $this->getRelationWithoutConstraints($relation);
            $this->$property = $relation->getRelationExistenceQuery(
                $relation->getRelated()->newQueryWithoutRelationships(), $this->builder
            );
        }
        return $this->$property;

    }
}
