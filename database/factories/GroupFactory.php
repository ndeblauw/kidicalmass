<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('2020-01-01', 'now');
        
        return [
            'shortname' => fake()->unique()->slug(2),
            'name' => fake()->company(),
            'zip' => fake()->postcode(),
            'parent_id' => null,
            'started_at' => $startedAt,
            'ended_at' => fake()->optional(0.1)->dateTimeBetween($startedAt, 'now'),
        ];
    }

    public function withParent(Group $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
        ]);
    }
}
