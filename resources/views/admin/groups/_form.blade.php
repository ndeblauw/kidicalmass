<x-ba-text name="shortname" label="Short Name" required />
<x-ba-text name="name_nl" label="Name (NL)" required />
<x-ba-text name="name_fr" label="Name (FR)" />
<x-ba-textarea name="intro_nl" label="Intro (NL)" rows="3" comment="One or two sentences under the page title. Leave empty to show the standard sentence." />
<x-ba-textarea name="intro_fr" label="Intro (FR)" rows="3" />
<x-ba-text name="zip" label="Postal Code" />
<x-ba-belongsto name="parent" label="Parent Group" :options="\App\Models\Group::orderBy('name_nl')->pluck('name_nl', 'id')->all()" allow-null-option />
<x-ba-boolean name="invisible" label="Invisible" comment="Hide this group from the public groups index page." />
<x-ba-datepicker name="started_at" label="Started At" only-date required />
<x-ba-datepicker name="ended_at" label="Ended At" only-date />
<x-ba-divider />
<x-ba-mediafile name="main" label="Main Image" />
<x-ba-mediafile name="gallery" label="Additional Images" multiple />
<x-ba-mediafile name="downloads" label="Downloads" multiple comment="PDF or images (flyers, route maps). Listed on the group page." />
