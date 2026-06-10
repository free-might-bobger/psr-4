<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ShowResource extends JsonResource
{
    protected array $fields = [];

    public function __construct(Model $resource)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        if (!empty($this->fields)) {
            return $this->filterByFields();
        }

        return parent::toArray($request);
    }

    private function filterByFields(): array
    {
        return $this->resource->only($this->fields);
    }
}
