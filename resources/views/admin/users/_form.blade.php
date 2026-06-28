<x-ba-text name="name" label="Name" required />
<x-ba-text type="email" name="email" label="Email" required />
<x-ba-text type="password" name="password" label="Password" comment="Leave blank on edit to keep the current password." />

<x-ba-divider />
<x-ba-checkboxes name="groups" label="Groups" :options="\App\Models\Group::orderBy('name')->pluck('name', 'id')->all()" />