<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ImageController extends Controller
{
    /**
     * Display the image upload page.
     */
    public function index(): View
    {
        $uploadedImage = null;
        if (session('uploaded_key')) {
            $uploadedImage = Image::where('unique_key', session('uploaded_key'))->first();
        } elseif (session('uploaded_image')) {
            $imgData = session('uploaded_image');
            if ($imgData instanceof Image) {
                $uploadedImage = $imgData;
            } elseif (is_array($imgData) && isset($imgData['unique_key'])) {
                $uploadedImage = Image::where('unique_key', $imgData['unique_key'])->first();
            }
        }

        return view('images.index', compact('uploadedImage'));
    }

    /**
     * Handle the image upload process.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ], [
            'image.required' => 'Please select an image file to upload.',
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'Only JPG, JPEG, PNG, and WebP images are allowed.',
            'image.max' => 'The image size cannot exceed 10 MB.',
        ]);

        $file = $request->file('image');

        // Generate a unique 8-character key
        do {
            $uniqueKey = Str::random(8);
        } while (Image::where('unique_key', $uniqueKey)->exists());

        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $fileName = $uniqueKey.'.'.strtolower($extension);

        // Store in public disk under 'images' folder (storage/app/public/images/)
        $filePath = $file->storeAs('images', $fileName, 'public');

        $image = Image::create([
            'unique_key' => $uniqueKey,
            'original_name' => $originalName,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'mime_type' => $file->getMimeType() ?? 'image/'.$extension,
            'file_size' => $file->getSize(),
            'views' => 0,
        ]);

        return redirect()->route('images.index')
            ->with('success', 'Image uploaded successfully!')
            ->with('uploaded_key', $image->unique_key)
            ->with('uploaded_image', $image);
    }

    /**
     * Display the uploaded image and increment view count.
     */
    public function show(string $key): View
    {
        $image = Image::where('unique_key', $key)->firstOrFail();

        // Increment view count
        $image->increment('views');

        return view('images.show', compact('image'));
    }
}
