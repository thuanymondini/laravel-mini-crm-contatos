<?php

namespace Database\Factories;

use App\Infrastructure\Contact\Eloquent\ContactModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactModelFactory extends Factory
{
    protected $model = ContactModel::class;

    public function definition(): array
    {
        return [
            'name'   => fake()->name(),
            'email'  => fake()->unique()->safeEmail(),
            'phone'  => fake()->numerify('119########'),
            'score'  => 0,
            'status' => 'pending',
        ];
    }
}
