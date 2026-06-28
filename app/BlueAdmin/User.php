<?php

namespace App\BlueAdmin;

use Ndeblauw\BlueAdmin\Models\BlueAdminModel;

class User extends BlueAdminModel
{
    public $CLASS = \App\Models\User::class;

    public $name_to_use = 'Users';

    public $title_field = 'name';

    public $indexTableColumns = ['name', 'email'];

    public $attributesToShow = ['name', 'email', 'created_at'];

    public $belongsToMany = ['groups'];

    public $index_load = ['groups'];

    public $show_load = ['groups'];
}
