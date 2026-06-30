<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\Store;
use App\Models\Image;
use Intervention\Image\Facades\Image as ImageFacade;

class ApplyStoreService
{

    public function create(Request $request): Store
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:255',
            'desc' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $store = Store::create([
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'desc' => $validated['desc'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'user_id' => auth()->id(),
            'is_active' => false,
        ]);

        $this->attachImages($store, $request);

        return $store->load('images');
    }

    private function attachImages(Store $store, Request $request): void
    {
        if (!$request->hasFile('images')) {
            return;
        }

        $files = $request->file('images');
        if (!is_array($files)) {
            $files = [$files];
        }

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
                'is_primary' => true,
                'size' => filesize(public_path($filePath)),
            ]);

            $store->images()->save($image);
        }
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
}
