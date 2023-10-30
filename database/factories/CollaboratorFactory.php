<?php

namespace Database\Factories;

use App\Classes\Services\CollaboratorService;
use App\Models\Collaborator;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class CollaboratorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Collaborator::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws Exception
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'cpf' => $this->faker->unique()->numerify('###########'),
            'cod_user' => User::factory()->create()->id,
            'registration' => $this->faker->unique()->numerify('########'),
            'password' => Hash::make('123456'),
            'flag_permissions' => random_int(0, 2),
        ];
    }
}
