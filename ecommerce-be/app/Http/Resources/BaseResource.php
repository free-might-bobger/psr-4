<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class BaseResource extends JsonResource
{
    protected array $fields = [];

    public function __construct(Collection|LengthAwarePaginator|Model $resource)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        if (!empty($this->fields)) {
            return $this->filterByFields();
        }

        if ($this->resource instanceof LengthAwarePaginator) {
            return $this->getPaginate();
        }

        if ($this->resource instanceof Collection) {
            return $this->filterCollection($request);
        }

        return parent::toArray($request);
    }

    private function filterByFields(): array
    {
        if ($this->resource instanceof LengthAwarePaginator) {
            return $this->getPaginateWithFields();
        }

        if ($this->resource instanceof Collection) {
            return $this->resource->map(fn ($item) => $this->extractFields($item))->toArray();
        }

        return $this->extractFields($this->resource);
    }

    private function getPaginateWithFields(): array
    {
        $items = collect($this->items())->map(fn ($item) => $this->extractFields($item))->toArray();

        return [
            'data' => $items,
            'meta' => $this->getPaginationMeta(),
        ];
    }

    private function filterCollection(Request $request): array
    {
        if (!$request->only) {
            return $this->resource->toArray();
        }

        $fields = explode(',', $request->only);
        $items = $this->resource instanceof Collection ? $this->resource : collect($this->resource);
        return $items->map(fn ($item) => $item->only($fields))->toArray();
    }

    private function extractFields(Model $item): array
    {
        $attributes = [];
        $loadedRelations = $item->getRelations();

        foreach ($this->fields as $field) {
            // Handle dot notation (e.g., "store.id" => store: {id: ...})
            if (str_contains($field, '.')) {
                [$relation, $nestedField] = explode('.', $field, 2);
                $camelRelation = \Illuminate\Support\Str::camel($relation);

                // Get relation data from loaded relations
                if (array_key_exists($camelRelation, $loadedRelations)) {
                    $relationData = $loadedRelations[$camelRelation];
                } elseif (array_key_exists($relation, $loadedRelations)) {
                    $relationData = $loadedRelations[$relation];
                } else {
                    continue;
                }

                // Build nested structure
                if (!isset($attributes[$relation])) {
                    $attributes[$relation] = [];
                }

                // Handle collection relations (hasMany) vs single model (hasOne/belongsTo)
                if ($relationData instanceof Collection) {
                    $attributes[$relation] = $relationData->map(
                        fn ($r) => $r->only(explode('.', $nestedField))
                    )->toArray();
                } elseif ($relationData instanceof Model) {
                    $attributes[$relation][$nestedField] = $relationData->{$nestedField} ?? null;
                }
            } else {
                // Plain attribute field
                $camel = \Illuminate\Support\Str::camel($field);
                if (array_key_exists($camel, $loadedRelations)) {
                    $attributes[$field] = $loadedRelations[$camel];
                } elseif (array_key_exists($field, $loadedRelations)) {
                    $attributes[$field] = $loadedRelations[$field];
                } else {
                    $attributes[$field] = $item->{$field} ?? null;
                }
            }
        }

        return $attributes;
    }

    public function getTo(): int
    {
        $to = $this->perPage() * $this->currentPage();
        return $to >= $this->total() ? $this->total() : $to;
    }

    public function getPaginate(): array
    {
        $items = $this->items();
        $items = $this->processItems($items);

        return [
            'data' => $items,
            'meta' => $this->getPaginationMeta(),
        ];
    }

    private function processItems(array $items): array
    {
        if (method_exists($this, 'hasStoreAdvertisement')) {
            return $this->hasStoreAdvertisement();
        }

        if (method_exists($this, 'makeVisibleFields')) {
            return $this->makeVisibleFields();
        }

        return $items;
    }

    private function getPaginationMeta(): array
    {
        $perPage = $this->perPage();
        $currentPage = $this->currentPage();

        return [
            'per_page' => $perPage,
            'current_page' => $currentPage,
            'total' => $this->total(),
            'last_page' => $this->lastPage(),
            'to' => $this->getTo(),
            'from' => ($perPage * $currentPage) - $perPage + 1,
        ];
    }

    
}