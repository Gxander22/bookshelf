<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class ManagerSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'manager@bookshelf.test';

        if (User::where('email', $email)->exists()) {
            $this->command->info('Manager account already exists.');

            return;
        }

        // Prompt without displaying the password in the terminal.
        $password = $this->command->secret(
            'Choose a manager password of at least 12 characters'
        );

        if (! is_string($password) || strlen($password) < 12) {
            throw new RuntimeException(
                'Password must contain at least 12 characters.'
            );
        }

        User::create([
            'name' => 'BookShelf Manager',
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->command->info("Account created: {$email}");
    }
}
