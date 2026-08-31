<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'unique_key',
        'original_name',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'views',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'views' => 'integer',
    ];

    /**
     * Get the shareable URL for the image.
     */
    public function getShareUrlAttribute(): string
    {
        return url('/image/'.$this->unique_key);
    }

    /**
     * Get the direct public URL for the stored file.
     */
    public function getStorageUrlAttribute(): string
    {
        return asset('storage/'.$this->file_path);
    }

    /**
     * Get human readable file size.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' B';
    }
}
