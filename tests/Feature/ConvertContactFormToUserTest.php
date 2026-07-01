<?php

use App\Models\ContactForm;
use App\Models\Group;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    Group::query()->delete();
    User::query()->delete();
    ContactForm::query()->delete();

    $this->admin = User::factory()->create(['superadmin' => true]);
    $this->group = Group::factory()->create(['name' => 'Kidical Mass Amsterdam']);
});

it('creates a new user and attaches them to the contact form\'s group', function () {
    $contactform = ContactForm::factory()->create([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'group_id' => $this->group->id,
        'handled_at' => null,
    ]);

    actingAs($this->admin)
        ->post(route('admin.contactforms.convert-to-user', $contactform))
        ->assertSessionHas('success');

    $user = User::where('email', 'jane@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Jane Doe')
        ->and($user->groups()->whereKey($this->group)->exists())->toBeTrue()
        ->and($contactform->fresh()->handled_at)->not->toBeNull();
});

it('reuses an existing user by email', function () {
    $existing = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'jane@example.com',
    ]);

    $contactform = ContactForm::factory()->create([
        'name' => 'Jane Doe Updated',
        'email' => 'jane@example.com',
        'group_id' => $this->group->id,
    ]);

    actingAs($this->admin)
        ->post(route('admin.contactforms.convert-to-user', $contactform))
        ->assertSessionHas('success');

    // Should not create a second user with the same email
    expect(User::where('email', 'jane@example.com')->count())->toBe(1)
        // Existing user name should not be overwritten
        ->and($existing->fresh()->name)->toBe('Original Name');
});

it('does not duplicate group attachment if already a member', function () {
    $user = User::factory()->create(['email' => 'jane@example.com']);
    $this->group->users()->attach($user, ['role' => 'pinkvest']);

    $contactform = ContactForm::factory()->create([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'group_id' => $this->group->id,
    ]);

    actingAs($this->admin)
        ->post(route('admin.contactforms.convert-to-user', $contactform))
        ->assertSessionHas('success');

    // Role should remain unchanged (should not overwrite 'pinkvest' with null)
    expect($user->fresh()->groups()->wherePivot('role', 'pinkvest')->exists())->toBeTrue();
});

it('returns 400 when contact form has no group', function () {
    $contactform = ContactForm::factory()->create([
        'email' => 'jane@example.com',
        'group_id' => null,
    ]);

    actingAs($this->admin)
        ->post(route('admin.contactforms.convert-to-user', $contactform))
        ->assertStatus(400);
});
