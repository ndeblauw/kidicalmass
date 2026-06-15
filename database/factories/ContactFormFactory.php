<?php

namespace Database\Factories;

use App\Models\ContactForm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactForm>
 */
class ContactFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'message' => fake()->paragraph(3),
            'phone' => fake()->optional(0.6)->phoneNumber(),
            'page_url' => fake()->randomElement([
                url('/'),
                url('/groups'),
                url('/articles'),
                url('/activities'),
            ]),
            'honeypot' => null, // Should always be null for legitimate submissions
        ];
    }
}
