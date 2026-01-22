<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetupApplication extends Command
{
    protected $signature = 'app:setup
                            {--skip-products : Skip importing products}
                            {--skip-subscribers : Skip generating subscribers}
                            {--subscribers=100 : Number of subscribers to generate}';

    protected $description = 'Fresh setup: drop database, run migrations, create admin user, seed categories, import products, generate subscribers';

    public function handle(): int
    {
        $this->info('Starting application setup...');
        $this->newLine();

        // Step 1: Fresh migration (drops all tables and runs migrations)
        $this->warn('Step 1: Dropping database and running migrations...');
        $this->call('migrate:fresh', ['--force' => true]);
        $this->info('Migrations completed.');
        $this->newLine();

        // Step 2: Create admin user
        $this->warn('Step 2: Creating admin user...');
        $this->createAdminUser();
        $this->newLine();

        // Step 3: Seed categories
        $this->warn('Step 3: Seeding categories...');
        $this->call('db:seed', ['--class' => 'CategorySeeder', '--force' => true]);
        $this->info('Categories seeded.');
        $this->newLine();

        // Step 4: Import products
        if (! $this->option('skip-products')) {
            $this->warn('Step 4: Importing products...');
            $this->call('import:products');
            $this->newLine();
        } else {
            $this->info('Step 4: Skipping product import.');
            $this->newLine();
        }

        // Step 5: Generate subscribers
        if (! $this->option('skip-subscribers')) {
            $this->warn('Step 5: Generating subscribers...');
            $this->call('subscribers:generate', [
                'count' => $this->option('subscribers'),
            ]);
            $this->newLine();
        } else {
            $this->info('Step 5: Skipping subscriber generation.');
            $this->newLine();
        }

        // Summary
        $this->newLine();
        $this->info('========================================');
        $this->info('Application setup complete!');
        $this->info('========================================');
        $this->newLine();
        $this->table(
            ['Setting', 'Value'],
            [
                ['Admin Email', 'm@marceli.to'],
                ['Admin Password', '7aq31rr23'],
                ['Admin Panel', '/admin'],
            ]
        );

        return self::SUCCESS;
    }

    private function createAdminUser(): void
    {
        $user = User::create([
            'name' => 'Marcel Stadelmann',
            'email' => 'm@marceli.to',
            'password' => Hash::make('7aq31rr23'),
        ]);

        $user->email_verified_at = now();
        $user->save();

        $this->info("Admin user created: {$user->name} ({$user->email})");
    }
}
