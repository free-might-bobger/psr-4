<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use App\Repositories\Support\ColumnValueCriteria;
use App\Traits\Obfuscate\OptimusId;
use Illuminate\Support\Arr;
use App\Traits\Support\BaseSupportRepository;
use App\Models\Image;
use App\Traits\RoleTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Http\Requests\BaseIndexRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
class BaseRepository implements BaseInterface
{
    use RoleTrait, ColumnValueCriteria, OptimusId, BaseSupportRepository;

    protected Model|Builder|null $model;
    protected array $params;
    protected ?string $cacheKey;
    protected array $fillable;
    protected object $request;
    protected array $associatedClass;
    protected string $name;
    protected string $fileName;
    protected int $size;

    /**
     * Set the model
     * @param Model $model
     * @return self
     */
    public function setModel(Model $model): self
    {
        $this->model = $model;
        return $this;
    }

     /**
     * Set the fillable
     * @return void
     */
    public function setFillable(): void
    {
        $this->fillable = $this->model->getFillable();
    }

    /**
     * Like the resource
     * @param string $column
     * @param string $value
     * @return self
     */
    public function like(string $column, string $value): self
    {
        $this->model = $this->model->where($column, 'LIKE', '%' . $value . '%');
        return $this;
    }

    /**
     * First the resource
     * @return Model
     */
    public function first(): Model
    {
        return $this->model->first();
    }

    /**
     * Find the resource or fail
     * @param int $id
     * @param array $relations
     * @return Model
     */
    public function findOrFail(int $id, array $relations = []): Model
    {
        // If model is a Builder (after filterQuery), use it directly
        if ($this->model instanceof Builder) {
            $query = $this->model;
        } else {
            $query = $this->getFreshQuery();
            $this->applyStoredFiltersToQuery($query);
        }
        
        if (!empty($relations)) {
            $query = $query->with($relations);
        }
        return $query->findOrFail($this->optimus()->decode($id));
    }

    /**
     * Get a fresh query builder instance
     * @return Builder
     */
    private function getFreshQuery(): Builder
    {
        $modelClass = get_class($this->model instanceof Model ? $this->model : $this->model->getModel());
        return $modelClass::query();
    }

    /**
     * Create the resource
     * @return Model
     */
    public function create(): Model
    {
        return $this->model->create($this->params);
    }

    //filters=id:1,name:as
    /**
     * Filter the resource
     * @param array $parameters
     * @return self
     */
    public function filterQuery(array $parameters): self
    {
        $this->setParameters($parameters);
        $filters = $this->pregSplit('@,@', Arr::get($parameters, 'filters', ''));
        foreach ($filters as $filterKeys => $filterValues) {
            [$column, $value] = $this->pregSplit('@:@', $filterValues);
            if (method_exists($this, $column)) {
                call_user_func([$this, $column], $value);
            }
        }
        $this->with();
        $this->orderBy(Arr::get($parameters, 'orderBy', 'created_at:desc'));
        $this->deleted(Arr::get($parameters, 'deleted', null));
        return $this;
    }

    //type=listing
    /**
     * Get the results
     * @return LengthAwarePaginator|Collection
     */
    public function getResults(): LengthAwarePaginator|Collection
    {
        $limit = Arr::get($this->params, 'limit', 12);
        $type  = Arr::get($this->params, 'type', false);
        
        // If model is a Builder (after filterQuery), use it directly
        if ($this->model instanceof Builder) {
            if ($type) {
                $result = $this->model->take($limit)->get();
                // Debug: Log the type if it's still wrong
                if ($result instanceof Builder) {
                    // Force execution
                    return $result->get();
                }
                return $result;
            }
            $result = $this->model->paginate($limit);
            // Debug: Log the type if it's still wrong  
            if ($result instanceof Builder) {
                // Force execution
                return $result->paginate($limit);
            }
            return $result;
        }
        
        // Otherwise, get fresh query and apply filters
        $query = $this->getFreshQuery();
        $this->applyStoredFiltersToQuery($query);
        
        if ($type) {
            return $query->take($limit)->get();
        }
        return $query->paginate($limit);
    }

    /**
     * Apply stored filters to a fresh query
     * @param Builder $query
     */
    private function applyStoredFiltersToQuery(Builder $query): void
    {
        // Re-apply the filters that were applied during filterQuery
        $filters = $this->pregSplit('@,@', Arr::get($this->params, 'filters', ''));
        foreach ($filters as $filterKeys => $filterValues) {
            [$column, $value] = $this->pregSplit('@:@', $filterValues);
            if (method_exists($this, $column)) {
                // Create a temporary repository with the fresh query
                $tempRepo = clone $this;
                $tempRepo->model = $query;
                call_user_func([$tempRepo, $column], $value);
                // Update the query with any modifications
                $query = $tempRepo->model;
            }
        }
        
        // Apply other filters
        $this->applyWithToQuery($query);
        $this->applyOrderByToQuery($query, Arr::get($this->params, 'orderBy', 'created_at:desc'));
        $this->applyDeletedToQuery($query, Arr::get($this->params, 'deleted', null));
    }

    /**
     * Set the parameters
     * @param array $parameters
     * @return self
     */
    public function setParameters(array $parameters): self
    {
        $this->params = $parameters;
        return $this;
    }

    /**
     * Where the resource
     * @param string $field
     * @param int $optimusId
     * @return Builder
     */
    public function where(string $field, int $optimusId): Builder
    {
        $this->model = $this->model->where($field, $this->optimus()->decode($optimusId));
        $this->params = app(BaseIndexRequest::class)->all();
        if(Arr::get($this->params, 'with')){
            $this->with();

        }
        return $this->model;
    }


    /**
     * Order the resource
     * @param string $param
     * @return self
     */
    public function orderBy(string $param): self
    {
        [$column, $value] = $this->pregSplit('@:@', $param);
        $this->model = $this->model->orderBy($column, $value);
        return $this;
    }

    /**
     * Limit the resource
     * @return self
     */
    public function limit(): self
    {
        $limit = Arr::get($this->params, 'limit', 15);
        $this->model = $this->model->limit($limit);
        return $this;
    }

    //type=collection
    /**
     * Get the resource
     * @return Collection|LengthAwarePaginator
     */
    public function get(): Collection|LengthAwarePaginator
    {
        if (Arr::get($this->params, 'type', null) === 'collection') {
            return $this->model->get();
        }
        return $this->model->paginate(Arr::get($this->params, 'limit', 100));
    }

    //&columns=id,name
    /**
     * Columns the resource
     * @return self
     */
    public function columns(): self
    {
        $this->model = $this->model->select($this->pregSplit('@,@',  Arr::get($this->params, 'columns', [])));
        return $this;
    }

    //&with=itemPrice.unit:id;<;20,images:id;1
    /**
     * With the resource
     * @return self
     */
    public function with(): self
    {
        $relationships = $this->pregSplit('@,@', Arr::get($this->params, 'with'));
        foreach ($relationships as $relationship) {
            if (!$relationship) {
                continue;
            }

            $pregSplit = $this->pregSplit('@:@', $relationship);
            if (count($pregSplit) > 1) {
                [$relationTable, $fieldConditionValue] = $pregSplit;

                $fieldConditionValueArray = $this->pregSplit('@;@', $fieldConditionValue);
                if (count($fieldConditionValueArray) === 3) {
                    [$field, $comparison, $value] = $fieldConditionValueArray;
                    $this->model = $this->model->whereRelation($relationTable, $field, $comparison, $this->optimus()->decode($value));
                } else {

                    [$field, $value] = $fieldConditionValueArray;

                    $this->model = $this->model->with([$relationTable => function ($q) use ($field, $value) {
                        if (Arr::get($this->params, 'isOptimus') === 'false') {
                            return $q->where($field, $value);
                        }
                        return $q->where($field, $this->optimus()->decode($value));
                    }]);
                }
            } else {
                $this->model = $this->model->with($relationship);
            }
        }

        return $this;
    }

    /***
     * Example: relation=itemPrice:price;<;1439664,images:id;1439664
     * Values are Optimus-encoded unless isOptimus=false is set.
     * @return self
     * */ 
    public function relation()
    {
        $relationships = $this->pregSplit('@,@', Arr::get($this->params, 'relation', []));
        foreach ($relationships as $relationship) {
            if (!$relationship) {
                continue;
            }

            [$relationTable, $fieldConditionValue] = $this->pregSplit('@:@', $relationship);
            $fieldConditionValueArray = $this->pregSplit('@;@', $fieldConditionValue);
            if (count($fieldConditionValueArray) === 3) {

                [$field, $comparison, $value] = $fieldConditionValueArray;
                $this->model = $this->model->whereRelation($relationTable, $field, $comparison, $this->optimus()->decode($value));
            } else {

                [$field, $value] = $fieldConditionValueArray;
                $this->model = $this->model->whereRelation($relationTable, $field, $this->optimus()->decode($value));
            }
        }

        return $this;
    }

    //relationNoDecode=itemPrice:price;in;1439664,images:id;>;1439664
    //realfield value
    public function relationNoDecode()
    {
        $relationships = $this->pregSplit('@,@', Arr::get($this->params, 'relationNoDecode', []));
        foreach ($relationships as $relationship) {
            [$relationTable, $fieldConditionValue] = $this->pregSplit('@:@', $relationship);
            $fieldConditionValueArray = $this->pregSplit('@;@', $fieldConditionValue);
            if (count($fieldConditionValueArray) === 3) {
                [$field, $comparison, $value] = $fieldConditionValueArray;
                if ($comparison == 'in') {
                    $explode = explode('*', $value);
                    $this->model = $this->model->whereRelation($relationTable, fn($q) => $q->whereIn($field, $explode));
                } else if ($comparison == 'like') {
                    $this->model = $this->model->whereRelation($relationTable, fn($q) => $q->where($field, 'LIKE', '%' . $value . '%'));
                } else {
                    $this->model = $this->model->whereRelation($relationTable, $field, $comparison, $value);
                }
            } else {

                [$field, $value] = $fieldConditionValueArray;
                $this->model = $this->model->whereRelation($relationTable, $field, $value);
            }
        }

        return $this;
    }

    /**
     * This will also works in many to many relationship
     * whereHas=itemPrice:price;1.2.3,images:id;1.2.3
     * Do not change this it is confirm on 7-21-2024
     * By Bobby Gerez
     */
    public function whereHas()
    {
        $relationships = $this->pregSplit('@,@', Arr::get($this->params, 'whereHas', []));

        foreach ($relationships as $relationship) {
            [$relationTable, $fieldValue] = $this->pregSplit('@:@', $relationship);
            $fieldValueArray = $this->pregSplit('@;@', $fieldValue);
            if (count($fieldValueArray) === 2) {
                [$field, $value] = $fieldValueArray;

                $this->model = $this->model->whereHas(
                    $relationTable,
                    function (Builder $query) use ($field, $value) {
                        $explode = explode('.', $value);
                        $query->whereIn($field, $explode);
                    }
                );
            }
        }

        return $this;
    }

    //Get the associated Class ex. items
    // morph=imageable:field;like;asd
    public function morph()
    {
        [$associated, $queries] = $this->pregSplit('@:@', $this->request->morph);
        $queries = $this->pregSplit('@;@', $queries);
        if (count($queries) === 3) {
            [$field, $operator, $value] = $queries;
            $this->model = $this->model->whereHasMorph(
                $associated,
                $this->associatedClass,
                function (Builder $query) use ($field, $operator, $value) {
                    $query->where($field, $operator, '%' . $value . '%');
                }
            );
        } else {
            [$field, $value] = $queries;
            $this->model = $this->model->whereHasMorph(
                $associated,
                $this->associatedClass,
                function (Builder $query) use ($field, $value) {
                    $query->where($field, $value);
                }
            );
        }
        return $this;
    }



    

    public function update()
    {

        $data = array_intersect_key(
            $this->request->all(),
            array_flip($this->fillable)
        );

        return $this->model->first()->update($data);
    }

    public function delete()
    {
        return $this->model->delete();
    }

    /**
     * Delete records matching multiple where conditions
     * @param array $conditions Associative array of column => value pairs
     * @return int Number of deleted rows
     */
    public function deleteWhere(array $conditions): int
    {
        $query = $this->model;
        foreach ($conditions as $column => $value) {
            $query = $query->where($column, $value);
        }
        return $query->delete();
    }


    public function all()
    {
        return $this->model->all();
    }

    public function noDecodeWhere($field, $condition, $value)
    {
        return $this->model->where($field, $condition, $value);
    }

    public function decodeWhere($field, $value)
    {
        return $this->model->where($field, $this->optimus()->decode($value));
    }

    /**
     * Apply with relations to query
     * @param Builder $query
     */
    private function applyWithToQuery(Builder $query): void
    {
        $with = Arr::get($this->params, 'with', []);
        if (!empty($with)) {
            $withRelations = $this->pregSplit('@,@', $with);
            $query->with($withRelations);
        }
    }

    /**
     * Apply order by to query
     * @param Builder $query
     * @param string $orderBy
     */
    private function applyOrderByToQuery(Builder $query, string $orderBy): void
    {
        [$column, $value] = $this->pregSplit('@:@', $orderBy);
        $query->orderBy($column, $value);
    }

    /**
     * Apply deleted filter to query
     * @param Builder $query
     * @param string|null $deleted
     */
    private function applyDeletedToQuery(Builder $query, ?string $deleted): void
    {
        if ($deleted === 'true') {
            $query->onlyTrashed();
        } elseif ($deleted === 'false') {
            $query->whereNull('deleted_at');
        }
    }

    protected function updatePrimaryImageFromRequest(Request $request): void
    {
        $primaryImageName = $request->input('primaryImageName');
        
        if (!$primaryImageName) {
            return;
        }

        $model = $this->model->first();
        if (!$model) {
            return;
        }

        $model->images()->update(['is_primary' => 0]);
        $model->images()
            ->where('name', $primaryImageName)
            ->update(['is_primary' => 1]);
    }

    protected function deletedFiles(): void
    {
        $request = app()->make('request');
        $deletedFileIds = $request->input('deletedFiles');
        
        if (!$deletedFileIds) {
            return;
        }

        if (!is_array($deletedFileIds)) {
            $deletedFileIds = [$deletedFileIds];
        }

        foreach ($deletedFileIds as $id) {
            $image = Image::find($id);
            if (!$image) {
                continue;
            }

            if (Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            $image->delete();
        }
    }
}
