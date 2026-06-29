<?php

namespace Database\Factories;

use App\Models\DemandeAdhesion;
use App\Models\Gynecologue;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class GynecologueFactory extends Factory
{
    protected $model = Gynecologue::class;

    public function definition(): array
    {
        $villes = ['Dakar', 'Thiès', 'Saint-Louis', 'Mbour', 'Kaolack', 'Ziguinchor', 'Louga'];
        $structures = [
            'Clinique du Cap',
            'Hôpital Principal',
            'Hôpital Dalal Jamm',
            'Centre de Santé Philippe Senghor',
            'Clinique de la Madeleine',
            'Cabinet Médical Yoff'
        ];

        $nom = $this->faker->lastName();
        $prenom = $this->faker->firstName('female');

        return [
            'nom'               => $nom,
            'prenom'            => $prenom,
            'email'             => strtolower($prenom . '.' . $nom) . '@test.sn',
            'password'          => Hash::make('password'),
            'telephone'         => '77' . $this->faker->numerify('#######'),
            'numero_ordre'      => 'OM-' . $this->faker->unique()->numberBetween(2000, 3000),
            'specialite'        => 'Gynécologie-Obstétrique',
            'annees_experience' => $this->faker->numberBetween(5, 30),
            'structure_sante'   => $this->faker->randomElement($structures),
            'ville'             => $this->faker->randomElement($villes),
            'bio'               => 'Gynécologue passionnée par la santé des femmes sénégalaises.',
            'email_verified_at' => now(),
            'is_active'         => true,
        ];
    }
}
