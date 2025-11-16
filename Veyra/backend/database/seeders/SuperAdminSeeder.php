<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // On évite les doublons si on relance le seeder
        if (User::where('email', 'admin@veyra.com')->exists()) {
            return;
        }

        User::create([
            // Auth
            'email' => 'admin@veyra.com',
            'password' => Hash::make('adminadmin'),
            'email_verified' => true,
            'email_verification_token' => null,

            // Company (valeurs fictives)
            'company_name' => 'Veyra Admin',
            'logo_url' => null,
            'tva_number' => 'ADMIN-TVA',

            // Contact
            'first_name' => 'Admin',
            'last_name' => 'Veyra',
            'function' => 'Super Admin',

            // Adresse (fictif)
            'address1' => 'Adresse admin',
            'address2' => null,
            'postal_code' => '00000',
            'country' => 'France',

            // Business
            'sector' => 'Autres',
            'sector_other' => 'Administration Veyra',

            // Partenaires
            'partner' => null,
            'partner_other' => null,

            // Préférences
            'language' => 'fr',

            // Tracking
            'ip_signup' => '127.0.0.1',
            'signup_at' => now(),

            // Admin & statut
            'user_type' => 'ADMIN',
            'status'    => 'approved',
            'dpp_access' => 2,
            'quota_dpp' => 0,
        ]);
    }
}
