<?php

namespace App\BlueAdmin;

use Ndeblauw\BlueAdmin\Models\BlueAdminModel;

class TeamMember extends BlueAdminModel
{
    public $CLASS = \App\Models\TeamMember::class;

    public $name_to_use = 'Teamleden';

    public $title_field = 'name';

    public $indexTableColumns = ['name', 'role_nl', 'visible'];

    public $attributesToShow = ['name', 'role_nl', 'role_fr', 'bio_nl', 'bio_fr', 'sort', 'visible'];

    public $filepond = ['photo'];

    public $index_load = ['media'];

    public $show_load = ['media'];
}
