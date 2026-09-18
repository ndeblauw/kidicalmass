<?php

namespace App\BlueAdmin;

use Ndeblauw\BlueAdmin\Models\BlueAdminModel;

class Group extends BlueAdminModel
{
    public $CLASS = \App\Models\Group::class;

    public $name_to_use = 'Chapters';

    public $title_field = 'name_nl';

    public $indexTableColumns = ['shortname', 'name_nl'];

    public $attributesToShow = ['shortname', 'name_nl', 'name_fr', 'zip', 'parent_id', 'invisible', 'started_at', 'ended_at'];

    public $filepond = ['main', 'gallery'];

    public $index_load = ['media'];

    public $show_load = ['media', 'parent'];
}
