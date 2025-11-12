<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
            'aadhar_number' => $this->faker->unique()->numerify('##########') . $this->faker->numerify('##'),
            'pan_number' => strtoupper($this->faker->lexify('?????')) . $this->faker->numerify('####') . strtoupper($this->faker->lexify('?')),
            'phone_number' => $this->faker->numerify('9#########'),
            'age' => $this->faker->numberBetween(21, 60),
            'aadhar_document_path' => null,
            'pan_document_path' => null,
        ];
    }
}

