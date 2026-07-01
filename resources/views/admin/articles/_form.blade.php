<x-ba-text name="title_nl" label="Title (NL)" />
<x-ba-text name="title_fr" label="Title (FR)" />
<x-ba-textarea name="content_nl" label="Content (NL)" rows="5" />
<x-ba-textarea name="content_fr" label="Content (FR)" rows="5" />

<x-ba-belongsto name="author" label="Author" :options="\App\Models\User::orderBy('name')->pluck('name', 'id')->all()" />
<x-ba-checkboxes name="groups" label="Groups" :options="\App\Models\Group::orderBy('name')->pluck('name', 'id')->all()" />

<x-ba-divider />
<x-ba-mediafile name="main" label="Main Image" comment="Shown on the articles index card." />
<x-ba-mediafile name="gallery" label="Additional Images" multiple comment="Shown on the article detail page." />