<?php

namespace App\BlueAdmin;

use Ndeblauw\BlueAdmin\Models\BlueAdminModel;

class Activity extends BlueAdminModel
{
    public $CLASS = \App\Models\Activity::class;

    public $name_to_use = 'Activities';

    public $title_field = 'title_nl';

    public $indexTableColumns = ['title_nl', 'title_fr', 'activity_type', 'begin_date', 'location', 'is_published'];

    public $attributesToShow = ['title_nl', 'title_fr', 'activity_type', 'begin_date', 'location', 'postal_code', 'distance', 'duration_minutes', 'commute_link', 'komoot_url', 'author_id', 'organizer_id', 'is_published'];

    public $filepond = ['main', 'gallery', 'gpx'];

    public $belongsToMany = ['groups'];

    public $index_load = ['author', 'organizer', 'groups', 'media'];

    public $show_load = ['author', 'organizer', 'groups', 'media'];
}
