<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if ($email = config('app.admin_email')) {
            User::updateOrCreate(['email' => $email], [
                'name' => 'Calone Perfume',
                'password' => env('ADMIN_PASSWORD') ?: 'change-me-please',
                'is_admin' => true,
            ]);
        }

        $this->call([ContentSeeder::class, InstagramSeeder::class]);
    }
}
