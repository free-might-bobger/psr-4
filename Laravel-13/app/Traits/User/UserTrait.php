<?php

declare(strict_types=1);

namespace App\Traits\User;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\Http\Requests\UserSkillRequest\IndexRequest;
use NPlanner\Shared\Exceptions\ValidationException;


trait UserTrait
{
    use FiltersRelationshipTrait;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var Builder
     */
    protected $whereHasRequestedByUser;

    /**
     * @var Builder
     */
    protected $whereHasAuthorisedByUser;

    /**
     * @var Builder
     */
    protected $whereHasUserSkillRequestInfo;

    /**
     * applyFilters.
     *
     * @param  Builder  $builder
     * @param  array    $filters
     * @return Builder
     */
    public function applyFilters(Builder $builder, array $filters): Builder
    {
        $this->builder = $builder;
        foreach ($filters as $name => $value) {
            $filterMethod = "filterBy" . Str::ucfirst(Str::camel($name));
            if (method_exists($this, $filterMethod)) {
                \call_user_func([$this, $filterMethod], $value);
            }
        }

        if ($this->whereHasAuthorisedByUser instanceof Builder) {
            $this->builder->addWhereExistsQuery($this->whereHasAuthorisedByUser->toBase());
        }

        if ($this->whereHasRequestedByUser instanceof Builder) {
            $this->builder->addWhereExistsQuery($this->whereHasRequestedByUser->toBase());
        }

        if ($this->whereHasUserSkillRequestInfo instanceof Builder) {
            $this->builder->addWhereExistsQuery($this->whereHasUserSkillRequestInfo->toBase());
        }

        return $this->builder;
    }

    /**
     * filter query by requested_by_user_id
     * @param  integer  $id
     * @return Builder
     */
    protected function filterByRequestedByUserId($id) : Builder
    {
        return $this->builder->whereIntegerInRaw('requested_by_user_id', CreateArray::apply($id));
    }

    /**
     * filter query by authorised_by_user_id
     * @param  integer  $id
     * @return Builder
     */
    protected function filterByAuthorisedByUserId($id) : Builder
    {
        return $this->builder->where('authorised_by_user_id', $id);
    }

    /**
     * filter query by requested_at
     * @param  integer  $id
     * @return Builder
     */
    protected function filterByRequestedAt($dateTime) : Builder
    {
        $dateTime = Carbon::parse($dateTime);
        if ($dateTime->hour == 0 && $dateTime->minute == 0 && $dateTime->second == 0) {
            return $this->builder->whereDate('requested_at', $dateTime);
        }
        return $this->builder->where('requested_at', ">=", $dateTime);
    }

    /**
     * filter query by request_status
     * @param  string  $status
     * @return Builder
     */
    protected function filterByRequestStatus($status) : Builder
    {
        return $this->builder->whereIn('request_status', CreateArray::apply($status));
    }

    /**
     * filter query by user skill request info skill_id
     * @param  integer  $id
     * @return Builder
     */
    protected function filterBySkillId($id) : Builder
    {
        $this->queryWhereHasUserSkillRequestInfo()->where('skill_id', $id);
        return $this->builder;
    }

    /**
     * filter query by user skill request info skill_id
     * @param  string $id
     * @return Builder
     */
    protected function filterBySkillIds($id) : Builder
    {
        $this->queryWhereHasUserSkillRequestInfo()->whereIntegerInRaw('skill_id', CreateArray::apply($id));
        return $this->builder;
    }

    /**
     * filter query by user skill request info rating
     * @param  integer $rating
     * @return Builder
     */
    protected function filterByRating($rating) : Builder
    {
        $this->queryWhereHasUserSkillRequestInfo()->where('rating', $rating);
        return $this->builder;
    }

    /**
     * filter query by user skill request info ratings
     * @param  string $rating
     * @return Builder
     */
    protected function filterByRatings($rating) : Builder
    {
        $this->queryWhereHasUserSkillRequestInfo()->whereIntegerInRaw('rating', CreateArray::apply($rating));
        return $this->builder;
    }

    /**
     * filter query by requested by user office_id
     * @param  integer $id
     * @return Builder
     */
    protected function filterByOfficeId($id) : Builder
    {
        $this->queryWhereHasRequestedByUser()->whereIntegerInRaw('office_id', CreateArray::apply($id));
        return $this->builder;
    }

    /**
     * filter query by requested by user name or github_username
     * @param  string  $name
     * @return Builder
     */
    protected function filterByUsername($name) : Builder
    {
        $this->queryWhereHasRequestedByUser()->where(
            function ($query) use ($name) {
                $wildcard = "%{$name}%";
                return $query->where('name', 'like', $wildcard)
                    ->orWhere('github_username', 'like', $wildcard);
            }
        );
        return $this->builder;
    }

    /**
     * filter query by requested by user email
     * @param  string  $name
     * @return Builder
     */
    protected function filterByEmail($name) : Builder
    {
        $this->queryWhereHasRequestedByUser()->where('email', 'like', "%{$name}%");
        return $this->builder;
    }

    /**
     * filter query by requested by user manager_id
     * @param  integer  $id
     * @return Builder
     */
    protected function filterByManagerUserId($id) : Builder
    {
        $this->queryWhereHasRequestedByUser()->where('manager_user_id', $id);
        return $this->builder;
    }

    /**
     * filter query by requested by user designated_role_id
     * @param  integer  $id
     * @return Builder
     */
    protected function filterByDesignatedRoleId($id) : Builder
    {
        $this->queryWhereHasRequestedByUser()->where('designated_role_id', $id);
        return $this->builder;
    }

    /**
     * get the relationship query for user skill request info
     * @param  integer  $id
     * @return Builder
     */
    protected function queryWhereHasUserSkillRequestInfo() : Builder
    {
        return $this->createRelationQuery('userSkillRequestInfo');
    }

    /**
     * get the relationship query for authorised by user
     * @param  integer  $id
     * @return Builder
     */
    protected function queryWhereHasAuthorisedByUser() : Builder
    {
        return $this->createRelationQuery('authorisedByUser');
    }

    /**
     * get the relationship query for requested by user
     * @param  integer  $id
     * @return Builder
     */
    protected function queryWhereHasRequestedByUser() : Builder
    {
        return $this->createRelationQuery('requestedByUser');
    }


}
