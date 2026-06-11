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
        $attributes = $item->only($this->fields);
        $loadedRelations = $item->getRelations();
        foreach ($this->fields as $field) {
            $camel = \Illuminate\Support\Str::camel($field);
            if (array_key_exists($camel, $loadedRelations)) {
                $attributes[$field] = $loadedRelations[$camel];
            } elseif (array_key_exists($field, $loadedRelations)) {
                $attributes[$field] = $loadedRelations[$field];
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