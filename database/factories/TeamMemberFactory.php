<?php

namespace Database\Factories;

use App\Models\TeamMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeamMember>
 */
class TeamMemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'role_nl' => 'Coördinatie',
            'role_fr' => 'Coordination',
            'bio_nl' => fake()->sentences(2, true),
            'bio_fr' => null,
            'sort' => 0,
            'visible' => true,
        ];
    }
}
