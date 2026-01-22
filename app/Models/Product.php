<?php

namespace App\Models;

use App\Enums\ProductType;
use App\Traits\HasGermanSlug;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use HasGermanSlug;
    use HasSlug;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'parent_id',
        'type',
        'title',
        'slug',
        'short_description',
        'label',
        'description',
        'sku',
        'delivery_time',
        'price',
        'stock',
        'order',
        'published',
    ];

    protected $casts = [
        'type' => ProductType::class,
        'price' => 'decimal:2',
        'published' => 'boolean',
    ];

    /**
     * Check if product is of type simple.
     */
    protected function isSimple(): Attribute
    {
        return Attribute::get(fn () => $this->type === ProductType::Simple);
    }

    /**
     * Check if product has children (is a parent with variations).
     */
    protected function isVariations(): Attribute
    {
        return Attribute::get(fn () => $this->children()->exists());
    }

    /**
     * Check if product is a child (variation).
     */
    protected function isChild(): Attribute
    {
        return Attribute::get(fn () => $this->parent_id !== null);
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Categories that belong to this product.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Get all images for this product.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('order');
    }

    /**
     * Get the preview image (for category views).
     */
    public function previewImage()
    {
        return $this->morphOne(Image::class, 'imageable')->ofMany([
            'preview' => 'MAX', // 1 (true) will come before 0 (false)
            'id' => 'MIN', // Tie-breaker: if no preview, pick the oldest/first uploaded
        ]);
    }

    /**
     * Shipping methods available for this product.
     */
    public function shippingMethods(): BelongsToMany
    {
        return $this->belongsToMany(ShippingMethod::class, 'product_shipping_method')
            ->withPivot('price')
            ->withTimestamps()
            ->orderBy('order');
    }

    /**
     * Parent product (for child products).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    /**
     * Child products (variations).
     */
    public function children(): HasMany
    {
        return $this->hasMany(Product::class, 'parent_id')->orderBy('order');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->uuid)) {
                $product->uuid = (string) Str::uuid();
            }
        });
    }
}
