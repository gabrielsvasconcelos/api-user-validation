<?php

namespace Database\Factories;

use App\Models\UserData;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserDataFactory extends Factory
{
    protected $model = UserData::class;

    public function definition()
    {
        return [
            'cpf' => $this->faker->numerify('###########'),
            'cep' => $this->faker->numerify('########'),
            'email' => $this->faker->unique()->safeEmail(),
            'address_data' => json_encode([
                'logradouro' => $this->faker->streetAddress(),
                'localidade' => $this->faker->city(),
                'uf' => $this->faker->stateAbbr(),
            ]),
            'name_origin_data' => json_encode([
                'country' => $this->faker->countryCode(),
            ]),
            'cpf_status' => $this->faker->randomElement(['limpo', 'pendente', 'negativado']),
        ];
    }
}