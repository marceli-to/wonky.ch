<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    protected $fillable = [
        'imageable_type',
        'imageable_id',
        'file_name',
        'file_path',
        'mime_type',
        'size',
        'width',
        'height',
        'caption',
        'order',
        'preview',
    ];

    protected $casts = [
        'preview' => 'boolean',
    ];

    /**
     * Get the parent imageable model (Product, Category, etc.).
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the full URL to the image file.
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Delete the file when the model is deleted.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($image) {
            if (Storage::exists($image->file_path)) {
                Storage::delete($image->file_path);
            }
        });
    }
}
