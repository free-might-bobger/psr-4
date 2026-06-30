<?php

namespace App\Repositories;

use App\Models\Item;
use App\Repositories\BaseRepository;
use App\Traits\RoleTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use App\Repositories\Support\ColumnValueCriteria;
use App\Models\Image;
use Intervention\Image\Facades\Image as ImageFacade;
use App\Constants\Config;
class ItemRepository extends BaseRepository
{
    use RoleTrait, ColumnValueCriteria;

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

        // Sanitize description to prevent XSS attacks
        if (isset($data['description'])) {
            $data['description'] = $this->sanitizeHtml($data['description']);
        }

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
            return $item->store && $item->store->distance <= Config::MAX_DISTANCE;
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
            $filePath = 'images/uploads/' . $fileName;

            // Optimize image before saving
            $optimizedImage = $this->optimizeImage($file);
            $optimizedImage->save(public_path($filePath), 85); // 85% quality

            $image = new Image([
                'thumbnail' => $filePath,
                'path' => $filePath,
                'name' => $originalName,
                'is_primary' => $request->input('primaryImageName') === $originalName,
                'size' => filesize(public_path($filePath))
            ]);

            $this->model->images()->save($image);
        }

        $this->updatePrimaryImageFromRequest($request);
    }

    private function optimizeImage($file)
    {
        $image = ImageFacade::make($file->getPathname());

        // Get original dimensions
        $width = $image->width();
        $height = $image->height();

        // Set maximum dimensions (adjust as needed)
        $maxWidth = 1920;
        $maxHeight = 1080;

        // Only resize if image is larger than maximum dimensions
        if ($width > $maxWidth || $height > $maxHeight) {
            $image->resize($maxWidth, $maxHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        // Convert to JPEG for better compression (if not already JPEG)
        if ($file->getClientOriginalExtension() !== 'jpg' && $file->getClientOriginalExtension() !== 'jpeg') {
            $image->encode('jpg', 85);
        }

        return $image;
    }

    /**
     * Sanitize HTML to prevent XSS attacks
     * @param string $html
     * @return string
     */
    private function sanitizeHtml(string $html): string
    {
        if (empty($html)) {
            return $html;
        }

        // Remove dangerous tags
        $dangerousTags = ['script', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'style', 'meta', 'link'];
        foreach ($dangerousTags as $tag) {
            $html = preg_replace('#</?' . $tag . '[^>]*>#is', '', $html);
        }

        // Remove dangerous event handlers
        $dangerousEvents = ['onclick', 'onload', 'onerror', 'onmouseover', 'onmouseout', 'onfocus', 'onblur', 'onchange', 'onsubmit'];
        foreach ($dangerousEvents as $event) {
            $html = preg_replace('#\s' . $event . '\s*=\s*["\'][^"\']*["\']#is', '', $html);
            $html = preg_replace('#\s' . $event . '\s*=\s*[^>\s]*#is', '', $html);
        }

        // Remove javascript: protocol from href and src attributes
        $html = preg_replace('#(href|src)\s*=\s*["\']\s*javascript:#is', '$1=""', $html);
        $html = preg_replace('#(href|src)\s*=\s*javascript:#is', '$1=""', $html);

        // Remove data: protocol from src attributes (can be used for XSS)
        $html = preg_replace('#src\s*=\s*["\']\s*data:#is', 'src=""', $html);

        // Remove any on* attributes
        $html = preg_replace('#\son[a-z]+\s*=\s*["\'][^"\']*["\']#is', '', $html);
        $html = preg_replace('#\son[a-z]+\s*=\s*[^>\s]*#is', '', $html);

        return $html;
    }


}
