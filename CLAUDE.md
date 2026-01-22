# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 10 e-commerce application (shop.hobel.ch) for selling products with integrated Stripe payments, built with Filament admin panel, Livewire components, and Tailwind CSS. The application is a German/Swiss market shop with multilingual support.

**Note:** This codebase serves as reference for building a modernized Laravel 12 prototype with Filament 4 and Tailwind CSS 4. The new prototype will use a simplified architecture (no product variations, no CMS pages, deferred payment integration).

## Development Commands

### Setup
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### Development
```bash
# Start development server
php artisan serve

# Frontend development (Vite)
npm run dev

# Watch for changes
npm run watch

# Build for production
npm run build
```

### Testing
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run single test file
php artisan test tests/Feature/ExampleTest.php
```

### Code Quality
```bash
# Format code with Laravel Pint
./vendor/bin/pint

# Run specific file
./vendor/bin/pint app/Models/Product.php
```

### Database
```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Create new migration
php artisan make:migration create_table_name
```

### Cache Management
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan optimize
```

## Architecture

### Directory Structure

- **app/Actions/** - Single-purpose action classes organized by domain (Cart, Order, Product, Landing)
  - Follow the pattern: `(new ActionName())->execute($params)`
  - Used to encapsulate business logic outside controllers

- **app/Livewire/** - Real-time interactive components for cart functionality
  - `Cart.php`, `CartButton.php`, `CartIcon.php`, `CartItem.php`, `CartTotal.php`
  - `ProductNotification.php` for product-related notifications

- **app/Filament/** - Admin panel resources and configuration
  - Resources for Products, Orders, ProductCategories, ProductVariations, ContactPage, IdeaPage, LandingPage
  - Admin panel accessible at `/admin`

- **app/Models/** - Eloquent models with key relationships:
  - `Product` - Has many `ProductVariation`, belongs to `ProductCategory`
  - `Order` - Has many `OrderProduct`, belongs to many `Product` through pivot
  - Models use soft deletes and Spatie Sluggable for SEO-friendly URLs

- **app/Http/Middleware/** - Custom middleware for order flow:
  - `EnsureCartIsNotEmpty` - Protects checkout routes
  - `EnsureCorrectOrderStep` - Validates sequential checkout process
  - `EnsureOrderIsPaid` - Validates payment completion

- **app/Notifications/** - Email notifications:
  - `OrderConfirmationNotification` - Sent to customers
  - `OrderInformationNotification` - Sent to shop owners
  - `ProductNotification` - Product-related alerts

- **app/Services/Pdf/** - PDF generation services using DomPDF (for invoices)

### Key Patterns

**Session-Based Cart System**
- Cart data stored in session, managed through Action classes
- Cart structure includes: items, invoice_address, shipping_address, payment_method, order_step, is_paid
- Cart operations: `GetCart`, `StoreCart`, `UpdateCart`, `DestroyCart`

**Multi-Step Checkout Flow**
1. Overview → 2. Invoice Address → 3. Shipping Address → 4. Payment Method → 5. Summary → 6. Stripe Payment → 7. Confirmation
- Protected by `ensure.cart.not.empty` and `ensure.correct.order.step` middleware
- Step validation in `OrderController::handleStep()`

**Product System**
- Products have a `state` enum (ProductState): available, not_available, on_request
- Products support variations through `ProductVariation` model
- Use Spatie Sluggable for URL routing (route key: `slug`)
- Products have `group_title` (for grouping variations) and individual `title`

**Payment Integration**
- Stripe Checkout integration in `OrderController::finalize()`
- Success/cancel webhook routes configured
- Payment confirmation triggers `HandleOrder` action to persist order

**Image Management**
- Custom image caching system via `ImageController`
- Images served through `/img/{template}/{filename}` route
- Templates defined in `app/Filters/Image/Template/` (Small, Medium, Large)
- Uses Intervention Image v2 (not compatible with Laravel 12 - will need alternative for new prototype)

### Database Schema

**Key Tables:**
- `products` - Main product catalog with category relationship
- `product_variations` - Product variants (size, color, etc.)
- `product_categories` - Product categorization
- `orders` - Customer orders with invoice/shipping addresses
- `order_product` - Pivot table linking orders to products with price snapshot
- `landing_page`, `idea_page`, `contact_page` - CMS-managed pages

**Important Fields:**
- Products: `uuid`, `slug`, `state`, `publish`, `attributes` (JSON), `cards` (JSON)
- Orders: `uuid`, `payed_at`, `use_invoice_address`, `payment_method`
- Both use soft deletes (`deleted_at`)

### Third-Party Integrations

- **Filament v3.2** - Admin panel framework
- **Livewire v3.4** - Real-time components
- **Stripe PHP** - Payment processing
- **Laravel Scout + Algolia** - Product search (configured but check implementation)
- **Spatie Packages**:
  - `laravel-sluggable` - SEO-friendly URLs
  - `laravel-honeypot` - Form spam protection
  - `laravel-ray` - Debugging tool
- **Intervention Image v2** - Image manipulation and caching (not compatible with Laravel 12)

### Frontend Stack

- **Vite** - Asset bundler (replaces Laravel Mix)
- **Tailwind CSS** - Utility-first CSS framework
- **Alpine.js** - Lightweight JavaScript framework
- **Swiper** - Carousel/slider library
- Views in `resources/views/` using Blade templates

## Important Notes

- This is a **Swiss market** shop - currency is CHF, language is German
- Order numbers use format: `FS-{6-digit-padded-id}` (e.g., FS-000042)
- Shipping validation checks if country is in `config('countries.delivery')`
- Environment requires Stripe keys: `PAYMENT_STRIPE_PRIVATE_KEY`
- Session-based cart means no persistent cart for logged-out users
- Admin panel uses Filament - customize in `app/Providers/Filament/AdminPanelProvider.php`

## Modernization Notes (for Laravel 12 Prototype)

When building the new prototype, consider:
- **Image handling**: Intervention Image v2 is not Laravel 12 compatible. Research alternatives: Intervention Image v3, Spatie Media Library, or Laravel's built-in image manipulation
- **Simplified database**: Single products table (no variations), no CMS pages
- **Payment integration**: Deferred to later stage (no Stripe integration initially)
- **Checkout flow**: Simplified to 3-4 steps instead of 7
- **Removed for prototype**: Algolia/Scout search, soft deletes, PDF generation, email notifications
