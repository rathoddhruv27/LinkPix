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
     * Handle the media (image/video) upload process.
     */
    public function store(Request $request): RedirectResponse
    {
        // Fallback input field detection: support 'image', 'media', or 'file'
        $fieldKey = 'image';
        if (! $request->hasFile('image')) {
            if ($request->hasFile('media')) {
                $fieldKey = 'media';
            } elseif ($request->hasFile('file')) {
                $fieldKey = 'file';
            }
        }

        $request->validate([
            $fieldKey => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif,mp4,webm,ogg,mov', 'max:51200'],
        ], [
            "{$fieldKey}.required" => 'Please select an image or video file to upload.',
            "{$fieldKey}.file" => 'The uploaded file must be a valid media file.',
            "{$fieldKey}.mimes" => 'Allowed formats: JPG, JPEG, PNG, WebP, GIF, MP4, WebM, OGG, and MOV.',
            "{$fieldKey}.max" => 'The media file size cannot exceed 50 MB.',
        ]);

        $file = $request->file($fieldKey);

        // Generate a unique 8-character key
        do {
            $uniqueKey = Str::random(8);
        } while (Image::where('unique_key', $uniqueKey)->exists());

        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $fileName = $uniqueKey.'.'.$extension;

        // Store in public disk under 'images' folder (storage/app/public/images/)
        $filePath = $file->storeAs('images', $fileName, 'public');

        $mimeType = $file->getMimeType();
        if (! $mimeType) {
            $mimeType = in_array($extension, ['mp4', 'webm', 'ogg', 'mov']) ? 'video/'.$extension : 'image/'.$extension;
        }

        $image = Image::create([
            'unique_key' => $uniqueKey,
            'original_name' => $originalName,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
            'views' => 0,
        ]);

        $mediaLabel = $image->is_video ? 'Video' : 'Image';

        return redirect()->route('images.index')
            ->with('success', "{$mediaLabel} uploaded successfully!")
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
