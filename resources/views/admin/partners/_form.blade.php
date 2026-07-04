<x-ba-text name="name" label="Name" required />
<x-ba-text type="url" name="url" label="Website URL" placeholder="https://example.com" />
<x-ba-textarea name="description_nl" label="Description (NL)" rows="3" />
<x-ba-textarea name="description_fr" label="Description (FR)" rows="3" />
<x-ba-select name="category" label="Categorie" :options="\App\Enums\PartnerCategory::getOptionsArray()" allow-null-option comment="Institutioneel en Bondgenoot verschijnen als kaart op /about/partners." />
<x-ba-belongsto name="group" label="Group" :options="\App\Models\Group::orderBy('name')->pluck('name', 'id')->all()" allow-null-option />
<x-ba-boolean name="show_logo" label="Show Logo" />
<x-ba-boolean name="visible" label="Visible" />
<x-ba-mediafile name="logo" label="Logo" comment="Recommended size: 400x200px." />