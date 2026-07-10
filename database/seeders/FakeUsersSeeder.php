<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class FakeUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1, 5) as $i) {
            $email = 'fakeuser'.uniqid().'@test.sn';

            $user = User::create([
                'role' => 'patient',
                'genre' => 'femme',
                'nom' => 'Test',
                'prenom' => 'User'.$i,
                'email' => $email,
                'password' => 'password',
                'telephone' => '000000000',
                'date_naissance' => now()->subYears(25)->toDateString(),
                'ville' => 'Dakar',
            ]);

            event(new Registered($user));
        }
    }
}
