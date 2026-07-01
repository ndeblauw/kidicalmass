<?php

namespace App\Livewire;

use App\Models\Group;
use App\Models\PostalCode;
use App\Support\Location\CurrentLocation;
use App\Support\Location\Proximity;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class NewsletterSignup extends Component
{
    /**
     * RFC 5321 caps a full email address at 254 characters; anything longer is
     * a paste accident or junk, so we reject it with a friendly nudge.
     */
    private const EMAIL_RULES = 'required|email|max:254';

    public string $email = '';

    /** @var array<int, int> */
    public array $selectedGroups = [];

    public bool $submitted = false;

    public bool $showGroups = false;

    /**
     * Opt in to following every chapter countrywide ("Heel België"), instead of
     * just the nearby chips. Off by default: the nearby selection is the norm.
     */
    public bool $followAll = false;

    /** @var array{zip: string, lat: float, lng: float, name: string}|null */
    public ?array $pickedLocation = null;

    public function mount(): void
    {
        // If we already know where they are (shared kcm_location cookie), expand
        // the groups straight away and pre-select every nearby chapter (opt-out).
        // Cold visitors start collapsed: email first, groups behind a link.
        if (CurrentLocation::resolve() !== null) {
            $this->showGroups = true;
            $this->selectedGroups = $this->nearestGroups()->pluck('id')->all();
        }
    }

    public function revealGroups(): void
    {
        $this->showGroups = true;
    }

    /**
     * Real-time feedback: when the visitor leaves the email field (wire:model.blur)
     * we normalise and validate the address straight away, so a typo is flagged
     * before they reach the submit button. An empty field stays quiet, though, so
     * we don't nag someone who simply tabbed past it.
     */
    public function updatedEmail(): void
    {
        $this->email = $this->normalizedEmail();

        if ($this->email === '') {
            $this->resetErrorBag('email');

            return;
        }

        $this->validateOnly('email', ['email' => self::EMAIL_RULES]);
    }

    /**
     * The reactive location picker dispatches this without a page navigation, so
     * a typed email survives. Recompute nearby chapters from the passed coords
     * and pre-select them (opt-out default).
     *
     * @param  array{zip: string, lat: float, lng: float, name: string}|null  $payload
     */
    #[On('location-selected')]
    public function setLocation(?array $payload): void
    {
        $this->pickedLocation = $payload;
        $this->showGroups = true;
        $this->selectedGroups = $this->nearestGroups()->pluck('id')->all();
    }

    public function subscribe(): void
    {
        $this->email = $this->normalizedEmail();

        $rules = ['email' => self::EMAIL_RULES];

        // Only constrain group choice when chips are actually on screen AND the
        // visitor hasn't opted into "Heel België": someone who revealed the
        // section without a location (no nearby chapters) or who ticked the
        // countrywide option is still free to subscribe.
        if ($this->showGroups && ! $this->followAll && $this->nearestGroups()->isNotEmpty()) {
            $rules['selectedGroups'] = 'array|min:1';
        }

        $this->validate($rules, [
            'selectedGroups.min' => 'Kies minstens één groep bij jou in de buurt, of volg heel België.',
        ]);

        // TODO(backend, Nico): persist the e-mail + chosen scope (location /
        // selectedGroups) to the Email-subscription model and send the
        // double opt-in mail. Until that lands this is an optimistic,
        // non-persisting confirmation.

        $this->submitted = true;
    }

    /**
     * Trimmed, lower-cased email. Livewire bypasses the HTTP TrimStrings
     * middleware, so a pasted "  Jouw@Email.BE " would otherwise fail an
     * exact-match check or create a near-duplicate subscription.
     */
    private function normalizedEmail(): string
    {
        return strtolower(trim($this->email));
    }

    /**
     * The five chapters nearest the visitor's saved location, nearest first.
     * Not radius-bound: even a visitor far from any chapter gets the closest
     * ones to pick from. Empty only until a location is set, so the checkboxes
     * appear once we know where they are.
     *
     * @return Collection<int, Group>
     */
    public function nearestGroups(): Collection
    {
        $location = $this->pickedLocation ?? CurrentLocation::resolve();

        if ($location === null) {
            return new Collection;
        }

        $groups = Group::query()->visible()->whereNotNull('zip')->get();

        $origin = ['lat' => (float) $location['lat'], 'lng' => (float) $location['lng']];

        $coords = PostalCode::query()
            ->whereIn('zip', $groups->pluck('zip')->filter()->unique()->all())
            ->get()
            ->keyBy('zip');

        return $groups
            ->map(function (Group $group) use ($coords, $origin): Group {
                $postalCode = $coords->get($group->zip);

                $group->setAttribute('distance_km', $postalCode
                    ? Proximity::distanceKm($origin, ['lat' => (float) $postalCode->latitude, 'lng' => (float) $postalCode->longitude])
                    : null);

                $group->setAttribute('gemeente', $this->gemeenteFor($group));

                return $group;
            })
            ->filter(fn (Group $group): bool => $group->distance_km !== null)
            ->sortBy(fn (Group $group): float => $group->distance_km)
            ->take(5)
            ->values();
    }

    /**
     * The chapter's gemeente: the group name minus the leading "Kidical Mass "
     * so the chips stay short. Falls back to the full name if stripping empties it.
     */
    private function gemeenteFor(Group $group): string
    {
        $stripped = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));

        return $stripped !== '' ? $stripped : $group->name;
    }

    /**
     * Total visible chapters. Drives whether the "Heel België" pill is offered:
     * it only appears when there are chapters beyond the nearby ones on screen.
     */
    public function totalGroups(): int
    {
        return Group::query()->visible()->count();
    }

    /**
     * The chapter scope this signup will follow: every visible chapter when the
     * visitor ticked "Heel België", otherwise just the nearby chips they kept.
     * This is the payload the backend will persist once that lands.
     *
     * @return array<int, int>
     */
    public function resolvedGroupIds(): array
    {
        if ($this->followAll) {
            return Group::query()->visible()->orderBy('id')->pluck('id')->all();
        }

        return $this->selectedGroups;
    }

    public function render()
    {
        return view('livewire.newsletter-signup', [
            'location' => CurrentLocation::resolve(),
            'groups' => $this->nearestGroups(),
            'totalGroups' => $this->totalGroups(),
        ]);
    }
}
