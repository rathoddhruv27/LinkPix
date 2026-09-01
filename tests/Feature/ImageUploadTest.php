<?php

namespace Tests\Feature;

use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_page_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('LinkPix 3D');
        $response->assertSee('Upload & Share Media', false);
    }

    public function test_user_can_upload_an_image(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('cat.jpg', 600, 600);

        $response = $this->post('/upload', [
            'image' => $file,
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success');
        $response->assertSessionHas('uploaded_image');

        $image = Image::first();
        $this->assertNotNull($image);
        $this->assertEquals('cat.jpg', $image->original_name);
        $this->assertTrue($image->is_image);
        $this->assertFalse($image->is_video);
        $this->assertEquals('image', $image->media_type);
        $this->assertEquals(0, $image->views);
        $this->assertEquals('images/'.$image->file_name, $image->file_path);

        Storage::disk('public')->assertExists('images/'.$image->file_name);
    }

    public function test_user_can_upload_a_video(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('intro.mp4', 5000, 'video/mp4');

        $response = $this->post('/upload', [
            'image' => $file,
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success');

        $media = Image::first();
        $this->assertNotNull($media);
        $this->assertEquals('intro.mp4', $media->original_name);
        $this->assertTrue($media->is_video);
        $this->assertFalse($media->is_image);
        $this->assertEquals('video', $media->media_type);

        Storage::disk('public')->assertExists('images/'.$media->file_name);
    }

    public function test_validation_rejects_invalid_file_type(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->post('/upload', [
            'image' => $file,
        ]);

        $response->assertSessionHasErrors(['image']);
        $this->assertEquals(0, Image::count());
    }

    public function test_user_can_view_uploaded_image_and_increment_view_count(): void
    {
        Storage::fake('public');

        $image = Image::create([
            'unique_key' => 'X8k92LmP',
            'original_name' => 'cat.jpg',
            'file_name' => 'X8k92LmP.jpg',
            'file_path' => 'images/X8k92LmP.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 245678,
            'views' => 0,
        ]);

        $response = $this->get('/image/'.$image->unique_key);

        $response->assertStatus(200);
        $response->assertSee('cat.jpg');
        $response->assertSee('X8k92LmP');
        $response->assertSee('1'); // view count incremented

        $this->assertEquals(1, $image->fresh()->views);
    }
}
