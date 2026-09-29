<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\User;

it('saves the extra info fields from the admin form', function () {
    $admin = User::factory()->create(['superadmin' => true]);
    $activity = Activity::factory()->create(['author_id' => $admin->id]);

    $this->actingAs($admin)
        ->put(route('admin.activities.update', $activity), [
            'title_nl' => 'Zomertocht',
            'content_nl' => 'We fietsen samen door de stad.',
            'activity_type' => ActivityType::cases()[0]->value,
            'begin_date' => now()->addWeek()->format('Y-m-d H:i'),
            'location_nl' => 'Jubelpark, Brussel',
            'author_id' => $admin->id,
            'extra_info_nl' => 'Er is een spreker aan de start.',
            'extra_info_fr' => 'Un orateur au départ.',
        ])
        ->assertSessionHasNoErrors();

    expect($activity->fresh())
        ->extra_info_nl->toBe('Er is een spreker aan de start.')
        ->extra_info_fr->toBe('Un orateur au départ.');
});

it('shows both extra info fields on the admin edit form', function () {
    $admin = User::factory()->create(['superadmin' => true]);
    $activity = Activity::factory()->create(['author_id' => $admin->id]);

    $this->actingAs($admin)
        ->get(route('admin.activities.edit', $activity))
        ->assertOk()
        ->assertSee('name="extra_info_nl"', false)
        ->assertSee('name="extra_info_fr"', false);
});
