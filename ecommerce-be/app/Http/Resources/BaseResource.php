<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BaseResource extends JsonResource
{
    public function toArray($request): array
    {
        if ($this->resource instanceof LengthAwarePaginator) {
            return $this->getPaginate();
        }

        if ($this->resource instanceof Collection) {
            return $this->filterCollection($request);
        }

        return parent::toArray($request);
    }

    private function filterCollection($request): array
    {
        if (!$request->only) {
            return $this->resource->toArray();
        }

        $fields = explode(',', $request->only);
        return $this->resource->map(fn ($item) => $item->only($fields))->toArray();
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

    private function processItems($items): array
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