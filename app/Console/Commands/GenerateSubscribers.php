<?php

namespace App\Console\Commands;

use App\Models\Subscriber;
use Faker\Factory as Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateSubscribers extends Command
{
    protected $signature = 'subscribers:generate {count=100 : Number of subscribers to generate}';

    protected $description = 'Generate random subscribers with German names';

    public function handle(): int
    {
        $count = (int) $this->argument('count');
        $faker = Faker::create('de_DE');

        $this->info("Generating {$count} subscribers...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            $subscribedAt = $faker->dateTimeBetween('-1 year', 'now');
            $isConfirmed = $faker->boolean(80); // 80% are confirmed

            Subscriber::create([
                'uuid' => Str::uuid(),
                'email' => $faker->unique()->safeEmail(),
                'name' => $faker->name(),
                'subscribed_at' => $subscribedAt,
                'confirmed_at' => $isConfirmed ? $faker->dateTimeBetween($subscribedAt, 'now') : null,
                'token' => Str::random(64),
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully created {$count} subscribers.");

        return Command::SUCCESS;
    }
}
