<?php

namespace App\Repositories;

use App\Models\Item;
use App\Repositories\BaseRepository;
use App\Traits\UtilsTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use App\Repositories\Support\SearchFieldSupport;
use Illuminate\Support\Facades\File;
use App\Models\Image;

class ItemRepository extends BaseRepository
{
    use UtilsTrait, SearchFieldSupport;

    protected Collection $collection;

    public function __construct()
    {
        $this->setModel(new Item);
        $this->cacheKey = 'items-get';
        $this->collection = new Collection();
    }

    public function category_id(int $value) : void
    {   
        $this->model = $this->model->where('category_id', $value);
    }

    public function itemUpdateWithImage(int $id, array $params): Item
    {
        /**get fillable should be before accessing the model */
        $this->setFillable();
        $this->model = $this->findOrFail($id);
        $data = array_intersect_key(
            $params,
            array_flip($this->fillable)
        );
        unset($data['store_id']);
        $this->filesUpload();
        $this->model->update($data);
        return $this->model->fresh();

    }

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
        
        $this->collection = $this->model->get()
        ->filter(function ($item) {
            return $item->store && $item->store->distance <= 30;
        })
        ->sortBy(function ($item) {
            return $item->store->distance ?? PHP_INT_MAX;
        })
        ->values();
        return $this;
    }

        /**
        * Get the resource collection
        * @return Collection
        */  
    public function getCollection(): Collection
    {        
        return $this->collection;
    }

    public function filesUpload(): void
    {
        $request = app()->make('request');

        if (!$request->hasFile('images')) {
            return;
        }

        $files = $request->file('images');
        if (!is_array($files)) {
            $files = [$files];
        }

        // Reset all images to non-primary
        $this->model->images()->update(['is_primary' => 0]);

        foreach ($files as $file) {
            if (!$file->isValid()) {
                continue;
            }

            $originalName = $file->getClientOriginalName();
            $fileName = uniqid() . '-' . $originalName;
            $fileContent = file_get_contents($file->getPathname());
            $filePath = 'images/uploads/' . $fileName;

            File::put(public_path($filePath), $fileContent);

            $image = new Image([
                'thumbnail' => $filePath,
                'path' => $filePath,
                'name' => $originalName,
                'is_primary' => $request->input('primaryImageName') === $originalName,
                'size' => $file->getSize()
            ]);

            $this->model->images()->save($image);
        }

        $this->updatePrimaryImageFromRequest($request);
    }


}
