<?php

namespace App\BlueAdmin;

use Ndeblauw\BlueAdmin\Models\BlueAdminModel;

class Partner extends BlueAdminModel
{
    public $CLASS = \App\Models\Partner::class;

    public $name_to_use = 'Partners';

    public $title_field = 'name_nl';

    public $indexTableColumns = ['name_nl', 'category', 'visible'];

    public $attributesToShow = ['name_nl', 'name_fr', 'url', 'description_nl', 'description_fr', 'category', 'group_id', 'show_logo', 'visible'];

    public $filepond = ['logo'];

    public $index_load = ['group', 'media'];

    public $show_load = ['group', 'media'];
}
