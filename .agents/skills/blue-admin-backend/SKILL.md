---
name: blue-admin-backend
description: "Apply this skill when creating or modifying admin CRUD resources using blue-admin. Triggers on: admin panel, blue-admin, admin crud, backend crud, resource admin. Covers creating routes, empty controllers, BlueAdmin config classes, FormRequests, _form.blade.php views, sidebar menu entries, and custom index/show views."
license: MIT
metadata:
---

# Blue-Admin Backend

A step-by-step workflow for adding admin CRUD resources using `ndeblauw/blue-admin`. The system uses convention-over-configuration auto-discovery so you only write the minimal files — everything else (CRUD logic, media handling, belongsToMany sync, policy checks) is handled by the vendor `AdminController` and its 6 traits.

## Quick Reference

| Step | Location | What to Create/Wire |
|------|----------|---------------------|
| 1 | `routes/web.php` | `Route::resource('xxx', AdminXxxController::class)` inside the `admin` group |
| 2 | `app/Http/Controllers/Admin/XxxController.php` | Empty class, extends `AdminController` (from vendor) |
| 3 | `app/BlueAdmin/Xxx.php` | Config class, extends `BlueAdminModel`, maps to Eloquent model |
| 4 | `app/Http/Requests/XxxRequest.php` | FormRequest with validation rules |
| 5 | `resources/views/admin/xxx/_form.blade.php` | Form fields using `x-ba-*` components |
| 6 | `config/blue-admin.php` | Sidebar menu entry |
| 7 | `resources/views/admin/xxx/index.blade.php` (optional) | Custom index view |
| 7b | `resources/views/admin/xxx/show.blade.php` (optional) | Custom show/detail view |

Run `vendor/bin/pint --format agent` after creating or editing any PHP files.

## Architecture Overview

```
routes/web.php  ─── Route::resource('treatments', AdminTreatmentController::class)
                           │
                           ▼
App\Controllers\Admin\TreatmentController   (empty — extends Ndeblauw\BlueAdmin\Http\Controllers\AdminController)
                           │
                    auto-discovers by class name
                           │
                           ▼
App\BlueAdmin\Treatment                     (config — maps to Eloquent model)
    $CLASS → App\Models\Treatment
                           │
              ┌────────────┼────────────────┐
              ▼            ▼                ▼
     App\Http\Requests   Views              Routes
     TreatmentRequest   admin.treatments.*  admin.treatments.*
       (validation)     ─────┬──────       (named routes)
                             │
              ┌──────────────┼──────────────┐
              ▼              ▼              ▼
         index.blade    _form.blade.php  show.blade.php
         (optional)     (if missing →    (optional)
                         form-not-found)
```

### Auto-Discovery Naming Conventions

```
Controller class:      AdminTreatmentController
Config class:          App\BlueAdmin\Treatment          (strips "Controller", prepends App\BlueAdmin)
FormRequest class:     App\Http\Requests\TreatmentRequest (strips "Controller", appends "Request")
Eloquent model:        App\Models\Treatment             (from $CLASS property in config)
View path:             admin.treatments.{action}        (from modelname → plural → lowercase)
Named routes:          admin.treatments.{action}        (same convention as views)
```

## Step-by-Step Workflow

### Step 1 — Add the Route

In `routes/web.php`, inside the existing `admin` route group (which has `prefix('admin')`, `name('admin.')`, `middleware(['auth'])`):

```php
Route::resource('treatments', AdminTreatmentController::class);
```

This registers all 7 RESTful routes: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`.

### Step 2 — Create an Empty Controller

```bash
php artisan make:controller Admin/TreatmentController --no-interaction
```

The plain stub creates an empty class extending `App\Http\Controllers\Controller`. Replace the parent with the vendor one — it must extend `Ndeblauw\BlueAdmin\Http\Controllers\AdminController` with zero methods:

```php
<?php

namespace App\Http\Controllers\Admin;

use Ndeblauw\BlueAdmin\Http\Controllers\AdminController;

class TreatmentController extends AdminController
{
    //
}
```

That's it. The vendor `AdminController` auto-discovers the config class (`App\BlueAdmin\Treatment`) by stripping "Controller" from the class name.

### Step 3 — Create the BlueAdminModel Config Class

Create `app/BlueAdmin/Treatment.php`:

```php
<?php

namespace App\BlueAdmin;

use Ndeblauw\BlueAdmin\Models\BlueAdminModel;

class Treatment extends BlueAdminModel
{
    public $CLASS = \App\Models\Treatment::class;
    public $name_to_use = 'Behandelingen';
    public $title_field = 'name';
}
```

### Step 4 — Create a FormRequest

```bash
php artisan make:request TreatmentRequest --no-interaction
```

The artisan stub generates `authorize()` returning `false` and a verbose PHPDoc block. Per project convention (see existing `TagRequest`, `PackageRequest`, `UserRequest`), set `authorize(): true` and drop the PHPDoc — authorization is enforced upstream by `AdminController::policyCheck()`.

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TreatmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_active' => ['required', 'boolean'],
            'name_nl' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'description_nl' => ['required', 'string'],
            'description_en' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('treatments', 'slug')->ignore($this->treatment)],
        ];
    }
}
```

The controller auto-discovers this via `AdminControllerFormRequestTrait` — looks for `App\Http\Requests\{Modelname}Request` (note: lowercase-cased modelname is not used — the PascalCase `modelname()` from `BlueAdminModel` is the lookup key).

#### Rules you MUST include when the form or config uses these features

The vendor `AdminController::store()` / `update()` call `filepondPreparations($valid)` and `belongsToManyPreparations($valid)` on the **validated** array. Keys that don't appear in `$valid` are silently dropped (set to `[]`), and any keys NOT consumed by those handlers get passed straight to `Model::create($valid)` / `$model->update($valid)`. That means:

| Form/Config feature | Required rule | Why |
|---------------------|---------------|-----|
| `$config->filepond = ['image']` | `'image' => ['nullable', 'array'], 'image.*' => ['string']` | Without rules, uploads never reach `filepondPreparations` and the file is lost. The array items are Filepond server IDs (strings). |
| `$config->belongsToMany = ['tags']` | `'tags' => ['nullable', 'array'], 'tags.*' => ['integer', 'exists:tags,id']` | Without rules, `sync([])` runs on every save, wiping existing relations. |
| Form has `<x-ba-tagselect name="foo">` or `<x-ba-checkboxes name="foo">` but no `$belongsToMany` | Add the rule **AND** add `public $belongsToMany = ['foo'];` to the config | Otherwise `foo` stays in `$valid` and hits `Model::create()` → mass-assignment / "column not found" error (the field is a relation method, not a column). |
| Model uses `Spatie\Sluggable` (slug auto-generated) | `'slug' => ['nullable', 'string', 'max:255', Rule::unique('table', 'slug')->ignore($this->{model})]` | Allows manual slug overrides on update; `->ignore($this->treatment)` is the route-parameter binding name (singular snake of the model). |
| `<x-ba-belongsto name="category" />` | `'category_id' => ['nullable', 'integer', 'exists:categories,id']` | The component appends `_id` to the input name. |
| `<x-ba-datepicker name="published_at" />` | `'published_at' => ['nullable', 'date']` (or `'nullable', 'date_format:Y-m-d H:i:s'` if datetime) | Picker may submit empty or full datetime string. |

#### The `->ignore($this->{model})` route-binding pattern

For update routes like `admin/treatments/{treatment}`, the FormRequest receives the route-bound model as `$this->treatment` (singular snake_case of the model class basename). Use that exact property name in `Rule::unique(...)->ignore(...)`. For `Article` → `$this->article`, for `Formation` → `$this->formation`, etc.

### Step 5 — Create the `_form.blade.php` View

Create `resources/views/admin/treatments/_form.blade.php`:

```blade
<x-ba-boolean name="is_active" label="Visible" />

<x-ba-text name="name_nl" label="Naam" required />
<x-ba-textarea name="description_nl" label="Omschrijving" rte />

<x-ba-divider subtitle="EN"/>
<x-ba-text name="name_en" label="Name" />
<x-ba-textarea name="description_en" label="Description" rte />
```

The generic create/edit views (from the vendor) include this partial. If no `_form.blade.php` exists, a red "form not found" placeholder shows instead.

### Step 6 — Add the Sidebar Menu Entry

In `config/blue-admin.php`, add an entry to the `menu` array:

```php
[
    'title' => 'Treatments',
    'color' => 'green-mist',
    'link' => 'admin/treatments',
    'icon' => 'fa-book-medical',
],
```

Optional menu key reference:

| Key | Required | Description |
|-----|----------|-------------|
| `title` | yes | Display name in sidebar |
| `link` | yes | URL path (relative to site root) |
| `icon` | no | Font Awesome icon class (e.g., `fa-user`, `fa-home`) |
| `color` | no | Tailwind color name for accent (e.g., `green-mist`, `sky`, `purple`, `blue`) |
| `header` | n/a | Use `['header' => 'Section Name']` as a section divider instead of a link |
| `permission` | no | Spatie permission name — hides the menu item if user lacks it |
| `submenu` | no | Array of nested menu items with same structure |

### Step 7 (Optional) — Custom Index or Show Views

If you need a custom index table (not just the generic DataTable), create `resources/views/admin/treatments/index.blade.php`:

```blade
<x-ba-admin-layout>
    {{-- your custom index HTML --}}
    @push('blueadmin_header')
        @include('BlueAdminLayout::dataTables')
    @endpush
</x-ba-admin-layout>
```

Available variables in index view: `$models` (Eloquent collection), `$config` (BlueAdminModel instance).

Available variables in show view: `$model` (single Eloquent model), `$attributesToShow` (array of column names), `$config`.

Custom show view at `resources/views/admin/treatments/show.blade.php`.

The view resolution logic (`AdminControllerSelectViewTrait`):
1. Check `admin.{modelsname}.{action}` in app views
2. Fall back to `BlueAdminGeneric::old.{action}` vendor generic view

## Available BlueAdminModel Properties

Properties you can set on your `app/BlueAdmin/Xxx.php` config class:

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `$CLASS` | string | required | Fully qualified Eloquent model class, e.g., `\App\Models\Treatment::class` |
| `$name_to_use` | string | required | Display name used in views (create/edit headers, sidebar fallback) |
| `$title_field` | string | `'title'` | The model attribute used as the record title in index/show views |
| `$indexTableColumns` | array | `['title']` | Column names to display in the index table |
| `$filepond` | array | `[]` | Media collection names handled via Filepond (must match media collections on the model) |
| `$belongsToMany` | array | `[]` | Relationship method names that should be synced on save |
| `$color` | string | `'blue'` | Tailwind color name for accent (common values seen: `sky`, `rose`, `blue`, `violet`; custom values like `green-mist` may be defined in the project) |
| `$attributesToShow` | array | null | Column names to display in show view (null = auto-detect from model attributes) |
| `$index_load` | array | `[]` | Eager-load relations for the index view (e.g., `['media', 'collaborator']`) |
| `$show_load` | array | `[]` | Eager-load relations for the show view |
| `$use_ajax_index` | bool | `false` | Use server-side DataTables AJAX index (routes to `ApiIndexController`) |
| `$has_policy` | bool | `false` | Enable Laravel policy check for this model |
| `$afterCreate` | string | null | Redirect target after create: `'index'` or `'show'` |
| `$afterEdit` | string | null | Redirect target after edit: `'index'` or `'show'` |

## x-ba-* Components Reference

### Common Parameters (from base `Component` class)

All form input components accept these:

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `name` | string | required | Input name attribute; also used for model binding |
| `label` | string | `ucfirst($name)` | Display label above the input |
| `placeholder` | string | `''` | Placeholder text |
| `comment` | string | `''` | Helper text displayed below the input |
| `id` | string | `$name` | HTML id attribute |
| `required` | bool | `false` | Adds a blue `*` indicator |
| `disabled` | bool | `false` | Disables the input |
| `size` | string | `'w-full md:w-1/2'` | Width classes for the field wrapper |
| `value` | string | null | Default value (create) or bound model value (edit) |

### Input Components

#### `<x-ba-text />`

A single-line text input. `type` parameter to override HTML input type (default: `'text'`).

| Parameter | Type | Default |
|-----------|------|---------|
| `type` | string | `'text'` | HTML input type (`text`, `email`, `url`, `tel`, etc.) |

```blade
<x-ba-text name="email" label="Email" type="email" required />
<x-ba-text name="booking_url" label="Link" placeholder="https://..." comment="Laat leeg indien geen" />
```

#### `<x-ba-textarea />`

Multi-line text input. Has optional RTE (rich text editor via Livewire).

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `rows` | int | `6` | Number of visible rows |
| `rte` | bool | `false` | Enable rich text editor (TinyMCE-based Livewire component) |
| `h2h3` | bool | `false` | Restrict RTE headings to h2/h3 only |

```blade
<x-ba-textarea name="description_nl" label="Omschrijving" rte />
<x-ba-textarea name="bio_en" label="Bio" rows="4" />
```

#### `<x-ba-boolean />`

Toggle switch (renders as two radio buttons: yes/no). Translates the label as the question.

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `legend` | string | `''` | Optional legend text for the fieldset |

```blade
<x-ba-boolean name="is_active" label="Visible" />
```

#### `<x-ba-select />`

Dropdown select from an array of options.

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `options` | array | required | `['value' => 'Display Label', ...]` |
| `allowNullOption` | bool | `false` | Add an empty "choose..." option at the top (prevents auto-selecting first option) |

```blade
<x-ba-select name="type" label="Type" :options="['kine' => 'Kinesist', 'studio' => 'Studio']" allow-null-option />
```

#### `<x-ba-select-searchbox />`

Same as select but with a search/filter input for long lists.

```blade
<x-ba-select-searchbox name="user_id" label="User" :options="$users->pluck('name', 'id')" />
```

#### `<x-ba-radiobuttons />`

Radio button group.

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `options` | array | required | `['value' => 'Display Label', ...]` |
| `inline` | bool | `false` | Render options horizontally instead of stacked |
| `source` | string | null | Name of a class in `BlueAdminFormelements::` namespace returning options |

```blade
{{-- inline options --}}
<x-ba-radiobuttons name="type" label="Voor" :options="['kine' => 'Kine Madou', 'studio' => 'Studio Madou']" />

{{-- with source class --}}
<x-ba-radiobuttons name="status" label="Status" source="StatusOptions" />
```

#### `<x-ba-checkboxes />`

Checkbox group. For belongsToMany relationships, bound values are auto-populated from the model.

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `options` | array | required | `['value' => 'Display Label', ...]` |
| `inline` | bool | `false` | Render options horizontally |
| `values` | array | null | Pre-selected values (only used when no model binding) |
| `source` | string | null | Source class name |

```blade
<x-ba-checkboxes name="tags" label="Tags" :options="\App\Models\Tag::pluck('name', 'id')" />
```

#### `<x-ba-tagselect />`

Multi-select with tag-style chips. For belongsToMany relationships.

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `options` | array | required | `['value' => 'Display Label', ...]` |
| `inline` | bool | `false` | Render inline |
| `allowNullOption` | bool | `false` | Allow null |
| `values` | array | null | Pre-selected values |
| `source` | string | null | Source class name |

```blade
<x-ba-tagselect name="categories" label="Categories" source="CategoryOptions" />
```

#### `<x-ba-mediafile />`

File upload using Filepond. For `spatie/laravel-medialibrary` integration.

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `multiple` | bool | `false` | Allow multiple file uploads |
| `maxFiles` | int | null | Maximum number of files (null = unlimited) |

```blade
<x-ba-mediafile name="image" label="Foto" />
<x-ba-mediafile name="gallery" label="Gallery" multiple max-files="5" />
```

#### `<x-ba-datepicker />`

Date, time, or datetime picker.

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `only-date` | bool | `false` | Show date-only picker (saves as `Y-m-d`) |
| `only-time` | bool | `false` | Show time-only picker (saves as `H:i`) |

```blade
<x-ba-datepicker name="published_at" label="Published" only-date />
<x-ba-datepicker name="start_time" label="Start" only-time />
<x-ba-datepicker name="starts_at" label="Start datum" />
```

#### `<x-ba-belongsto />`

Foreign key select for `belongsTo` relationships. Appends `_id` to the name automatically.

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `options` | array | optional | `['id' => 'Display Label', ...]` |
| `source` | string | null | Source class name |
| `allowNullOption` | bool | null | Allow null selection |
| `inline` | bool | `false` | Render inline |
| `template` | string | null | Override template |

```blade
{{-- name becomes 'collaborator_id' automatically --}}
<x-ba-belongsto name="collaborator" label="Lesgever" source="CollaboratorOptions" />
```

#### `<x-ba-tinymceimage />`

Inline image upload for TinyMCE (separate from the `rte` textarea, used for standalone image insertion).

```blade
<x-ba-tinymceimage name="header_image" label="Header Afbeelding" />
```

#### `<x-ba-hidden />`

Hidden input field.

| Parameter | Type | Default |
|-----------|------|---------|
| `value` | string | null |

```blade
<x-ba-hidden name="tenant_id" :value="$tenant->id" />
```

### Layout Components

#### `<x-ba-divider />`

Section divider with optional subtitle.

| Parameter | Type | Default |
|-----------|------|---------|
| `subtitle` | string | null |

```blade
<x-ba-divider subtitle="NL" />
<x-ba-divider />   {{-- just a horizontal rule --}}
```

#### `<x-ba-info />`

Read-only information display (not an input).

| Parameter | Type | Default |
|-----------|------|---------|
| `label` | string | required |
| `value` | string | required |
| `comment` | string | `''` |

```blade
<x-ba-info label="Created by" :value="$model->creator->name" />
```

### Admin UI Components

#### `<x-ba-admin-layout />`

Root layout wrapper for all admin views. Use on every admin page.
```blade
<x-ba-admin-layout>
    {{-- page content --}}
</x-ba-admin-layout>
```

#### `<x-ba-admin-button />`

Styled link that looks like an admin button.

| Parameter | Type | Default |
|-----------|------|---------|
| `href` | string | `'#'` |

```blade
<x-ba-admin-button href="{{ $config->getCreateUrl() }}" class="bg-green-500">Create New</x-ba-admin-button>
```

#### `<x-ba-button />`

Form submit button.

| Parameter | Type | Default |
|-----------|------|---------|
| `type` | string | `'submit'` |

```blade
<x-ba-button type="submit" class="bg-blue-500">Save</x-ba-button>
```

#### `<x-ba-delete-button />`

Styled delete button that submits a DELETE form.

| Parameter | Type | Default |
|-----------|------|---------|
| `action` | string | required |

```blade
<x-ba-delete-button action="{{ $config->getDestroyUrl($model->getKey()) }}" />
```

#### `<x-ba-show-info />`

Displays a read-only attribute with label and value in show view.

## URL Helper Methods (on BlueAdminModel)

Available on `$config` in all views:

| Method | Returns | Example |
|--------|---------|---------|
| `$config->getIndexUrl()` | URL | `admin/treatments` |
| `$config->getCreateUrl($params)` | URL | `admin/treatments/create` |
| `$config->getStoreUrl()` | URL | `admin/treatments` (POST) |
| `$config->getShowUrl($id)` | URL | `admin/treatments/1` |
| `$config->getEditUrl($id)` | URL | `admin/treatments/1/edit` |
| `$config->getUpdateUrl($id)` | URL | `admin/treatments/1` (PUT) |
| `$config->getDestroyUrl($id)` | URL | `admin/treatments/1` (DELETE) |
| `$config->getBackOrIndexUrl()` | URL | Session return path or index fallback |
| `$config->modelname()` | string | `'Treatment'` |
| `$config->modelsname()` | string | `'treatments'` |
| `$config->titleField()` | string | Value of `$title_field` or `'title'` |
| `$config->pathforAdminViews()` | string | `'admin.treatments'` |
| `$config->routename()` | string | `'treatments'` |

## Gotchas & Common Pitfalls

These are not obvious from the docs and have caused real bugs in this project.

### 1. FormRequest stub ships with `authorize(): false`

`php artisan make:request` generates `return false;`. **Always flip to `true`.** Authorization in blue-admin happens upstream via `AdminController::policyCheck()` (which consults policies, Spatie permissions, or the `IsAdmin` middleware). A `false` return here makes every store/update 403 — including for admins — before policy logic even runs.

### 2. Validated array is the contract between FormRequest, Config, and Controller

The vendor flow is:

```
$request = $this->getValidatedRequestObject();   // app( XxxRequest::class )
$valid   = $request->validated();
$filepond      = $this->filepondPreparations($valid);       // keys listed in $config->filepond
$belongsToMany = $this->belongsToManyPreparations($valid);  // keys listed in $config->belongsToMany
$model = ({$config->CLASS})::create($valid);   // ← remaining validated keys hit the DB
```

Three consequences, in increasing order of pain:

1. **Filepond uploads silently lost.** If `$config->filepond = ['image']` but `'image'` has no rule, `validated()` omits it, `filepondPreparations` sees it missing and sets `$filepond['image'] = []`, and you save a record with no media. Add `'image' => ['nullable', 'array'], 'image.*' => ['string']`.
2. **belongsToMany silently wiped.** Same shape: `'tags'` must be in the rules so it survives `$valid`, otherwise `sync([])` runs and erases existing relations on every save.
3. **Mass-assignment / "column not found" error.** If your form posts a key (`tags`, `categories`, …) that's a **relation method**, not a column, and (a) you added it to the rules **but** (b) you forgot to declare `public $belongsToMany = ['tags'];` on the config, the key stays in `$valid` and flows into `Model::create($valid)` → `Illuminate\Database\QueryException` (no such column: `tags`). Diagnose by checking `$config->belongsToMany` covers every relation input in the form.

### 3. `Rule::unique(...)->ignore()` expects the route-bound model property

For `Route::resource('treatments', ...)` the update URL is `admin/treatments/{treatment}`. Laravel route-model-binding injects that as `$this->treatment` on the FormRequest — singular snake_case of the model class basename. So `Article` → `$this->article`, `Formation` → `$this->formation`, `User` → `$this->user`. Mismatching this name (e.g., `$this->id`) compiles but won't ignore the current row on update, causing spurious "slug already taken" validation failures.

### 4. Create the config BEFORE the FormRequest rules for filepond/belongsToMany

The rules you need to write depend on what the config declares (which collections/relations). When scaffolding a new resource, write Step 3 (config) — including any `public $filepond` and `public $belongsToMany` arrays — before Step 4 (request rules). Otherwise you'll either under-validate (lost uploads) or over-validate (mass-assignment errors per Gotcha #2).

### 5. Pair every `<x-ba-tagselect>` / `<x-ba-checkboxes>` with a `$belongsToMany` entry

These components render multi-select inputs for relations. If you place one in `_form.blade.php` without adding the matching key to `$config->belongsToMany`, the post will fail (per Gotcha #2.3). The rule of thumb:

```
_form.blade.php:  <x-ba-tagselect name="tags" .../>
config (BlueAdmin/Article.php):  public $belongsToMany = ['tags'];
FormRequest (ArticleRequest.php):  'tags' => ['nullable', 'array'], 'tags.*' => ['integer', 'exists:tags,id']
```

All three lines together — skip any one and the cycle breaks.

### 6. `php artisan make:controller` flags

Use the plain form (`php artisan make:controller Admin/XxxController --no-interaction`) — it generates an empty class extending `App\Http\Controllers\Controller`. Avoid `--invokable` (injects an unused `__invoke()` you then delete) and `--resource` (injects 7 stub methods that shadow the vendor `AdminController`'s real implementations — silent bug factory).

### 7. Run Pint after writing Request classes

The artisan stub includes a multi-line PHPDoc block on `authorize()` and `rules()` that doesn't match the project style. Run `vendor/bin/pint --dirty --format agent` on the new file after editing; Pint leaves the rules alone but strips excess docblocks and `use` imports you didn't end up using (e.g., the stub's `use Illuminate\Contracts\Validation\ValidationRule;`).

### 8. BlueAdmin config color must come from the project's palette

The `$color` property is rendered as `border-{color}-500` etc. via Tailwind classes. If you set `$color = 'green-mist'` but `green-mist` isn't a Tailwind color in this project, the top-border accent silently fails. Stick to the values already used in `app/BlueAdmin/*.php` (`sky`, `rose`, `blue`, `violet`) unless you've verified the palette has the new one.

## Testing

- Use `php artisan make:test Admin/XxxTest --phpunit` for feature tests
- Test CRUD access (index, create, store, show, edit, update, destroy) as authenticated admin user
- Test validation rules via FormRequest: assert invalid data returns errors, valid data redirects
- Test file upload paths if using Filepond
- Run `php artisan test --compact --filter=XxxTest` to verify

## Existing Patterns to Follow

Before creating a new resource, always check the existing resources in this project:
- **Controllers:** `app/Http/Controllers/Admin/` — all are empty classes extending `AdminController`
- **Configs:** `app/BlueAdmin/` — each sets `$CLASS`, `$name_to_use`, `$title_field`, optionally `$indexTableColumns`, `$filepond`, `$color`
- **Requests:** `app/Http/Requests/` — all return `authorize(): bool` as `true` and define `rules(): array`
- **Views:** `resources/views/admin/{modelname}/` — `_form.blade.php` uses `x-ba-*` components without extra wrapping
