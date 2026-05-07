<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;

class PassportClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clientRepository = app(ClientRepository::class);

        // Create Personal Access Client
        $clientRepository->createPersonalAccessClient(
            null, // User ID (null for no user)
            'Personal Access Client', // Client Name
            config('app.url') // Redirect URL
        );

        // Create Password Grant Client
        $clientRepository->createPasswordGrantClient(
            null, // User ID (null for no user)
            'Password Grant Client', // Client Name
            config('app.url') // Redirect URL
        );
    }
}
