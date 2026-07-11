<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Administrateur;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SimpleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@yoonumakk.sn'],
            [
                'role'              => 'admin',
                'nom'               => 'Diallo',
                'prenom'            => 'Aminata',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        Administrateur::updateOrCreate(['user_id' => $admin->id]);
    }
}
