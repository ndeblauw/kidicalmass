<?php

namespace App\BlueAdmin;

use Ndeblauw\BlueAdmin\Models\BlueAdminModel;

class Partner extends BlueAdminModel
{
    public $CLASS = \App\Models\Partner::class;

    public $name_to_use = 'Partners';

    public $title_field = 'name';

    public $indexTableColumns = ['name'];

    public $attributesToShow = ['name', 'url', 'description_nl', 'description_fr', 'group_id', 'show_logo', 'visible'];

    public $filepond = ['logo'];

    public $index_load = ['group', 'media'];

    public $show_load = ['group', 'media'];
}
