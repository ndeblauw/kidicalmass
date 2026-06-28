<x-ba-text name="title_nl" label="Title (NL)" required />
<x-ba-text name="title_fr" label="Title (FR)" required />
<x-ba-text name="outlet" label="Outlet" required comment="News outlet name (RTBF, BRUZZ, HLN, …)." />
<x-ba-text type="url" name="url" label="Article URL" placeholder="https://..." comment="Link to the original article or video." />
<x-ba-datepicker name="published_at" label="Published At" />
<x-ba-belongsto name="author" label="Author" :options="\App\Models\User::orderBy('name')->pluck('name', 'id')->all()" allow-null-option />

<x-ba-divider subtitle="Linked records" />
<x-ba-checkboxes name="activities" label="Activities" :options="\App\Models\Activity::orderByDesc('begin_date')->limit(100)->get()->mapWithKeys(fn (\App\Models\Activity $a) => [$a->id => $a->title_nl.' ('.$a->begin_date?->format('Y-m-d').')'])->all()" />
<x-ba-checkboxes name="articles" label="Site Articles" :options="\App\Models\Article::orderByDesc('created_at')->limit(100)->get()->mapWithKeys(fn (\App\Models\Article $a) => [$a->id => $a->title_nl])->all()" />
<x-ba-checkboxes name="groups" label="Groups" :options="\App\Models\Group::orderBy('name')->pluck('name', 'id')->all()" />

<x-ba-divider />
<x-ba-mediafile name="document" label="Article scan / PDF" comment="Upload a PDF scan or image of the press article as it appeared." />