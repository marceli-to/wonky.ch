<?php

namespace App\Console\Commands;

use App\Enums\ProductType;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products
                            {--fresh : Delete all existing products and images before import}
                            {--skip-images : Skip copying images (only import product data)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from the JSON export file in storage/app/import';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $importPath = storage_path('app/import');
        $jsonFile = $importPath.'/products.json';
        $imagesDir = $importPath.'/images';

        // Validate import files exist
        if (! File::exists($jsonFile)) {
            $this->error("Import file not found: {$jsonFile}");

            return self::FAILURE;
        }

        // Load and decode JSON
        $json = File::get($jsonFile);
        $products = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Failed to parse products.json: '.json_last_error_msg());

            return self::FAILURE;
        }

        $this->info('Found '.count($products).' products to import.');

        // Handle fresh import
        if ($this->option('fresh')) {
            if ($this->confirm('This will delete all existing products and images. Continue?', false)) {
                $this->warn('Deleting existing products and images...');
                Image::query()->where('imageable_type', Product::class)->delete();
                Product::query()->forceDelete();

                // Clean up product images directory
                Storage::disk('public')->deleteDirectory('products');

                $this->info('Existing data cleared.');
            } else {
                $this->info('Operation cancelled.');

                return self::SUCCESS;
            }
        }

        // Create products directory in public storage
        Storage::disk('public')->makeDirectory('products');

        $bar = $this->output->createProgressBar(count($products));
        $bar->start();

        $imported = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($products as $index => $data) {
                try {
                    // Check if product already exists
                    if (Product::where('slug', $data['slug'])->exists()) {
                        $skipped++;
                        $bar->advance();

                        continue;
                    }

                    // Parse price (handle formats like "CHF50.00" or "CHF50.00 - CHF130.00")
                    $price = $this->parsePrice($data['price']);

                    // Create product
                    $product = Product::create([
                        'type' => ProductType::Simple,
                        'title' => $data['title'],
                        'slug' => $data['slug'],
                        'description' => $data['description'] ?: null,
                        'price' => $price,
                        'stock' => 99,
                        'order' => $index,
                        'published' => true,
                    ]);

                    // Assign random categories (1-3)
                    $categoryIds = Category::pluck('id')->toArray();
                    if (! empty($categoryIds)) {
                        $randomCount = rand(1, min(3, count($categoryIds)));
                        $randomCategoryIds = array_rand(array_flip($categoryIds), $randomCount);
                        $product->categories()->attach((array) $randomCategoryIds);
                    }

                    // Import images if not skipped
                    if (! $this->option('skip-images') && ! empty($data['local_images'])) {
                        $this->importImages($product, $data['local_images'], $imagesDir);
                    }

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Product '{$data['title']}': ".$e->getMessage();
                }

                $bar->advance();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->newLine();
            $this->error('Import failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('Import complete!');
        $this->table(
            ['Status', 'Count'],
            [
                ['Imported', $imported],
                ['Skipped (already exists)', $skipped],
                ['Errors', count($errors)],
            ]
        );

        // Show errors if any
        if (! empty($errors)) {
            $this->newLine();
            $this->warn('Errors encountered:');
            foreach ($errors as $error) {
                $this->line("  - {$error}");
            }
        }

        return self::SUCCESS;
    }

    /**
     * Parse price string to decimal value.
     * Handles formats like "CHF50.00" or "CHF50.00 - CHF130.00" (takes first price).
     */
    private function parsePrice(string $priceString): float
    {
        // Remove currency prefix and trim
        $price = preg_replace('/^CHF\s*/i', '', $priceString);

        // If it's a range, take the first price
        if (str_contains($price, '-')) {
            $price = trim(explode('-', $price)[0]);
        }

        // Remove any remaining non-numeric characters except decimal point
        $price = preg_replace('/[^0-9.]/', '', $price);

        return (float) $price;
    }

    /**
     * Import images for a product.
     */
    private function importImages(Product $product, array $localImages, string $imagesDir): void
    {
        foreach ($localImages as $index => $filename) {
            $sourcePath = $imagesDir.'/'.$filename;

            if (! File::exists($sourcePath)) {
                continue;
            }

            // Generate destination path
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $newFilename = $product->slug.'_'.($index + 1).'.'.$extension;
            $destinationPath = 'products/'.$newFilename;

            // Copy file to public storage
            Storage::disk('public')->put(
                $destinationPath,
                File::get($sourcePath)
            );

            // Get image dimensions
            $imageInfo = @getimagesize($sourcePath);
            $width = $imageInfo[0] ?? null;
            $height = $imageInfo[1] ?? null;

            // Get mime type
            $mimeType = File::mimeType($sourcePath);

            // Create image record
            Image::create([
                'imageable_type' => Product::class,
                'imageable_id' => $product->id,
                'file_name' => $newFilename,
                'file_path' => $destinationPath,
                'mime_type' => $mimeType,
                'size' => File::size($sourcePath),
                'width' => $width,
                'height' => $height,
                'order' => $index,
                'preview' => $index === 0, // First image is preview
            ]);
        }
    }
}
