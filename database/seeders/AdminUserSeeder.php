<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Seeds the panel administrator.
     *
     * The password is never written into source control: it comes from
     * PORTFOLIO_ADMIN_PASSWORD in .env, which is gitignored. On a fresh machine,
     * set that variable before seeding. The email must also appear in
     * config/portfolio.php `admin_emails`, or the account cannot reach the panel.
     */
    public function run(): void
    {
        $email = config('portfolio.admin_emails')[0] ?? null;
        $password = config('portfolio.admin_password');

        if (blank($email)) {
            $this->command?->warn('No admin email configured — skipping AdminUserSeeder.');

            return;
        }

        if (blank($password)) {
            $this->command?->warn('PORTFOLIO_ADMIN_PASSWORD is not set — skipping AdminUserSeeder.');

            return;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => config('portfolio.admin_name', 'Site administrator'),
                'password' => $password, // hashed by the model's `hashed` cast
                'email_verified_at' => now(),
            ],
        );

        $this->command?->info("Admin ready: {$user->email}");
    }
}
