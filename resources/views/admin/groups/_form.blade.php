<x-ba-text name="shortname" label="Short Name" required />
<x-ba-text name="name_nl" label="Name (NL)" required />
<x-ba-text name="name_fr" label="Name (FR)" />
<x-ba-text name="zip" label="Postal Code" />
<x-ba-belongsto name="parent" label="Parent Group" :options="\App\Models\Group::orderBy('name_nl')->pluck('name_nl', 'id')->all()" allow-null-option />
<x-ba-boolean name="invisible" label="Invisible" comment="Hide this group from the public groups index page." />
<x-ba-datepicker name="started_at" label="Started At" only-date required />
<x-ba-datepicker name="ended_at" label="Ended At" only-date />
<x-ba-divider />
<x-ba-mediafile name="main" label="Main Image" />
<x-ba-mediafile name="gallery" label="Additional Images" multiple />