<x-ba-text name="title_nl" label="Title (NL)" />
<x-ba-text name="title_fr" label="Title (FR)" />
<x-ba-textarea name="content_nl" label="Content (NL)" rows="5" rte />
<x-ba-textarea name="content_fr" label="Content (FR)" rows="5" rte />

<x-ba-divider subtitle="Activity Details" />
<x-ba-select name="activity_type" label="Activity Type" :options="\App\Enums\ActivityType::getOptionsArray()" />
<x-ba-datepicker name="begin_date" label="Begin Date" required />
<x-ba-text name="location" label="Location" required comment="For Critical Mass: enter the starting address." />
<x-ba-text name="postal_code" label="Postal Code" comment="e.g. 1000 — used in the display title." />
<x-ba-text name="distance" label="Distance" comment="e.g. 5–7 km" />
<x-ba-text type="number" name="duration_minutes" label="Duration (minutes)" comment="Duration of the activity in minutes." />

<x-ba-divider subtitle="Route Information" />
<x-ba-text type="url" name="commute_link" label="Commute Route Link" comment="URL to visualize the route (e.g. Komoot, RideWithGPS)." />
<x-ba-text type="url" name="komoot_url" label="Komoot URL" comment="Paste the public Komoot tour URL. Optional." />
<x-ba-mediafile name="gpx" label="Route (GPX file)" comment="Export GPX from Komoot (or any route planner) and upload here." />

<x-ba-divider subtitle="Organisation" />
<x-ba-belongsto name="author" label="Author" :options="\App\Models\User::orderBy('name')->pluck('name', 'id')->all()" />
<x-ba-belongsto name="organizer" label="Organizer" :options="\App\Models\User::orderBy('name')->pluck('name', 'id')->all()" allow-null-option comment="Leave empty to automatically assign from the responsible group or author." />
<x-ba-checkboxes name="groups" label="Groups" :options="\App\Models\Group::orderBy('name')->pluck('name', 'id')->all()" />

<x-ba-divider subtitle="Images" />
<x-ba-mediafile name="main" label="Main Image" comment="Shown on the activities index card." />
<x-ba-mediafile name="gallery" label="Additional Images" multiple comment="Shown on the activity detail page." />

<x-ba-divider subtitle="Visibility" />
<x-ba-boolean name="is_published" label="Published" comment="Unpublished activities are hidden from the public site." />