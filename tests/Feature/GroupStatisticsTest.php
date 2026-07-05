<?php

use App\Models\Group;

it('renders group growth per year in Dutch as a description list', function () {
    Group::factory()->create(['started_at' => '2022-05-01']);
    Group::factory()->count(2)->create(['started_at' => '2023-05-01']);

    $view = $this->blade('<x-group-statistics />');

    $view->assertSee(__('common.groups_growth_title'));
    $view->assertSee('groepen');
    $view->assertSeeInOrder(['<dl', '<dt', '<dd'], false);
    $view->assertDontSee('We are growing!');
});

it('renders the empty state in Dutch', function () {
    $view = $this->blade('<x-group-statistics />');

    $view->assertSee(__('common.groups_growth_empty'));
    $view->assertDontSee('No group statistics');
});
